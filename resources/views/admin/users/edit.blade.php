<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.users.show', $user->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium mb-2 inline-block">← Kembali ke Detail</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-1">Edit Pengguna</h1>
                <p class="text-gray-600 mt-2">Ubah informasi pengguna dan peran mereka di sistem.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Pribadi</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Depan</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Belakang</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp</label>
                                <input type="text" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $user->whatsapp_number) }}"
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('whatsapp_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Role Assignment -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Peran Pengguna</h2>
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                            <select id="role" name="role" required
                                    class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Siswa</option>
                                <option value="instructor" {{ old('role', $user->role) === 'instructor' ? 'selected' : '' }}>Instruktur</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-sm text-yellow-800">
                                    <strong>Peringatan:</strong> Mengubah peran pengguna akan mempengaruhi akses mereka ke fitur sistem. 
                                    @if($user->role === 'admin')
                                        <br><strong>Catatan:</strong> Tidak dapat menghapus peran admin jika ini adalah satu-satunya admin.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Password Reset (Optional) -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Reset Password (Opsional)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                <input type="password" id="password" name="password"
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Kosongkan jika tidak ingin mengubah">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Konfirmasi password baru">
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Biarkan kosong jika tidak ingin mengubah password.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

