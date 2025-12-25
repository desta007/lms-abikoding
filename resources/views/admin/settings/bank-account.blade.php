<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Kembali ke Dashboard</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Pengaturan Rekening Bank</h1>
                <p class="text-gray-600 mt-1">Kelola informasi rekening bank untuk pembayaran manual transfer.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Informasi Rekening</h2>
                            <p class="text-sm text-gray-500">Data ini akan ditampilkan di halaman checkout pembayaran</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.settings.bank-account.update') }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Bank <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                            name="bank_name" 
                            id="bank_name" 
                            value="{{ old('bank_name', $settings['bank_name']->value ?? '') }}"
                            placeholder="Contoh: Bank BCA, Bank Mandiri, Bank BNI"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-gray-900"
                            required>
                        @error('bank_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bank_account_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Rekening <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                            name="bank_account_number" 
                            id="bank_account_number" 
                            value="{{ old('bank_account_number', $settings['bank_account_number']->value ?? '') }}"
                            placeholder="Contoh: 1234567890"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 font-mono"
                            required>
                        @error('bank_account_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bank_account_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Pemilik Rekening <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                            name="bank_account_name" 
                            id="bank_account_name" 
                            value="{{ old('bank_account_name', $settings['bank_account_name']->value ?? '') }}"
                            placeholder="Contoh: PT ABC Indonesia"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-gray-900"
                            required>
                        @error('bank_account_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preview Section -->
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Preview Tampilan di Checkout:</h3>
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 border border-indigo-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p id="preview-bank-name" class="font-bold text-gray-900">{{ $settings['bank_name']->value ?? 'Nama Bank' }}</p>
                                    <p class="text-sm text-gray-500">Transfer ke rekening berikut</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between py-2 border-b border-indigo-100">
                                    <span class="text-gray-600">Nomor Rekening</span>
                                    <span id="preview-account-number" class="font-mono font-bold text-gray-900">{{ $settings['bank_account_number']->value ?? '0000000000' }}</span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-600">Atas Nama</span>
                                    <span id="preview-account-name" class="font-semibold text-gray-900">{{ $settings['bank_account_name']->value ?? 'Nama Pemilik' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-medium transition shadow-lg hover:shadow-xl">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Pengaturan
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-blue-800">Informasi</p>
                        <p class="text-sm text-blue-700 mt-1">
                            Informasi rekening ini akan ditampilkan kepada siswa saat melakukan pembayaran manual transfer. 
                            Pastikan data yang diisi benar dan aktif untuk menerima pembayaran.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Live preview
        document.getElementById('bank_name').addEventListener('input', function() {
            document.getElementById('preview-bank-name').textContent = this.value || 'Nama Bank';
        });
        document.getElementById('bank_account_number').addEventListener('input', function() {
            document.getElementById('preview-account-number').textContent = this.value || '0000000000';
        });
        document.getElementById('bank_account_name').addEventListener('input', function() {
            document.getElementById('preview-account-name').textContent = this.value || 'Nama Pemilik';
        });
    </script>
</x-app-layout>
