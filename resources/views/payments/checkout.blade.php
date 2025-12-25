<x-app-layout>
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
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
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

                    <!-- Bank Transfer Information -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Informasi Rekening Tujuan</h2>
                        
                        <div class="space-y-4">
                            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 border border-indigo-100">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $bankAccount['bank_name'] ?? 'Bank Transfer' }}</p>
                                            <p class="text-sm text-gray-500">Transfer ke rekening berikut</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between py-2 border-b border-indigo-100">
                                            <span class="text-gray-600">Nomor Rekening</span>
                                            <span class="font-mono font-bold text-gray-900">{{ $bankAccount['bank_account_number'] ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-2 border-b border-indigo-100">
                                            <span class="text-gray-600">Atas Nama</span>
                                            <span class="font-semibold text-gray-900">{{ $bankAccount['bank_account_name'] ?? '-' }}</span>
                                        </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-gray-600">Jumlah Transfer</span>
                                        <span class="font-bold text-indigo-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-yellow-800">Penting!</p>
                                        <ul class="text-sm text-yellow-700 mt-1 space-y-1">
                                            <li>• Transfer tepat sesuai nominal di atas</li>
                                            <li>• Pembayaran akan diverifikasi dalam 1x24 jam</li>
                                            <li>• Simpan bukti transfer untuk diunggah</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary & Action -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Konfirmasi Pembayaran</h2>
                        
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

                        <!-- Manual Payment Form -->
                        <form action="{{ route('payments.manual', $invoice->id) }}" method="POST" enctype="multipart/form-data" id="payment-form">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Bukti Transfer <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="file" name="payment_proof" id="payment_proof" accept="image/jpeg,image/png,image/jpg" required
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-200 rounded-lg">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB</p>
                                @error('payment_proof')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Image Preview -->
                            <div id="image-preview" class="hidden mb-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                                <img id="preview-img" src="" alt="Preview" class="w-full h-40 object-cover rounded-lg border border-gray-200">
                            </div>

                            <button type="submit" id="submit-btn" class="w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <span id="submit-text">Konfirmasi Pembayaran</span>
                                <span id="submit-loading" class="hidden">
                                    <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                                    Mengunggah...
                                </span>
                            </button>
                        </form>

                        <div class="text-center mt-4">
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
                                <span>Pembayaran Aman & Terverifikasi</span>
                            </div>
                        </div>

                        {{-- Midtrans Payment Option (Hidden for now) --}}
                        {{-- 
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-500 text-center mb-3">Atau bayar dengan</p>
                            <button id="midtrans-btn" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-all">
                                Midtrans Payment
                            </button>
                        </div>
                        --}}
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('payment_proof');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const form = document.getElementById('payment-form');
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const submitLoading = document.getElementById('submit-loading');

            // Image preview
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('hidden');
                }
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                submitLoading.classList.remove('hidden');
            });

            // Auto-hide flash messages after 5 seconds
            setTimeout(function() {
                document.querySelectorAll('.fixed.bottom-4').forEach(function(el) {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(function() { el.remove(); }, 500);
                });
            }, 5000);
        });
    </script>
</x-app-layout>
