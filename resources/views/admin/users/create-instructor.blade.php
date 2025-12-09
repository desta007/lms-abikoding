<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium mb-2 inline-block">← Kembali ke Daftar Pengguna</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-1">Buat Akun Instruktur Baru</h1>
                <p class="text-gray-600 mt-2">Buat akun instruktur baru untuk memberikan akses membuat dan mengelola kursus.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('admin.users.store-instructor') }}">
                    @csrf

                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Pribadi</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Depan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Belakang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="contoh@email.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="081234567890 atau +6281234567890">
                                <p class="mt-1 text-xs text-gray-500">Format: 081234567890, +6281234567890, atau 6281234567890</p>
                                @error('whatsapp_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Account Credentials -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Kredensial Akun</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" id="password" name="password" required
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Minimal 8 karakter">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>

                    <!-- Information Box -->
                    <div class="mb-8 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-semibold text-indigo-900 mb-1">Informasi Penting</h3>
                                <ul class="text-sm text-indigo-800 space-y-1 list-disc list-inside">
                                    <li>Akun instruktur akan langsung aktif setelah dibuat</li>
                                    <li>Email verifikasi otomatis dilakukan</li>
                                    <li>Email welcome akan dikirim ke instruktur baru</li>
                                    <li>Instruktur dapat langsung mengakses dashboard dan mulai membuat kursus</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                            Buat Akun Instruktur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

