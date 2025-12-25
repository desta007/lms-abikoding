<x-app-layout>
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">Menunggu Verifikasi</h1>
                <p class="text-white/90 text-lg">Pembayaran Anda sedang diproses oleh admin</p>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Sedang Diverifikasi</h2>
                    <p class="text-gray-600">Kami telah menerima bukti pembayaran Anda. Tim kami akan memverifikasi dalam waktu 1x24 jam kerja.</p>
                </div>

                <!-- Invoice Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Nomor Invoice</p>
                            <p class="font-semibold text-gray-900">{{ $invoice->invoice_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Total Pembayaran</p>
                            <p class="font-semibold text-indigo-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tanggal</p>
                            <p class="font-semibold text-gray-900">{{ now()->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Status</p>
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                Pending
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-100 mb-6">
                    @if($invoice->course && $invoice->course->thumbnail)
                        <img src="{{ asset('storage/' . $invoice->course->thumbnail) }}" alt="{{ $invoice->course->title }}" class="w-20 h-20 object-cover rounded-lg">
                    @else
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Kursus yang dibeli</p>
                        <h3 class="font-bold text-gray-900">{{ $invoice->course->title ?? 'Kursus' }}</h3>
                    </div>
                </div>

                @if($payment && $payment->payment_proof)
                <!-- Uploaded Proof -->
                <div class="mb-6">
                    <p class="text-sm font-medium text-gray-700 mb-2">Bukti Transfer yang Diunggah:</p>
                    <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti Transfer" class="w-full max-w-md mx-auto rounded-lg border border-gray-200 shadow-sm">
                </div>
                @endif

                <!-- What's Next -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Langkah Selanjutnya:</h3>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-gray-600">Bukti pembayaran berhasil diunggah</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-gray-600">Admin sedang memverifikasi pembayaran Anda</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-xs font-medium text-gray-500">3</span>
                            </div>
                            <p class="text-gray-600">Setelah diverifikasi, Anda akan mendapat akses ke kursus</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Alert -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-blue-800">Informasi</p>
                        <p class="text-sm text-blue-700 mt-1">
                            Anda akan menerima notifikasi email setelah pembayaran diverifikasi. 
                            Jika dalam 24 jam belum ada konfirmasi, silakan hubungi admin.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Kembali ke Beranda
                </a>
                <a href="{{ route('courses.show', $invoice->course->slug ?? '') }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Lihat Detail Kursus
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="fixed bottom-4 right-4 bg-blue-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
            {{ session('info') }}
        </div>
    @endif

    <script>
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.fixed.bottom-4').forEach(function(el) {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(function() { el.remove(); }, 500);
            });
        }, 5000);
    </script>
</x-app-layout>
