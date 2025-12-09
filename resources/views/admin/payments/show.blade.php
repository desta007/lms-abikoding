<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.payments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Kembali</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Detail Pembayaran</h1>
                <p class="text-gray-600 mt-1">Tinjau detail transaksi dan ubah status pembayaran.</p>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Transaksi</p>
                            <h2 class="text-xl font-semibold text-gray-900 mt-1">{{ $payment->transaction_id ?? 'Manual Payment' }}</h2>
                            <p class="text-xs text-gray-400 mt-1">Invoice: {{ optional($payment->invoice)->invoice_number ?? 'N/A' }}</p>
                        </div>
                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full
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
                    </div>

                    <dl class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Jumlah</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Metode Pembayaran</dt>
                            <dd class="mt-1 text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'manual')) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Tanggal Dibuat</dt>
                            <dd class="mt-1 text-gray-900">{{ $payment->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Tanggal Pembayaran</dt>
                            <dd class="mt-1 text-gray-900">{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Data Pengguna</h2>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-400 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($payment->user->full_name ?? $payment->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900">{{ $payment->user->full_name ?? $payment->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $payment->user->email }}</p>
                            <p class="text-xs text-gray-400 mt-1">Bergabung {{ $payment->user->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                @if($payment->invoice)
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Invoice</h2>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-500">Nomor Invoice</dt>
                                <dd class="mt-1 text-gray-900">{{ $payment->invoice->invoice_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Status Invoice</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full
                                        {{ $payment->invoice->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($payment->invoice->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Jatuh Tempo</dt>
                                <dd class="mt-1 text-gray-900">{{ $payment->invoice->due_date ? $payment->invoice->due_date->format('d M Y') : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Total</dt>
                                <dd class="mt-1 text-gray-900">Rp {{ number_format($payment->invoice->total_amount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-gray-900">{{ $payment->invoice->description ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                @endif

                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Ubah Status Pembayaran</h2>
                    <form method="POST" action="{{ route('admin.payments.update-status', $payment->id) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="status">Status</label>
                            <select id="status" name="status" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="pending" @selected($payment->status === 'pending')>Pending</option>
                                <option value="processing" @selected($payment->status === 'processing')>Diproses</option>
                                <option value="completed" @selected($payment->status === 'completed')>Selesai</option>
                                <option value="failed" @selected($payment->status === 'failed')>Gagal</option>
                                <option value="cancelled" @selected($payment->status === 'cancelled')>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                                Perbarui Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

