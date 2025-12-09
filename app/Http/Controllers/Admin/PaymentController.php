<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

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
        $payment = Payment::with(['user', 'invoice.course'])->findOrFail($id);
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
        }

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui');
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
