<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Administrasi • Invoice</p>
                    <h1 class="text-3xl font-bold text-gray-900 mt-1">Daftar Invoice</h1>
                    <p class="text-gray-600 mt-2">Kelola invoice yang diterbitkan untuk siswa dan pantau statusnya.</p>
                </div>
                <div>
                    <button type="button" onclick="document.getElementById('generateInvoiceForm').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                        Buat Invoice Baru
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="sr-only" for="search">Cari invoice</label>
                            <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari nomor invoice atau email pengguna..." class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                        </div>
                        <div class="flex items-center gap-3 justify-end">
                            <select name="status" class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2">
                                <option value="">Semua Status</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                <option value="paid" @selected(request('status') === 'paid')>Sudah Dibayar</option>
                                <option value="overdue" @selected(request('status') === 'overdue')>Terlambat</option>
                                <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
                            </select>
                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                                Terapkan
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('admin.payments.invoices') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Kursus / Tipe</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $invoice->invoice_number }}</p>
                                        <p class="text-xs text-gray-500">Dibuat {{ $invoice->created_at->format('d M Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900">{{ $invoice->user->full_name ?? $invoice->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $invoice->user->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        @if($invoice->course)
                                            {{ $invoice->course->title }}
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $invoice->type)) }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 font-semibold">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full
                                            @class([
                                                'bg-yellow-100 text-yellow-700' => $invoice->status === 'pending',
                                                'bg-green-100 text-green-700' => $invoice->status === 'paid',
                                                'bg-red-100 text-red-600' => $invoice->status === 'overdue',
                                                'bg-gray-100 text-gray-600' => $invoice->status === 'cancelled',
                                            ])
                                        ">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}
                                        @if($invoice->due_date && $invoice->due_date->isPast() && $invoice->status !== 'paid')
                                            <span class="block text-xs text-red-500 font-medium mt-1">Lewat {{ $invoice->due_date->diffForHumans(null, true) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <p class="font-semibold text-gray-700">Belum ada invoice yang diterbitkan.</p>
                                        <p class="text-sm mt-1">Buat invoice pertama Anda dengan tombol di kanan atas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $invoices->withQueryString()->links() }}
                </div>
            </div>

            <div id="generateInvoiceForm" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center px-4">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Buat Invoice</h2>
                        <button type="button" onclick="document.getElementById('generateInvoiceForm').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('admin.payments.generate-invoice') }}" class="px-6 py-6 space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="user_id">Pengguna</label>
                            <select id="user_id" name="user_id" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @foreach(\App\Models\User::orderBy('name')->get(['id', 'name']) as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="type">Tipe Invoice</label>
                                <select id="type" name="type" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="course_enrollment">Pendaftaran Kursus</option>
                                    <option value="subscription">Langganan</option>
                                    <option value="admin_payment">Pembayaran Manual</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="course_id">Kursus (Opsional)</label>
                                <select id="course_id" name="course_id" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">- Pilih kursus -</option>
                                    @foreach(\App\Models\Course::orderBy('title')->get(['id', 'title']) as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="description">Deskripsi</label>
                            <textarea id="description" name="description" rows="3" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Tuliskan detail invoice..."></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="amount">Jumlah</label>
                                <input id="amount" name="amount" type="number" min="0" step="1000" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="tax">Pajak</label>
                                <input id="tax" name="tax" type="number" min="0" step="1000" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="due_date">Jatuh Tempo</label>
                                <input id="due_date" name="due_date" type="date" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" onclick="document.getElementById('generateInvoiceForm').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:border-gray-300 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                                Terbitkan Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

