@props(['course'])

<div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group transform hover:-translate-y-2">
    <a href="{{ route('courses.show', $course->slug) }}" class="block">
        <div class="relative overflow-hidden">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                     alt="{{ $course->title }}" 
                     class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300">
            @else
                <div class="w-full h-48 bg-gradient-to-br from-indigo-400 via-purple-400 to-pink-400 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            @endif
            <div class="absolute top-3 right-3">
                @if($course->isFree())
                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg">
                        GRATIS
                    </span>
                @endif
            </div>
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-indigo-600 text-xs font-semibold rounded-full shadow-md">
                    {{ $course->level->name }}
                </span>
            </div>
        </div>
        
        <div class="p-5">
            <div class="mb-2">
                <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">
                    {{ $course->category->name }}
                </span>
            </div>
            
            <h3 class="font-bold text-lg mb-2 line-clamp-2 text-gray-900 group-hover:text-indigo-600 transition-colors">
                {{ $course->title }}
            </h3>
            
            @if($course->subtitle)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $course->subtitle }}</p>
            @endif

            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($course->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-sm font-medium text-gray-700">{{ number_format($course->average_rating, 1) }}</span>
                <span class="text-xs text-gray-500">({{ $course->ratings->count() }})</span>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <span class="text-xs font-bold text-indigo-600">
                            {{ substr($course->instructor->full_name ?? $course->instructor->name ?? 'Instruktur', 0, 1) }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-600">{{ $course->instructor->full_name ?? $course->instructor->name ?? 'Instruktur' }}</span>
                </div>
                <div class="text-right">
                    @if($course->isFree())
                        <span class="text-lg font-bold text-green-600">Gratis</span>
                    @else
                        <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </a>
</div>
