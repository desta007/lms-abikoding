<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\CourseEnrollment;
use App\Notifications\PaymentCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Log incoming webhook for debugging
        Log::info('Midtrans webhook received', [
            'order_id' => $request->get('order_id'),
            'transaction_status' => $request->get('transaction_status'),
            'all_data' => $request->all(),
        ]);

        try {
            // Initialize Midtrans Config
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $notification = new \Midtrans\Notification();

            $transaction = $notification->transaction_status;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            // Find invoice by invoice number
            $invoice = Invoice::with('course')->where('invoice_number', $orderId)->first();

            if (!$invoice) {
                Log::warning('Invoice not found for order', [
                    'order_id' => $orderId,
                    'transaction_status' => $transaction,
                ]);
                // Return 200 to acknowledge receipt, even if invoice not found
                return response()->json(['status' => 'acknowledged', 'message' => 'Invoice not found'], 200);
            }

            // Find payment
            $payment = Payment::where('invoice_id', $invoice->id)
                ->latest()
                ->first();

            if (!$payment) {
                Log::warning('Payment not found for invoice', [
                    'invoice_id' => $invoice->id,
                    'order_id' => $orderId,
                    'transaction_status' => $transaction,
                ]);
                // Return 200 to acknowledge receipt, even if payment not found
                return response()->json(['status' => 'acknowledged', 'message' => 'Payment not found'], 200);
            }

            // Process transaction status
            if ($transaction == 'capture') {
                if ($fraud == 'challenge') {
                    $payment->update(['status' => 'processing']);
                    Log::info('Payment marked as processing (fraud challenge)', [
                        'payment_id' => $payment->id,
                        'order_id' => $orderId,
                    ]);
                } else if ($fraud == 'accept') {
                    $this->completePayment($payment, $invoice, $notification);
                    Log::info('Payment completed (capture accepted)', [
                        'payment_id' => $payment->id,
                        'order_id' => $orderId,
                    ]);
                }
            } else if ($transaction == 'settlement') {
                $this->completePayment($payment, $invoice, $notification);
                Log::info('Payment completed (settlement)', [
                    'payment_id' => $payment->id,
                    'order_id' => $orderId,
                ]);
            } else if ($transaction == 'pending') {
                $payment->update(['status' => 'pending']);
                Log::info('Payment status updated to pending', [
                    'payment_id' => $payment->id,
                    'order_id' => $orderId,
                ]);
            } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                $payment->update(['status' => 'failed']);
                Log::info('Payment marked as failed', [
                    'payment_id' => $payment->id,
                    'order_id' => $orderId,
                    'transaction_status' => $transaction,
                ]);
            } else {
                Log::info('Unknown transaction status received', [
                    'payment_id' => $payment->id,
                    'order_id' => $orderId,
                    'transaction_status' => $transaction,
                ]);
            }

            // Always return 200 to acknowledge receipt
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Payment callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $request->get('order_id'),
                'request_data' => $request->all(),
            ]);
            // Return 200 to acknowledge receipt, even on error
            // This prevents Midtrans from retrying and sending error emails
            return response()->json(['status' => 'acknowledged', 'message' => 'Error processed'], 200);
        }
    }

    private function completePayment(Payment $payment, Invoice $invoice, $notification = null): void
    {
        // Get transaction ID from notification or request
        $transactionId = null;
        $gatewayResponse = json_encode(request()->all());
        
        if ($notification) {
            // Midtrans Notification object has transaction_id as a property
            $transactionId = $notification->transaction_id ?? null;
            // Get all notification data
            $gatewayResponse = json_encode([
                'transaction_id' => $notification->transaction_id ?? null,
                'transaction_status' => $notification->transaction_status ?? null,
                'order_id' => $notification->order_id ?? null,
                'payment_type' => $notification->payment_type ?? null,
                'fraud_status' => $notification->fraud_status ?? null,
                'gross_amount' => $notification->gross_amount ?? null,
                'currency' => $notification->currency ?? null,
                'settlement_time' => $notification->settlement_time ?? null,
            ]);
        } else {
            $transactionId = request()->get('transaction_id');
        }

        $payment->update([
            'status' => 'completed',
            'transaction_id' => $transactionId,
            'gateway_response' => $gatewayResponse,
            'paid_at' => now(),
        ]);

        $invoice->markAsPaid();

        // Send notification
        $invoice->user->notify(new PaymentCompletedNotification($invoice));

        // Create course enrollment if it's a course enrollment invoice
        if ($invoice->type === 'course_enrollment' && $invoice->course_id) {
            try {
                // Verify course exists
                if (!$invoice->course) {
                    Log::error('Course not found for enrollment', [
                        'course_id' => $invoice->course_id,
                        'invoice_id' => $invoice->id,
                    ]);
                    return;
                }

                // Use updateOrCreate to ensure enrollment exists and enrolled_at is always set
                $enrollment = CourseEnrollment::updateOrCreate([
                    'course_id' => $invoice->course_id,
                    'user_id' => $invoice->user_id,
                ], [
                    'progress_percentage' => 0,
                    'enrolled_at' => now(),
                ]);

                Log::info('Enrollment created/updated', [
                    'enrollment_id' => $enrollment->id,
                    'course_id' => $invoice->course_id,
                    'user_id' => $invoice->user_id,
                    'invoice_id' => $invoice->id,
                ]);

                // Send enrollment notification
                if ($invoice->course) {
                    $invoice->user->notify(new \App\Notifications\CourseEnrolledNotification($invoice->course));
                }
            } catch (\Exception $e) {
                Log::error('Failed to create enrollment after payment', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'course_id' => $invoice->course_id,
                    'user_id' => $invoice->user_id,
                    'invoice_id' => $invoice->id,
                ]);
            }
        }
    }
}
