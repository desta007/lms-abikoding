<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.students.show', $student->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Kembali</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Edit Siswa</h1>
                <p class="text-gray-600 mt-1">Perbarui informasi siswa dan setel ulang kata sandi bila diperlukan.</p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="first_name">Nama Depan</label>
                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $student->first_name ?? '') }}" required class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="last_name">Nama Belakang</label>
                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}" required class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $student->email) }}" required class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="whatsapp_number">Nomor WhatsApp</label>
                        <input id="whatsapp_number" type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $student->whatsapp_number) }}" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('whatsapp_number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password Baru</label>
                            <input id="password" type="password" name="password" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Opsional">
                            @error('password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="password_confirmation">Konfirmasi Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Opsional">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.students.show', $student->id) }}" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:border-gray-300 transition">
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

