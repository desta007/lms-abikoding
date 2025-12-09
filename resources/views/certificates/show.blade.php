<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('certificates.history') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Kembali ke Daftar Sertifikat</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Detail Sertifikat</h1>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col lg:flex-row gap-8">
                        <!-- Certificate Preview -->
                        <div class="lg:w-2/3">
                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg p-8 border-4 border-indigo-200">
                                <div class="text-center">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-2">SERTIFIKAT PENYELESAIAN</h2>
                                    <h3 class="text-lg text-gray-600 mb-6">CERTIFICATE OF COMPLETION</h3>
                                    
                                    <p class="text-gray-700 mb-4">Dengan ini menyatakan bahwa</p>
                                    <p class="text-gray-600 italic mb-6">This certifies that</p>
                                    
                                    <h4 class="text-3xl font-bold text-indigo-900 mb-6">{{ $certificate->user->full_name }}</h4>
                                    
                                    <p class="text-gray-700 mb-4">telah menyelesaikan kursus</p>
                                    <p class="text-gray-600 italic mb-6">has successfully completed the course</p>
                                    
                                    <h5 class="text-2xl font-semibold text-gray-800 mb-6 italic">"{{ $certificate->course->title }}"</h5>
                                    
                                    <p class="text-gray-700">
                                        Pada tanggal {{ $certificate->issued_at->format('d F Y') }}
                                    </p>
                                    <p class="text-gray-600 italic mb-8">
                                        On {{ $certificate->issued_at->format('F d, Y') }}
                                    </p>
                                    
                                    @if($certificate->course->instructor)
                                    <p class="text-gray-700">
                                        Instruktur: {{ $certificate->course->instructor->full_name }}
                                    </p>
                                    @endif
                                    
                                    <div class="mt-8">
                                        <p class="text-sm text-gray-500">Certificate No: {{ $certificate->certificate_number }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Certificate Details -->
                        <div class="lg:w-1/3">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Sertifikat</h3>
                                    
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Kursus</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $certificate->course->title }}</dd>
                                        </div>
                                        
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Diterbitkan</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $certificate->issued_at->format('d F Y') }}</dd>
                                        </div>
                                        
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Nomor Sertifikat</dt>
                                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $certificate->certificate_number }}</dd>
                                        </div>
                                        
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Kode Verifikasi</dt>
                                            <dd class="mt-1 text-sm text-gray-900 font-mono break-all">{{ $certificate->verification_code }}</dd>
                                        </div>
                                        
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                                            <dd class="mt-1">
                                                @if($certificate->is_valid && !$certificate->revoked_at)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Valid
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Dicabut
                                                    </span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <div class="pt-6 border-t border-gray-200">
                                    <div class="space-y-3">
                                        <a href="{{ route('certificates.download', $certificate->id) }}" 
                                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Unduh PDF
                                        </a>
                                        
                                        <a href="{{ route('certificates.verify', $certificate->verification_code) }}" 
                                           target="_blank"
                                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Verifikasi Sertifikat
                                        </a>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-gray-200">
                                    <p class="text-xs text-gray-500">
                                        Bagikan sertifikat Anda dengan menggunakan kode verifikasi di atas atau tautan verifikasi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

