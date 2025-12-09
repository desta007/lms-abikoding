<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Quiz</h1>
                <p class="text-gray-600 mt-2">Daftar quiz yang tersedia untuk Anda</p>
            </div>

            @if($exams->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($exams as $exam)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $exam->title }}</h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <span class="font-medium">Kursus:</span> {{ $exam->course->title }}
                                        </p>
                                        @if($exam->chapter)
                                            <p class="text-sm text-gray-600 mb-2">
                                                <span class="font-medium">Bab:</span> {{ $exam->chapter->title }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if($exam->description)
                                    <p class="text-sm text-gray-700 mb-4 line-clamp-2">{{ $exam->description }}</p>
                                @endif

                                <div class="space-y-2 mb-4">
                                    @if($exam->duration_minutes)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Durasi: {{ $exam->duration_minutes }} menit
                                        </div>
                                    @endif

                                    @if($exam->start_date)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Mulai: {{ $exam->start_date->format('d M Y, H:i') }}
                                        </div>
                                    @endif

                                    @if($exam->end_date)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Berakhir: {{ $exam->end_date->format('d M Y, H:i') }}
                                        </div>
                                    @endif

                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $exam->questions()->count() }} pertanyaan
                                    </div>
                                </div>

                                @php
                                    $attempt = \App\Models\ExamAttempt::where('exam_id', $exam->id)
                                        ->where('user_id', Auth::id())
                                        ->whereIn('status', ['submitted', 'graded'])
                                        ->latest()
                                        ->first();
                                @endphp

                                @if($attempt)
                                    <div class="mb-4">
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <p class="text-sm text-blue-800">
                                                <span class="font-medium">Status:</span> 
                                                @if($attempt->status === 'graded')
                                                    Selesai
                                                @else
                                                    Sudah dikirim
                                                @endif
                                            </p>
                                            @if($attempt->percentage !== null)
                                                <p class="text-sm text-blue-800 mt-1">
                                                    <span class="font-medium">Nilai:</span> {{ number_format($attempt->percentage, 2) }}%
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <a href="{{ route('student.exams.show', $exam->id) }}" 
                                   class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                                    @if($attempt)
                                        Lihat Hasil
                                    @else
                                        Mulai Quiz
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada quiz tersedia</h3>
                    <p class="text-gray-500 mb-4">Belum ada quiz yang tersedia untuk kursus yang Anda ikuti.</p>
                    <a href="{{ route('home') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Jelajahi Kursus
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

