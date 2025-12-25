<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.payments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Kembali</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Detail Pembayaran</h1>
                <p class="text-gray-600 mt-1">Tinjau detail transaksi dan verifikasi pembayaran.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

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
                            @if($payment->invoice->course)
                            <div class="md:col-span-2">
                                <dt class="text-gray-500">Kursus</dt>
                                <dd class="mt-1">
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        @if($payment->invoice->course->thumbnail)
                                            <img src="{{ asset('storage/' . $payment->invoice->course->thumbnail) }}" alt="{{ $payment->invoice->course->title }}" class="w-16 h-16 object-cover rounded-lg">
                                        @else
                                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $payment->invoice->course->title }}</p>
                                            <p class="text-sm text-gray-500">{{ $payment->invoice->course->subtitle }}</p>
                                        </div>
                                    </div>
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                @endif

                {{-- Payment Proof Section --}}
                @if($payment->payment_proof)
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Bukti Transfer</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti Transfer" class="max-w-full max-h-96 mx-auto rounded-lg border border-gray-200 shadow-sm cursor-pointer" onclick="window.open('{{ asset('storage/' . $payment->payment_proof) }}', '_blank')">
                            <p class="text-center text-sm text-gray-500 mt-2">Klik gambar untuk memperbesar</p>
                        </div>
                    </div>
                @endif

                {{-- Approval Info --}}
                @if($payment->approved_at)
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Verifikasi</h2>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-500">Diverifikasi Oleh</dt>
                                <dd class="mt-1 text-gray-900">{{ $payment->approver->full_name ?? $payment->approver->name ?? 'Admin' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Tanggal Verifikasi</dt>
                                <dd class="mt-1 text-gray-900">{{ $payment->approved_at->format('d M Y H:i') }}</dd>
                            </div>
                            @if($payment->admin_notes)
                            <div class="md:col-span-2">
                                <dt class="text-gray-500">Catatan Admin</dt>
                                <dd class="mt-1 text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $payment->admin_notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                @endif

                {{-- Approval Actions (only for pending payments) --}}
                @if($payment->status === 'pending')
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Verifikasi Pembayaran</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Approve Button (triggers modal) --}}
                            <button type="button" onclick="document.getElementById('approve-modal').classList.remove('hidden')" class="w-full px-6 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Approve Pembayaran
                            </button>

                            {{-- Reject Button (triggers modal) --}}
                            <button type="button" onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="w-full px-6 py-4 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject Pembayaran
                            </button>
                        </div>

                        <p class="text-sm text-gray-500 mt-4 text-center">
                            Pastikan Anda sudah memverifikasi bukti transfer sebelum meng-approve.
                        </p>
                    </div>
                @endif

                {{-- Manual Status Update (for non-pending) --}}
                @if($payment->status !== 'pending')
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
                @endif
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    <div id="approve-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Approve Pembayaran</h3>
                <p class="text-gray-600 mt-2 text-sm">Apakah Anda yakin ingin meng-approve pembayaran ini?</p>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-green-700">
                        <p class="font-medium">Yang akan terjadi:</p>
                        <ul class="mt-1 space-y-1">
                            <li>• Status pembayaran menjadi "Completed"</li>
                            <li>• Siswa otomatis terdaftar di kursus</li>
                            <li>• Siswa dapat mengakses materi kursus</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.payments.approve', $payment->id) }}">
                @csrf
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('approve-modal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Ya, Approve
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="reject-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Pembayaran</h3>
            <form method="POST" action="{{ route('admin.payments.reject', $payment->id) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="admin_notes">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea id="admin_notes" name="admin_notes" rows="3" required class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Bukti transfer tidak valid, nominal tidak sesuai, dll."></textarea>
                    @error('admin_notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
