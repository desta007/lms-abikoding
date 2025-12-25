<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'invoice']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->byStatus($request->status);
        }

        // Filter by payment method
        if ($request->has('method') && $request->method) {
            $query->where('payment_method', $request->method);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest()->paginate(20);

        $stats = [
            'total_payments' => Payment::count(),
            'total_revenue' => Payment::completed()->sum('amount'),
            'pending_payments' => Payment::pending()->count(),
            'this_month_revenue' => Payment::completed()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function show($id)
    {
        $payment = Payment::with(['user', 'invoice.course', 'approver'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,cancelled',
        ]);

        $payment->update([
            'status' => $request->status,
        ]);

        if ($request->status === 'completed' && $payment->invoice) {
            $payment->invoice->markAsPaid();
            $this->createEnrollment($payment);
        }

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui');
    }

    public function approve($id)
    {
        $payment = Payment::with('invoice.course')->findOrFail($id);

        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pembayaran dengan status pending yang dapat di-approve');
        }

        $payment->update([
            'status' => 'completed',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'paid_at' => now(),
        ]);

        // Mark invoice as paid
        if ($payment->invoice) {
            $payment->invoice->markAsPaid();
        }

        // Create enrollment
        $this->createEnrollment($payment);

        Log::info('Payment approved', [
            'payment_id' => $payment->id,
            'approved_by' => Auth::id(),
            'invoice_id' => $payment->invoice_id,
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil di-approve. Siswa sekarang dapat mengakses kursus.');
    }

    public function reject(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pembayaran dengan status pending yang dapat di-reject');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ], [
            'admin_notes.required' => 'Catatan penolakan wajib diisi',
            'admin_notes.max' => 'Catatan maksimal 500 karakter',
        ]);

        $payment->update([
            'status' => 'failed',
            'admin_notes' => $request->admin_notes,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Log::info('Payment rejected', [
            'payment_id' => $payment->id,
            'rejected_by' => Auth::id(),
            'reason' => $request->admin_notes,
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil di-reject.');
    }

    protected function createEnrollment(Payment $payment)
    {
        if (!$payment->invoice || !$payment->invoice->course_id) {
            return;
        }

        try {
            $enrollment = CourseEnrollment::updateOrCreate([
                'course_id' => $payment->invoice->course_id,
                'user_id' => $payment->user_id,
            ], [
                'progress_percentage' => 0,
                'enrolled_at' => now(),
            ]);

            Log::info('Enrollment created from admin approval', [
                'enrollment_id' => $enrollment->id,
                'course_id' => $payment->invoice->course_id,
                'user_id' => $payment->user_id,
                'payment_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create enrollment from admin approval', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
            ]);
        }
    }

    public function invoices(Request $request)
    {
        $query = Invoice::with(['user', 'course', 'payments']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->latest()->paginate(20);

        return view('admin.payments.invoices', compact('invoices'));
    }

    public function generateInvoice(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:course_enrollment,subscription,admin_payment',
            'course_id' => 'nullable|exists:courses,id',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        $validated['total_amount'] = $validated['amount'] + ($validated['tax'] ?? 0);
        $validated['status'] = 'pending';

        $invoice = Invoice::create($validated);

        return redirect()->route('admin.payments.invoices.show', $invoice->id)
            ->with('success', 'Invoice berhasil dibuat');
    }
}
