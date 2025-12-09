<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        // Create invoice
        $invoice = Invoice::create([
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

        // Initialize Midtrans Snap
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Prepare customer name
        $user = Auth::user();
        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if (empty($fullName)) {
            $fullName = $user->name ?? 'Customer';
        }
        $nameParts = explode(' ', $fullName, 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        // Prepare phone number (required for some payment methods like Virtual Account and QRIS)
        $phone = $user->whatsapp_number ?? $user->phone ?? null;
        
        // Clean and format phone number
        if (!empty($phone)) {
            // Remove any non-numeric characters
            $phone = preg_replace('/[^0-9]/', '', $phone);
            
            // Format Indonesian phone number
            if (strlen($phone) > 0) {
                // If starts with 62, remove it and add 0
                if (substr($phone, 0, 2) === '62') {
                    $phone = '0' . substr($phone, 2);
                }
                // If doesn't start with 0, add it
                if (substr($phone, 0, 1) !== '0') {
                    $phone = '0' . $phone;
                }
            }
        }
        
        // Default phone for testing if empty (required by Midtrans)
        if (empty($phone) || strlen($phone) < 10) {
            $phone = '081234567890';
        }

        // Build callback URLs
        $baseUrl = config('app.url');
        $callbacks = [
            'finish' => route('payments.success', $invoice->id),
            'unfinish' => route('payments.failed', $invoice->id),
            'error' => route('payments.failed', $invoice->id),
        ];

        $params = [
            'transaction_details' => [
                'order_id' => $invoice->invoice_number,
                'gross_amount' => (float) $invoice->total_amount,
            ],
            'customer_details' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'phone' => $phone,
                'billing_address' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'country_code' => 'IDN',
                ],
                'shipping_address' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => [
                [
                    'id' => (string) $course->id,
                    'price' => (float) $invoice->total_amount,
                    'quantity' => 1,
                    'name' => substr($course->title, 0, 50), // Max 50 chars for Midtrans
                ],
            ],
            'callbacks' => $callbacks,
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit' => 'hour',
                'duration' => 24, // 24 hours expiry
            ],
            // Enable specific payment methods explicitly
            'enabled_payments' => [
                'credit_card',
                'mandiri_va',
                'bca_va',
                'bni_va',
                'permata_va',
                'other_va',
                'gopay',
                'qris',
                'shopeepay',
                'indomaret',
                'alfamart',
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Create payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'invoice_id' => $invoice->id,
                'payment_method' => 'midtrans',
                'amount' => $invoice->total_amount,
                'status' => 'pending',
            ]);

            return view('payments.checkout', compact('course', 'invoice', 'snapToken', 'payment'));
        } catch (\Exception $e) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
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
