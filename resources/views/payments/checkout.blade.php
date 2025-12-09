<x-app-layout>
    @push('head')
    <!-- Content Security Policy for Midtrans - Must be set before Snap.js loads -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://snap-assets.al-pc-id-b.cdn.gtflabs.io https://snap-assets.al-pc-id-c.cdn.gtflabs.io https://snap-assets.al-pc-id-a.cdn.gtflabs.io https://app.sandbox.midtrans.com https://app.midtrans.com https://api.sandbox.midtrans.com https://api.midtrans.com https://pay.google.com https://js-agent.newrelic.com https://bam.nr-data.net https://gwk.gopayapi.com https://*.gopayapi.com https://*.midtrans.com https://*.veritrans.co.id https://*.gtflabs.io https://*.cloudfront.net https://*.mixpanel.com https://*.google-analytics.com https://code.jquery.com; frame-src 'self' 'unsafe-inline' https://*.midtrans.com https://*.veritrans.co.id https://*.gopayapi.com https://*.gtflabs.io https://*.cloudfront.net https://app.sandbox.midtrans.com https://app.midtrans.com; connect-src 'self' https://*.midtrans.com https://*.veritrans.co.id https://*.gopayapi.com https://*.gtflabs.io https://*.cloudfront.net https://api.sandbox.midtrans.com https://api.midtrans.com https://bam.nr-data.net https://*.mixpanel.com https://*.google-analytics.com; img-src 'self' data: https: blob:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://*.midtrans.com https://*.gtflabs.io https://*.cloudfront.net; font-src 'self' data: https://fonts.googleapis.com https://fonts.bunny.net https://*.cloudfront.net; object-src 'none'; base-uri 'self'; form-action 'self' https://*.midtrans.com;">
    @endpush
    
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Checkout Pembayaran</h1>
                <p class="text-white/90 text-lg">Selesaikan pembayaran untuk mengakses kursus</p>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Course Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Informasi Kursus</h2>
                        
                        <div class="flex gap-4 mb-6">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-32 h-32 object-cover rounded-lg">
                            @else
                                <div class="w-32 h-32 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-lg flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $course->title }}</h3>
                                <p class="text-gray-600 mb-3">{{ $course->subtitle }}</p>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <span>{{ $course->totalEnrollments() }} peserta</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ $course->instructor->full_name ?? $course->instructor->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Details -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Detail Invoice</h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-600">Nomor Invoice</span>
                                <span class="font-semibold text-gray-900">{{ $invoice->invoice_number }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-600">Deskripsi</span>
                                <span class="text-gray-900">{{ $invoice->description }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-600">Harga Kursus</span>
                                <span class="text-gray-900">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                            </div>
                            @if($invoice->tax > 0)
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-600">Pajak</span>
                                <span class="text-gray-900">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center py-4 border-t-2 border-gray-300">
                                <span class="text-lg font-bold text-gray-900">Total Pembayaran</span>
                                <span class="text-2xl font-bold text-indigo-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary & Action -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Pembayaran</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900 font-semibold">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                            </div>
                            @if($invoice->tax > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pajak</span>
                                <span class="text-gray-900 font-semibold">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-indigo-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Midtrans Payment Button -->
                        <div id="payment-widget-container" class="mb-4">
                            <button id="pay-button" class="w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="pay-button-text">Bayar Sekarang</span>
                                <span id="pay-button-loading" class="hidden">
                                    <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                                    Memproses...
                                </span>
                            </button>
                            <div id="payment-error" class="hidden mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"></div>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('courses.show', $course->slug) }}" class="text-sm text-gray-500 hover:text-gray-700">
                                ← Kembali ke Detail Kursus
                            </a>
                        </div>

                        <!-- Security Badge -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Pembayaran Aman & Terenkripsi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="fixed bottom-4 right-4 bg-blue-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
            {{ session('info') }}
        </div>
    @endif

    <!-- Midtrans Snap.js -->
    @php
        $midtransUrl = config('services.midtrans.is_production') 
            ? 'https://app.midtrans.com/snap/snap.js' 
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    <script src="{{ $midtransUrl }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    
    <script>
        $(document).ready(function() {
            var snapToken = '{{ $snapToken }}';
            var $payButton = $('#pay-button');
            var $payButtonText = $('#pay-button-text');
            var $payButtonLoading = $('#pay-button-loading');
            var $paymentError = $('#payment-error');
            var isProcessing = false;
            var snapReady = false;
            
            // Check if snap token exists
            if (!snapToken || snapToken === '') {
                $payButton.prop('disabled', true);
                $paymentError.removeClass('hidden').text('Gagal memuat token pembayaran. Silakan refresh halaman atau hubungi support.');
                return;
            }
            
            // Wait for Snap.js to load
            function waitForSnap(callback, maxAttempts) {
                maxAttempts = maxAttempts || 50; // 5 seconds max wait
                var attempts = 0;
                
                function checkSnap() {
                    attempts++;
                    if (typeof snap !== 'undefined' && typeof snap.pay === 'function') {
                        snapReady = true;
                        callback();
                    } else if (attempts < maxAttempts) {
                        setTimeout(checkSnap, 100);
                    } else {
                        $payButton.prop('disabled', false);
                        $paymentError.removeClass('hidden').text('Gagal memuat library pembayaran. Silakan refresh halaman.');
                        console.error('Snap.js failed to load');
                    }
                }
                
                checkSnap();
            }
            
            // Handle pay button click
            $payButton.on('click', function(e) {
                e.preventDefault();
                
                if (isProcessing) {
                    return;
                }
                
                // Show loading state
                isProcessing = true;
                $payButton.prop('disabled', true);
                $payButtonText.addClass('hidden');
                $payButtonLoading.removeClass('hidden');
                $paymentError.addClass('hidden');
                
                // Ensure Snap.js is loaded before proceeding
                if (!snapReady) {
                    waitForSnap(function() {
                        openPaymentPopup();
                    });
                } else {
                    openPaymentPopup();
                }
            });
            
            function openPaymentPopup() {
                // Wait a moment for UI update, then open Midtrans popup
                setTimeout(function() {
                    try {
                        // Verify snap is available
                        if (typeof snap === 'undefined' || typeof snap.pay !== 'function') {
                            throw new Error('Snap.js is not loaded');
                        }
                        
                        // Open Midtrans payment popup
                        snap.pay(snapToken, {
                            onSuccess: function(result) {
                                // Payment successful
                                console.log('Payment success:', result);
                                // Redirect to success page
                                window.location.href = '{{ route("payments.success", $invoice->id) }}';
                            },
                            onPending: function(result) {
                                // Payment pending (e.g., bank transfer, VA)
                                console.log('Payment pending:', result);
                                // Show message and redirect to pending/success page
                                alert('Pembayaran Anda sedang diproses. Silakan selesaikan pembayaran sesuai instruksi yang diberikan.');
                                window.location.href = '{{ route("payments.success", $invoice->id) }}?status=pending';
                            },
                            onError: function(result) {
                                // Payment error
                                console.error('Payment error:', result);
                                // Reset button state
                                resetButtonState();
                                // Show error message
                                $paymentError.removeClass('hidden').text('Pembayaran gagal. Silakan coba lagi atau gunakan metode pembayaran lain.');
                            },
                            onClose: function() {
                                // User closed the payment popup
                                console.log('Payment popup closed');
                                // Reset button state
                                resetButtonState();
                            }
                        });
                    } catch (error) {
                        console.error('Error opening payment popup:', error);
                        // Reset button state
                        resetButtonState();
                        // Show error
                        $paymentError.removeClass('hidden').text('Terjadi kesalahan saat membuka halaman pembayaran: ' + error.message + '. Silakan coba lagi atau refresh halaman.');
                    }
                }, 300);
            }
            
            function resetButtonState() {
                isProcessing = false;
                $payButton.prop('disabled', false);
                $payButtonText.removeClass('hidden');
                $payButtonLoading.addClass('hidden');
            }
            
            // Auto-hide flash messages after 5 seconds
            setTimeout(function() {
                $('.fixed.bottom-4').fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        });
    </script>
</x-app-layout>

