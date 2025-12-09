<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900">Verifikasi Sertifikat</h1>
                <p class="text-gray-600 mt-2">Verifikasi keaslian sertifikat dengan kode verifikasi</p>
            </div>

            @if(isset($certificate))
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8">
                        @if(isset($isValid) && $isValid)
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-lg font-semibold text-green-900">Sertifikat Valid</h3>
                                        <p class="text-sm text-green-700">Sertifikat ini telah diverifikasi dan valid.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-lg font-semibold text-red-900">Sertifikat Tidak Valid</h3>
                                        <p class="text-sm text-red-700">Sertifikat ini telah dicabut atau tidak valid.</p>
                                        @if($certificate->revoked_reason)
                                            <p class="text-sm text-red-600 mt-1">Alasan: {{ $certificate->revoked_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Sertifikat</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Nomor Sertifikat</dt>
                                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $certificate->certificate_number }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Kode Verifikasi</dt>
                                        <dd class="mt-1 text-sm text-gray-900 font-mono break-all">{{ $certificate->verification_code }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Diterbitkan</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->issued_at->format('d F Y') }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kursus</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Kursus</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->course->title }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Peserta</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->user->full_name }}</dd>
                                    </div>
                                    @if($certificate->course->instructor)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Instruktur</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->course->instructor->full_name }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('certificates.verify.form') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                ← Verifikasi sertifikat lain
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Sertifikat tidak ditemukan</h3>
                        <p class="mt-2 text-sm text-gray-500">Kode verifikasi yang Anda masukkan tidak valid.</p>
                        <div class="mt-6">
                            <a href="{{ route('certificates.verify.form') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Coba lagi
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

