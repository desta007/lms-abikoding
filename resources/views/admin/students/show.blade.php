<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <a href="{{ route('admin.students.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Kembali</a>
                    <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $student->full_name ?? $student->name }}</h1>
                    <p class="text-gray-600 mt-1">Detail lengkap siswa dan statistik aktivitas mereka.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.students.edit', $student->id) }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:border-indigo-500 hover:text-indigo-600 transition">
                        Edit
                    </a>
                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Hapus siswa ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontak</h2>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-500">Email</dt>
                                <dd class="mt-1 text-gray-900">{{ $student->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Nomor WhatsApp</dt>
                                <dd class="mt-1 text-gray-900">{{ $student->whatsapp_number ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Status Verifikasi</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $student->email_verified_at ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $student->email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Tanggal Bergabung</dt>
                                <dd class="mt-1 text-gray-900">{{ $student->created_at->format('d M Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Aktivitas Kursus</h2>
                            <a href="{{ route('admin.students.enrollments', $student->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Lihat Semua →
                            </a>
                        </div>
                        <div class="space-y-4">
                            @forelse($student->enrollments->take(5) as $enrollment)
                                <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ $enrollment->course->title }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Bergabung {{ $enrollment->created_at->format('d M Y') }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $enrollment->completed_at ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $enrollment->completed_at ? 'Selesai' : 'Sedang Belajar' }}
                                            </span>
                                            <span class="text-xs text-gray-400">Instruktur: {{ $enrollment->course->instructor->full_name ?? $enrollment->course->instructor->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada kursus yang diikuti.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Statistik</h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500">Total Pendaftaran</dt>
                                <dd class="text-gray-900 font-medium">{{ number_format($statistics['total_enrollments']) }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500">Kursus Selesai</dt>
                                <dd class="text-gray-900 font-medium">{{ number_format($statistics['completed_courses']) }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500">Post Komunitas</dt>
                                <dd class="text-gray-900 font-medium">{{ number_format($statistics['total_posts']) }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500">Total Poin</dt>
                                <dd class="text-gray-900 font-medium">{{ number_format($statistics['total_points']) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Tindakan Lanjutan</h2>
                        <div class="space-y-3 text-sm">
                            <form method="POST" action="{{ route('admin.students.suspend', $student->id) }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 rounded-lg border border-amber-200 text-amber-600 font-medium hover:bg-amber-50 transition">
                                    Suspend Akun
                                </button>
                            </form>
                            <a href="mailto:{{ $student->email }}" class="block px-4 py-2 rounded-lg border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 text-indigo-600 font-medium transition">
                                Hubungi via Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

