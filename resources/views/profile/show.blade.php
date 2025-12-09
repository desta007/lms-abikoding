<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-sm text-gray-500 mb-2">Profil</p>
                <h1 class="text-3xl font-bold text-gray-900">Halo, {{ $user->full_name ?? $user->name }}</h1>
                <p class="text-gray-600 mt-2">Kelola informasi dan aktivitas Anda di platform.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-xl shadow p-6 border-l-4 border-indigo-500">
                    <p class="text-sm text-gray-500">Total Pendaftaran</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($statistics['total_enrollments']) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Kursus Selesai</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($statistics['completed_courses']) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Post Komunitas</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($statistics['total_posts']) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
                    <p class="text-sm text-gray-500">Poin</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($statistics['points']) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Informasi Dasar</h2>
                            <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit Profil</a>
                        </div>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-500">Nama Lengkap</dt>
                                <dd class="mt-1 text-gray-900">{{ $user->full_name ?? $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Email</dt>
                                <dd class="mt-1 text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Nomor WhatsApp</dt>
                                <dd class="mt-1 text-gray-900">{{ $user->whatsapp_number ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Lokasi</dt>
                                <dd class="mt-1 text-gray-900">{{ optional($user->profile)->location ?? '-' }}</dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-gray-500">Bio</dt>
                                <dd class="mt-1 text-gray-900 whitespace-pre-line">
                                    {{ optional($user->profile)->bio ?? 'Belum ada bio.' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Kursus Terbaru</h2>
                            <a href="{{ route('profile.enrollments') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                Lihat Semua →
                            </a>
                        </div>
                        <div class="space-y-4">
                            @forelse($user->enrollments->take(5) as $enrollment)
                                <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                                    @if($enrollment->course->thumbnail)
                                        <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" alt="{{ $enrollment->course->title }}" class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-lg"></div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ $enrollment->course->title }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ optional($enrollment->course->instructor)->full_name ?? optional($enrollment->course->instructor)->name }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                                {{ $enrollment->completed_at ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ $enrollment->completed_at ? 'Selesai' : 'Sedang Belajar' }}
                                            </span>
                                            <span class="text-xs text-gray-400">{{ $enrollment->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm text-center py-8">Belum ada kursus yang diikuti.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Kartu Anggota</h2>
                        <div class="rounded-lg border border-gray-200 p-4 bg-gradient-to-br from-indigo-500/5 to-purple-500/5">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-indigo-500 text-white rounded-full flex items-center justify-center text-lg font-semibold">
                                    {{ strtoupper(substr($user->full_name ?? $user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $user->full_name ?? $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($user->role) }}</p>
                                </div>
                            </div>
                            <hr class="my-4 border-gray-200">
                            <div class="text-xs text-gray-500 space-y-2">
                                <div class="flex justify-between">
                                    <span>Bergabung</span>
                                    <span>{{ $user->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Sertifikat</span>
                                    <span>{{ number_format($statistics['total_certificates']) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Points</span>
                                    <span>{{ number_format($statistics['points']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Tautan Cepat</h2>
                        <div class="space-y-3">
                            <a href="{{ route('profile.edit') }}" class="block w-full text-left px-4 py-3 rounded-lg border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 text-sm font-medium text-indigo-600 transition">
                                Edit Profil
                            </a>
                            <a href="{{ route('profile.enrollments') }}" class="block w-full text-left px-4 py-3 rounded-lg border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 text-sm font-medium text-indigo-600 transition">
                                Riwayat Kursus
                            </a>
                            <a href="{{ route('profile.certificates') }}" class="block w-full text-left px-4 py-3 rounded-lg border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 text-sm font-medium text-indigo-600 transition">
                                Sertifikat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

