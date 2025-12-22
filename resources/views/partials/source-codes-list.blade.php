@if($sourceCodes->count() > 0)
    <div id="source-codes-container" class="{{ request('view', 'grid') == 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-4' }}">
        @foreach($sourceCodes as $sourceCode)
            @if(request('view', 'grid') == 'list')
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all overflow-hidden">
                    <a href="{{ route('source-codes.show', $sourceCode->slug) }}" class="flex flex-col md:flex-row">
                        <div class="md:w-64 flex-shrink-0">
                            @if($sourceCode->thumbnail)
                                <img src="{{ asset('storage/' . $sourceCode->thumbnail) }}" alt="{{ $sourceCode->title }}" class="w-full h-48 md:h-full object-cover">
                            @else
                                <div class="w-full h-48 md:h-full bg-gradient-to-br from-emerald-400 via-teal-400 to-cyan-400 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $sourceCode->title }}</h3>
                                    <p class="text-gray-600 mb-3 line-clamp-2">{{ $sourceCode->subtitle }}</p>
                                </div>
                                @if($sourceCode->isFree())
                                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full ml-4">GRATIS</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4 mb-3">
                                <span class="text-sm text-gray-600">Oleh: {{ $sourceCode->instructor->full_name ?? $sourceCode->instructor->name }}</span>
                                <span class="px-2 py-1 text-xs bg-emerald-100 text-emerald-800 rounded">{{ $sourceCode->level->name }}</span>
                            </div>
                            @if($sourceCode->technologies && count($sourceCode->technologies) > 0)
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach(array_slice($sourceCode->technologies, 0, 5) as $tech)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $tech }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-emerald-600 uppercase">{{ $sourceCode->category->name }}</span>
                                @if(!$sourceCode->isFree())
                                    <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($sourceCode->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @else
                <x-source-code-card :sourceCode="$sourceCode" />
            @endif
        @endforeach
    </div>

    <div class="mt-8" id="source-codes-pagination">
        {{ $sourceCodes->links() }}
    </div>
@else
    <div class="text-center py-16 bg-white rounded-xl shadow-lg">
        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada source code ditemukan</h3>
        <p class="text-gray-600">Coba ubah filter atau kata kunci pencarian Anda</p>
    </div>
@endif
