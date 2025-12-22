@props(['sourceCode'])

<div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group transform hover:-translate-y-2">
    <a href="{{ route('source-codes.show', $sourceCode->slug) }}" class="block">
        <div class="relative overflow-hidden">
            @if($sourceCode->thumbnail)
                <img src="{{ asset('storage/' . $sourceCode->thumbnail) }}" 
                     alt="{{ $sourceCode->title }}" 
                     class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300">
            @else
                <div class="w-full h-48 bg-gradient-to-br from-emerald-400 via-teal-400 to-cyan-400 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
            @endif
            <div class="absolute top-3 right-3">
                @if($sourceCode->isFree())
                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg">
                        GRATIS
                    </span>
                @endif
            </div>
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-emerald-600 text-xs font-semibold rounded-full shadow-md">
                    {{ $sourceCode->level->name }}
                </span>
            </div>
        </div>
        
        <div class="p-5">
            <div class="mb-2">
                <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">
                    {{ $sourceCode->category->name }}
                </span>
            </div>
            
            <h3 class="font-bold text-lg mb-2 line-clamp-2 text-gray-900 group-hover:text-emerald-600 transition-colors">
                {{ $sourceCode->title }}
            </h3>
            
            @if($sourceCode->subtitle)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $sourceCode->subtitle }}</p>
            @endif

            @if($sourceCode->technologies && count($sourceCode->technologies) > 0)
                <div class="flex flex-wrap gap-1 mb-3">
                    @foreach(array_slice($sourceCode->technologies, 0, 3) as $tech)
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $tech }}</span>
                    @endforeach
                    @if(count($sourceCode->technologies) > 3)
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">+{{ count($sourceCode->technologies) - 3 }}</span>
                    @endif
                </div>
            @endif

            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                        <span class="text-xs font-bold text-emerald-600">
                            {{ substr($sourceCode->instructor->full_name ?? $sourceCode->instructor->name ?? 'Instruktur', 0, 1) }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-600">{{ $sourceCode->instructor->full_name ?? $sourceCode->instructor->name ?? 'Instruktur' }}</span>
                </div>
                <div class="text-right">
                    @if($sourceCode->isFree())
                        <span class="text-lg font-bold text-green-600">Gratis</span>
                    @else
                        <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($sourceCode->price, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </a>
</div>
