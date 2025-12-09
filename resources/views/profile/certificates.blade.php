<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('profile.show') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Kembali ke Profil</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Sertifikat Saya</h1>
                <p class="text-gray-600 mt-2">Kelola dan unduh sertifikat dari kursus yang telah diselesaikan.</p>
            </div>

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                    <p class="text-sm text-gray-500">Total Sertifikat</p>
                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">
                        {{ $certificates->count() }}
                    </span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($certificates as $certificate)
                        <div class="px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-semibold">
                                    <span>{{ strtoupper(substr($certificate->course->title, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-gray-900">{{ $certificate->course->title }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Diterbitkan pada {{ $certificate->issued_at ? $certificate->issued_at->format('d M Y') : $certificate->created_at->format('d M Y') }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">Nomor Sertifikat: {{ $certificate->certificate_number }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('certificates.show', $certificate->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:border-indigo-500 hover:text-indigo-600 transition">
                                    Lihat
                                </a>
                                <a href="{{ route('certificates.download', $certificate->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                                    Unduh PDF
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center text-gray-500">
                            <p class="text-lg font-semibold mb-2">Belum ada sertifikat</p>
                            <p class="text-sm">Selesaikan kursus untuk mendapatkan sertifikat pertama Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

