<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Siswa</h1>
                <p class="text-gray-600 mt-2">Selamat datang kembali, {{ Auth::user()->full_name }}!</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Kursus</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_enrollments'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Kursus Selesai</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_courses'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Sedang Berjalan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['in_progress_courses'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Sertifikat</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_certificates'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Enrollments -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Kursus Terbaru</h2>
                        <a href="{{ route('profile.enrollments') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Lihat Semua</a>
                    </div>
                    
                    @if($enrollments->count() > 0)
                        <div class="space-y-4">
                            @foreach($enrollments as $enrollment)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $enrollment->course->title }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $enrollment->course->instructor->full_name ?? $enrollment->course->instructor->name }}
                                        </p>
                                        <div class="mt-2">
                                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                                <span>Progress</span>
                                                <span>{{ $enrollment->calculateProgress() }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $enrollment->calculateProgress() }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('courses.content', $enrollment->course_id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Lanjutkan →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Anda belum terdaftar di kursus manapun</p>
                            <a href="{{ route('home') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Jelajahi Kursus →
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Upcoming Exams -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Quiz Mendatang</h2>
                        <a href="{{ route('student.exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Lihat Semua</a>
                    </div>
                    
                    @if($upcomingExams->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingExams as $exam)
                                <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <h3 class="font-medium text-gray-900">{{ $exam->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $exam->course->title }}</p>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            @if($exam->start_date)
                                                {{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y, H:i') }}
                                            @else
                                                Tersedia Sekarang
                                            @endif
                                        </div>
                                        <a href="{{ route('student.exams.show', $exam->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Mulai →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Tidak ada quiz mendatang</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

