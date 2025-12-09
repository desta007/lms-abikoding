@if($courses->count() > 0)
    <div id="courses-container" class="{{ request('view', 'grid') == 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-4' }}">
        @foreach($courses as $course)
            @if(request('view', 'grid') == 'list')
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all overflow-hidden">
                    <a href="{{ route('courses.show', $course->slug) }}" class="flex flex-col md:flex-row">
                        <div class="md:w-64 flex-shrink-0">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 md:h-full object-cover">
                            @else
                                <div class="w-full h-48 md:h-full bg-gradient-to-br from-indigo-400 via-purple-400 to-pink-400 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $course->title }}</h3>
                                    <p class="text-gray-600 mb-3 line-clamp-2">{{ $course->subtitle }}</p>
                                </div>
                                @if($course->isFree())
                                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full ml-4">GRATIS</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4 mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= round($course->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ number_format($course->average_rating, 1) }}</span>
                                </div>
                                <span class="text-sm text-gray-600">Oleh: {{ $course->instructor->full_name ?? $course->instructor->name }}</span>
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ $course->level->name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-indigo-600 uppercase">{{ $course->category->name }}</span>
                                @if(!$course->isFree())
                                    <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @else
                <x-course-card :course="$course" />
            @endif
        @endforeach
    </div>

    <div class="mt-8" id="courses-pagination">
        {{ $courses->links() }}
    </div>
@else
    <div class="text-center py-16 bg-white rounded-xl shadow-lg">
        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada kursus ditemukan</h3>
        <p class="text-gray-600">Coba ubah filter atau kata kunci pencarian Anda</p>
    </div>
@endif

