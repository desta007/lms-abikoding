<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Administrasi • Pembayaran</p>
                    <h1 class="text-3xl font-bold text-gray-900 mt-1">Manajemen Pembayaran</h1>
                    <p class="text-gray-600 mt-2">Pantau transaksi masuk, status pembayaran, dan pendapatan platform.</p>
                </div>
                <div class="grid grid-cols-2 gap-3 md:w-auto w-full">
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-indigo-500">
                        <p class="text-xs text-gray-500 uppercase">Total Pembayaran</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ number_format($stats['total_payments']) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                        <p class="text-xs text-gray-500 uppercase">Pendapatan Bulan Ini</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">Rp {{ number_format($stats['this_month_revenue'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="sr-only" for="search">Cari pembayaran</label>
                            <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari transaksi atau email pengguna..." class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                        </div>
                        <div>
                            <label class="sr-only" for="status">Status</label>
                            <select id="status" name="status" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2">
                                <option value="">Semua Status</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                <option value="processing" @selected(request('status') === 'processing')>Diproses</option>
                                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                                <option value="failed" @selected(request('status') === 'failed')>Gagal</option>
                                <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
                            </select>
                        </div>
                        <div>
                            <label class="sr-only" for="method">Metode</label>
                            <select id="method" name="method" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2">
                                <option value="">Semua Metode</option>
                                <option value="bank_transfer" @selected(request('method') === 'bank_transfer')>Transfer Bank</option>
                                <option value="credit_card" @selected(request('method') === 'credit_card')>Kartu Kredit</option>
                                <option value="ewallet" @selected(request('method') === 'ewallet')>E-Wallet</option>
                                <option value="midtrans" @selected(request('method') === 'midtrans')>Midtrans</option>
                            </select>
                        </div>
                        <div class="md:col-span-4 flex items-center gap-3 justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                                Terapkan
                            </button>
                            @if(request()->hasAny(['search', 'status', 'method']))
                                <a href="{{ route('admin.payments.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $payment->transaction_id ?? 'Manual' }}</p>
                                        <p class="text-xs text-gray-500">Invoice #{{ optional($payment->invoice)->invoice_number ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900">{{ $payment->user->full_name ?? $payment->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $payment->user->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 font-semibold">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'manual')) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full
                                            @class([
                                                'bg-yellow-100 text-yellow-700' => $payment->status === 'pending',
                                                'bg-blue-100 text-blue-700' => $payment->status === 'processing',
                                                'bg-green-100 text-green-700' => $payment->status === 'completed',
                                                'bg-red-100 text-red-600' => $payment->status === 'failed',
                                                'bg-gray-100 text-gray-600' => $payment->status === 'cancelled',
                                            ])
                                        ">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $payment->created_at->format('d M Y') }}
                                        <span class="block text-xs text-gray-400">{{ $payment->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-600 hover:border-indigo-500 hover:text-indigo-600">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <p class="font-semibold text-gray-700">Belum ada transaksi pembayaran.</p>
                                        <p class="text-sm mt-1">Transaksi baru akan muncul di sini setelah siswa melakukan pembayaran.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $payments->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

