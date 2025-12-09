<x-app-layout>
    <div class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-white mb-4">Pembayaran Berhasil!</h1>
            <p class="text-white/90 text-lg">Terima kasih atas pembayaran Anda</p>
        </div>
    </div>

    <div class="bg-gray-50 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Detail Pembayaran</h2>
                    <p class="text-gray-600">Invoice: <span class="font-semibold">{{ $invoice->invoice_number }}</span></p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600">Kursus</span>
                        <span class="font-semibold text-gray-900">{{ $invoice->course->title }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600">Jumlah Pembayaran</span>
                        <span class="text-xl font-bold text-green-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600">Status</span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            @if(request('status') === 'pending')
                                Menunggu Konfirmasi
                            @else
                                Berhasil
                            @endif
                        </span>
                    </div>
                    @if($invoice->paid_at)
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600">Tanggal Pembayaran</span>
                        <span class="text-gray-900">{{ $invoice->paid_at->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                </div>

                @if(request('status') === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-yellow-900 mb-1">Pembayaran Sedang Diproses</h3>
                                <p class="text-sm text-yellow-800">Silakan selesaikan pembayaran sesuai instruksi yang diberikan. Kami akan mengirimkan notifikasi email setelah pembayaran dikonfirmasi.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-green-900 mb-1">Pembayaran Berhasil Dikonfirmasi</h3>
                                <p class="text-sm text-green-800">Anda sekarang dapat mengakses semua materi kursus. Email konfirmasi telah dikirim ke {{ Auth::user()->email }}.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('courses.content', $invoice->course_id) }}" class="flex-1 px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:scale-105 text-center">
                        Mulai Belajar Sekarang
                    </a>
                    <a href="{{ route('courses.show', $invoice->course->slug) }}" class="flex-1 px-6 py-4 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold text-lg transition-all text-center">
                        Kembali ke Kursus
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

