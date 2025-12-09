@props(['certificate'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
    <div class="p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-semibold flex-shrink-0">
                <span>{{ strtoupper(substr($certificate->course->title, 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $certificate->course->title }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Diterbitkan {{ $certificate->issued_at->format('d M Y') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">No: {{ $certificate->certificate_number }}</p>
                @if($certificate->is_valid && !$certificate->revoked_at)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-2">
                        Valid
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-2">
                        Dicabut
                    </span>
                @endif
            </div>
        </div>
        
        <div class="mt-4 flex gap-2">
            <a href="{{ route('certificates.show', $certificate->id) }}" 
               class="flex-1 text-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Lihat
            </a>
            <a href="{{ route('certificates.download', $certificate->id) }}" 
               class="flex-1 text-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                Unduh
            </a>
        </div>
    </div>
</div>

