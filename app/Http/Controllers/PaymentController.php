<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function checkout(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check if already enrolled
        $existingEnrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Anda sudah terdaftar dalam kursus ini');
        }

        // Check if course is free
        if ($course->isFree()) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Kursus ini gratis, silakan gunakan tombol Daftar');
        }

        // Check for existing pending invoice
        $existingInvoice = Invoice::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvoice) {
            // Check if there's already a pending payment
            $existingPayment = Payment::where('invoice_id', $existingInvoice->id)
                ->where('status', 'pending')
                ->first();

            if ($existingPayment) {
                return redirect()->route('payments.pending', $existingInvoice->id)
                    ->with('info', 'Anda sudah memiliki pembayaran yang menunggu verifikasi');
            }
        }

        // Create invoice if not exists
        $invoice = $existingInvoice ?? Invoice::create([
            'user_id' => Auth::id(),
            'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
            'type' => 'course_enrollment',
            'course_id' => $course->id,
            'description' => "Pembayaran untuk kursus: {$course->title}",
            'amount' => $course->price,
            'tax' => 0,
            'total_amount' => $course->price,
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ]);

        // Get bank account settings
        $bankAccount = \App\Models\Setting::getBankAccount();

        return view('payments.checkout', compact('course', 'invoice', 'bankAccount'));
    }

    public function processManualPayment(Request $request, $invoiceId)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'payment_proof.required' => 'Bukti transfer wajib diunggah',
            'payment_proof.image' => 'File harus berupa gambar',
            'payment_proof.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'payment_proof.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $invoice = Invoice::where('user_id', Auth::id())
            ->where('id', $invoiceId)
            ->where('status', 'pending')
            ->firstOrFail();

        // Store payment proof
        $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Create payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'invoice_id' => $invoice->id,
            'payment_method' => 'manual',
            'amount' => $invoice->total_amount,
            'status' => 'pending',
            'payment_proof' => $paymentProofPath,
        ]);

        Log::info('Manual payment created', [
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('payments.pending', $invoice->id)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Silakan tunggu verifikasi dari admin.');
    }

    public function pending($invoiceId)
    {
        $invoice = Invoice::where('user_id', Auth::id())
            ->with('course')
            ->findOrFail($invoiceId);

        $payment = Payment::where('invoice_id', $invoice->id)
            ->latest()
            ->first();

        return view('payments.pending', compact('invoice', 'payment'));
    }

    public function process(Request $request, $invoiceId)
    {
        // This method is kept for backward compatibility but not used in simplified flow
        // The checkout method now directly generates the Midtrans token
        $invoice = Invoice::where('user_id', Auth::id())
            ->findOrFail($invoiceId);

        return redirect()->route('payments.checkout', $invoice->course_id);
    }

    public function success($invoiceId)
    {
        $invoice = Invoice::where('user_id', Auth::id())
            ->with('course')
            ->findOrFail($invoiceId);

        // Get the payment for this invoice
        $payment = Payment::where('invoice_id', $invoice->id)
            ->latest()
            ->first();

        if (!$payment) {
            return redirect()->route('courses.show', $invoice->course->slug)
                ->with('error', 'Payment record not found');
        }

        // For manual payments, just show success page
        if ($payment->payment_method === 'manual') {
            // Refresh payment and invoice from database
            $payment->refresh();
            $invoice->refresh();

            return view('payments.success', compact('invoice'));
        }

        // Verify payment status directly from Midtrans API
        // This ensures we get the latest status even if webhook hasn't fired
        try {
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $status = \Midtrans\Transaction::status($invoice->invoice_number);
            $transactionStatus = $status->transaction_status ?? null;
            $fraudStatus = $status->fraud_status ?? null;

            // Update payment status based on Midtrans response
            if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                if ($fraudStatus === 'accept' || $transactionStatus === 'settlement') {
                    // Payment is successful
                    if ($payment->status !== 'completed') {
                        $payment->update([
                            'status' => 'completed',
                            'transaction_id' => $status->transaction_id ?? null,
                            'gateway_response' => json_encode($status),
                            'paid_at' => now(),
                        ]);

                        // Mark invoice as paid
                        if ($invoice->status !== 'paid') {
                            $invoice->markAsPaid();
                        }
                    }
                } else if ($fraudStatus === 'challenge') {
                    $payment->update(['status' => 'processing']);
                }
            } else if ($transactionStatus === 'pending') {
                $payment->update(['status' => 'pending']);
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $payment->update(['status' => 'failed']);
            }
        } catch (\Exception $e) {
            Log::error('Failed to verify payment status from Midtrans', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ]);
        }

        // Refresh payment and invoice from database
        $payment->refresh();
        $invoice->refresh();

        // Create course enrollment if payment is completed or invoice is paid
        if ($invoice->type === 'course_enrollment' && $invoice->course_id) {
            $shouldEnroll = false;
            
            // Check if payment is completed
            if ($payment->status === 'completed') {
                $shouldEnroll = true;
            }
            
            // Check if invoice is marked as paid
            if ($invoice->status === 'paid') {
                $shouldEnroll = true;
            }
            
            // If either condition is met, create/update enrollment
            if ($shouldEnroll) {
                try {
                    $enrollment = CourseEnrollment::updateOrCreate([
                        'course_id' => $invoice->course_id,
                        'user_id' => $invoice->user_id,
                    ], [
                        'progress_percentage' => 0,
                        'enrolled_at' => now(),
                    ]);

                    Log::info('Enrollment created/updated from success page', [
                        'enrollment_id' => $enrollment->id,
                        'course_id' => $invoice->course_id,
                        'user_id' => $invoice->user_id,
                        'invoice_id' => $invoice->id,
                        'payment_status' => $payment->status,
                        'invoice_status' => $invoice->status,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create enrollment in success page', [
                        'error' => $e->getMessage(),
                        'course_id' => $invoice->course_id,
                        'user_id' => $invoice->user_id,
                        'invoice_id' => $invoice->id,
                    ]);
                }
            }
        }

        return view('payments.success', compact('invoice'));
    }

    public function failed($invoiceId)
    {
        $invoice = Invoice::where('user_id', Auth::id())
            ->findOrFail($invoiceId);

        return view('payments.failed', compact('invoice'));
    }
}
