<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium mb-2 inline-block">← Kembali ke Daftar Pengguna</a>
                    <h1 class="text-3xl font-bold text-gray-900 mt-1">Detail Pengguna</h1>
                    <p class="text-gray-600 mt-2">{{ $user->full_name ?? $user->name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                        Edit Pengguna
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Pengguna</h2>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-purple-400 flex items-center justify-center text-white font-bold text-xl">
                                    {{ strtoupper(substr($user->full_name ?? $user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $user->full_name ?? $user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nama Depan</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->first_name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nama Belakang</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->last_name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">WhatsApp</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->whatsapp_number ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Terdaftar</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Peran Pengguna</h2>
                            <button onclick="document.getElementById('changeRoleModal').classList.remove('hidden')" class="px-4 py-2 rounded-lg border border-indigo-600 text-indigo-600 text-sm font-medium hover:bg-indigo-50 transition">
                                Ubah Peran
                            </button>
                        </div>
                        <div>
                            @if($user->role === 'admin')
                                <span class="inline-flex px-4 py-2 text-sm font-medium rounded-lg bg-red-100 text-red-700">
                                    Admin
                                </span>
                            @elseif($user->role === 'instructor')
                                <span class="inline-flex px-4 py-2 text-sm font-medium rounded-lg bg-purple-100 text-purple-700">
                                    Instruktur
                                </span>
                            @else
                                <span class="inline-flex px-4 py-2 text-sm font-medium rounded-lg bg-blue-100 text-blue-700">
                                    Siswa
                                </span>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">
                                @if($user->role === 'admin')
                                    Pengguna ini memiliki akses penuh ke semua fitur administrasi.
                                @elseif($user->role === 'instructor')
                                    Pengguna ini dapat membuat dan mengelola kursus.
                                @else
                                    Pengguna ini dapat mengikuti kursus dan berpartisipasi dalam komunitas.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Statistik</h2>
                        <div class="space-y-4">
                            @if($user->role === 'student')
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Total Pendaftaran</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $statistics['total_enrollments'] ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Kursus Selesai</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $statistics['completed_courses'] ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Total Post</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $statistics['total_posts'] ?? 0 }}</span>
                                </div>
                            @elseif($user->role === 'instructor')
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Total Kursus</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $statistics['total_courses'] ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Kursus Terbit</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $statistics['published_courses'] ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Total Siswa</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $statistics['total_students'] ?? 0 }}</span>
                                </div>
                            @else
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Total Pengguna</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $statistics['total_users'] ?? 0 }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Account Status -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Status Akun</h2>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Email Terverifikasi</span>
                                @if($user->email_verified_at)
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        Ya
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                        Belum
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Phone Terverifikasi</span>
                                @if($user->phone_verified_at)
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        Ya
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                        Belum
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Role Modal -->
    <div id="changeRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Ubah Peran Pengguna</h3>
                <form method="POST" action="{{ route('admin.users.update-role', $user->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Peran Baru</label>
                        <select id="role" name="role" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Siswa</option>
                            <option value="instructor" {{ $user->role === 'instructor' ? 'selected' : '' }}>Instruktur</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-yellow-800">
                            <strong>Peringatan:</strong> Mengubah peran pengguna akan mempengaruhi akses mereka ke fitur sistem.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="document.getElementById('changeRoleModal').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

