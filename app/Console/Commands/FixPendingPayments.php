<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\CourseEnrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:fix-pending {--limit=50 : Maximum number of payments to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify and fix pending payments by checking status from Midtrans API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        
        // Initialize Midtrans Config
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Get pending payments with invoices
        $payments = Payment::where('status', 'pending')
            ->with(['invoice.course'])
            ->limit($limit)
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No pending payments found.');
            return 0;
        }

        $this->info("Checking {$payments->count()} pending payment(s)...");
        $this->newLine();

        $fixed = 0;
        $failed = 0;

        foreach ($payments as $payment) {
            $invoice = $payment->invoice;
            
            if (!$invoice) {
                $this->warn("Payment #{$payment->id} has no invoice. Skipping.");
                continue;
            }

            try {
                // Check payment status from Midtrans
                $status = \Midtrans\Transaction::status($invoice->invoice_number);
                $transactionStatus = $status->transaction_status ?? null;
                $fraudStatus = $status->fraud_status ?? null;

                $this->line("Checking Payment #{$payment->id} (Invoice: {$invoice->invoice_number})...");

                if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                    if ($fraudStatus === 'accept' || $transactionStatus === 'settlement') {
                        // Payment is successful - update it
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

                        // Create enrollment if it's a course enrollment
                        if ($invoice->type === 'course_enrollment' && $invoice->course_id) {
                            $enrollment = CourseEnrollment::updateOrCreate([
                                'course_id' => $invoice->course_id,
                                'user_id' => $invoice->user_id,
                            ], [
                                'progress_percentage' => 0,
                                'enrolled_at' => $invoice->paid_at ?? now(),
                            ]);

                            $this->info("  ✓ Payment completed. Enrollment created/updated (ID: {$enrollment->id})");
                        } else {
                            $this->info("  ✓ Payment completed.");
                        }

                        $fixed++;
                    } else if ($fraudStatus === 'challenge') {
                        $payment->update(['status' => 'processing']);
                        $this->info("  → Payment is under challenge review.");
                    }
                } else if ($transactionStatus === 'pending') {
                    $this->line("  → Payment is still pending.");
                } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $payment->update(['status' => 'failed']);
                    $this->warn("  ✗ Payment failed: {$transactionStatus}");
                } else {
                    $this->line("  → Status: {$transactionStatus}");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("  ✗ Error checking payment #{$payment->id}: " . $e->getMessage());
                Log::error('Failed to check payment status', [
                    'payment_id' => $payment->id,
                    'invoice_number' => $invoice->invoice_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  Fixed: {$fixed}");
        $this->info("  Failed: {$failed}");
        $this->info("  Total checked: {$payments->count()}");

        return 0;
    }
}
