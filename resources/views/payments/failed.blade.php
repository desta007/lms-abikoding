<x-app-layout>
    <div class="bg-gradient-to-r from-red-600 via-rose-600 to-pink-600 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-white mb-4">Pembayaran Gagal</h1>
            <p class="text-white/90 text-lg">Maaf, pembayaran Anda tidak dapat diproses</p>
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
                        <span class="text-xl font-bold text-gray-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600">Status</span>
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                            Gagal
                        </span>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-red-900 mb-1">Pembayaran Tidak Berhasil</h3>
                            <p class="text-sm text-red-800 mb-3">Pembayaran Anda tidak dapat diproses. Kemungkinan penyebab:</p>
                            <ul class="text-sm text-red-800 list-disc list-inside space-y-1">
                                <li>Saldo tidak mencukupi</li>
                                <li>Kartu kredit/debit ditolak</li>
                                <li>Transaksi dibatalkan</li>
                                <li>Masalah teknis dengan gateway pembayaran</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('payments.checkout', $invoice->course_id) }}" class="flex-1 px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:scale-105 text-center">
                        Coba Lagi
                    </a>
                    <a href="{{ route('courses.show', $invoice->course->slug) }}" class="flex-1 px-6 py-4 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold text-lg transition-all text-center">
                        Kembali ke Kursus
                    </a>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        Butuh bantuan? <a href="mailto:support@example.com" class="text-indigo-600 hover:text-indigo-800 font-semibold">Hubungi Support</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

