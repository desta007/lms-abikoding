<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Enrollments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($enrollments->count() > 0)
                        <div class="space-y-4">
                            @foreach($enrollments as $enrollment)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-start gap-4">
                                                @if($enrollment->course->thumbnail)
                                                    <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" 
                                                         alt="{{ $enrollment->course->title }}" 
                                                         class="w-24 h-24 object-cover rounded-lg">
                                                @else
                                                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-400 via-purple-400 to-pink-400 rounded-lg flex items-center justify-center">
                                                        <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                        <a href="{{ route('courses.show', $enrollment->course->slug) }}" class="hover:text-indigo-600">
                                                            {{ $enrollment->course->title }}
                                                        </a>
                                                    </h3>
                                                    <p class="text-sm text-gray-600 mb-2">{{ $enrollment->course->subtitle }}</p>
                                                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                            {{ $enrollment->course->category->name }}
                                                        </span>
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                                            {{ $enrollment->course->level->name }}
                                                        </span>
                                                        <span>Oleh: {{ $enrollment->course->instructor->full_name ?? $enrollment->course->instructor->name }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Progress Bar -->
                                            <div class="mt-4">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm font-medium text-gray-700">Progress</span>
                                                    <span class="text-sm font-semibold text-indigo-600">{{ $enrollment->progress_percentage ?? 0 }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" 
                                                         style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col gap-2 md:items-end">
                                            @if($enrollment->completed_at)
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                                    Dalam Proses
                                                </span>
                                            @endif
                                            <a href="{{ route('courses.content', $enrollment->course->id) }}" 
                                               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-semibold transition-colors">
                                                {{ $enrollment->completed_at ? 'Lihat Materi' : 'Lanjutkan Belajar' }}
                                            </a>
                                            <span class="text-xs text-gray-500">
                                                Terdaftar: {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d M Y') : $enrollment->created_at->format('d M Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $enrollments->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada kursus yang diikuti</h3>
                            <p class="text-gray-600 mb-6">Mulai jelajahi kursus yang tersedia dan daftar untuk mulai belajar!</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                                Jelajahi Kursus
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

