<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Progress Bar -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $course->title }}</h2>
                    <span class="text-sm font-medium text-gray-600">{{ number_format($overallProgress, 0) }}% Selesai</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $overallProgress }}%"></div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($overallProgress >= 100)
                @php
                    $hasCertificate = \App\Models\Certificate::where('course_enrollment_id', $enrollment->id)->exists();
                @endphp
                @if(!$hasCertificate)
                    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-semibold text-green-900">Selamat! Kursus Telah Selesai</h3>
                                    <p class="text-sm text-green-700">Generate sertifikat Anda sekarang</p>
                                </div>
                            </div>
                            <a href="{{ route('certificates.generate', $course->id) }}" 
                               class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                                Generate Sertifikat
                            </a>
                        </div>
                    </div>
                @else
                    @php
                        $certificate = \App\Models\Certificate::where('course_enrollment_id', $enrollment->id)->first();
                    @endphp
                    <div class="mb-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-semibold text-indigo-900">Sertifikat Tersedia</h3>
                                    <p class="text-sm text-indigo-700">Nomor: {{ $certificate->certificate_number }}</p>
                                </div>
                            </div>
                            <a href="{{ route('certificates.show', $certificate->id) }}" 
                               class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                                Lihat Sertifikat
                            </a>
                        </div>
                    </div>
                @endif
            @endif
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar: Chapters List -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-4 sticky top-24">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daftar Bab</h3>
                        <div class="space-y-2">
                            @foreach($course->chapters as $chapter)
                                @php
                                    $chapterMaterials = $chapter->materials->count();
                                    $completedMaterials = $progress->where('chapter_id', $chapter->id)->where('is_completed', true)->count();
                                    $chapterProgress = $chapterMaterials > 0 ? ($completedMaterials / $chapterMaterials) * 100 : 0;
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                    <a href="{{ route('courses.chapter', [$course->id, $chapter->id]) }}" 
                                       class="block">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-900">
                                                Bab {{ $chapter->order }}: {{ $chapter->title }}
                                            </span>
                                            @if($completedMaterials == $chapterMaterials && $chapterMaterials > 0)
                                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mb-1">
                                            {{ $completedMaterials }}/{{ $chapterMaterials }} materi selesai
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $chapterProgress }}%"></div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Konten Kursus</h2>
                        
                        @if($course->chapters->count() > 0)
                            <div class="space-y-6">
                                @foreach($course->chapters as $chapter)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    Bab {{ $chapter->order }}: {{ $chapter->title }}
                                                </h3>
                                                @if($chapter->description)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $chapter->description }}</p>
                                                @endif
                                            </div>
                                            <a href="{{ route('courses.chapter', [$course->id, $chapter->id]) }}" 
                                               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                                                Lihat Bab
                                            </a>
                                        </div>
                                        
                                        @if($chapter->materials->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($chapter->materials->sortBy('order') as $index => $material)
                                                    @php
                                                        $materialProgress = $progress->get($material->id);
                                                        $isCompleted = $materialProgress && $materialProgress->is_completed;
                                                        $isApproved = $materialProgress && $materialProgress->isCompleted();
                                                        
                                                        // Check access using ProgressionService
                                                        $progressionService = new \App\Services\ProgressionService();
                                                        $accessCheck = $progressionService->canAccessMaterial(Auth::user(), $material, $enrollment);
                                                        $canAccess = $accessCheck['allowed'];
                                                    @endphp
                                                    <div class="flex items-center justify-between p-3 {{ $canAccess ? 'bg-gray-50 hover:bg-gray-100' : 'bg-gray-100 opacity-75' }} rounded-lg transition-colors">
                                                        @if($canAccess)
                                                            <a href="{{ route('courses.material', [$course->id, $chapter->id, $material->id]) }}" 
                                                               class="flex items-center gap-3 flex-1">
                                                        @else
                                                            <div class="flex items-center gap-3 flex-1 cursor-not-allowed" 
                                                                 onclick="event.preventDefault(); alert('Anda harus menyelesaikan dan mendapatkan persetujuan untuk materi sebelumnya terlebih dahulu.');">
                                                        @endif
                                                            @if(!$canAccess)
                                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                                </svg>
                                                            @elseif($isApproved)
                                                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @else
                                                                <div class="w-5 h-5 border-2 border-gray-300 rounded"></div>
                                                            @endif
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="text-sm font-medium {{ $canAccess ? 'text-gray-900' : 'text-gray-500' }}">{{ $material->title }}</span>
                                                                    @if(!$canAccess)
                                                                        <span class="px-2 py-0.5 text-xs bg-gray-300 text-gray-700 rounded flex items-center gap-1">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                                            </svg>
                                                                            Terkunci
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <span class="ml-0 mt-1 inline-block px-2 py-0.5 text-xs {{ $canAccess ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-600' }} rounded">
                                                                    {{ strtoupper($material->material_type) }}
                                                                </span>
                                                            </div>
                                                        @if($canAccess)
                                                            </a>
                                                        @else
                                                            </div>
                                                        @endif
                                                        @if($canAccess && $materialProgress && $materialProgress->progress_percentage > 0)
                                                            <span class="text-xs text-gray-500">{{ $materialProgress->progress_percentage }}%</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500">Belum ada materi dalam bab ini.</p>
                                        @endif

                                        {{-- Display Quizzes for this Chapter --}}
                                        @php
                                            $chapterExams = $exams->get($chapter->id, collect());
                                        @endphp
                                        @if($chapterExams->count() > 0)
                                            <div class="mt-4 pt-4 border-t border-gray-200">
                                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Quiz</h4>
                                                <div class="space-y-2">
                                                    @foreach($chapterExams as $exam)
                                                        @php
                                                            $attempt = \App\Models\ExamAttempt::where('exam_id', $exam->id)
                                                                ->where('user_id', Auth::id())
                                                                ->latest()
                                                                ->first();
                                                            $hasPassed = $attempt && $attempt->status === 'passed';
                                                            $bestScore = \App\Models\ExamAttempt::where('exam_id', $exam->id)
                                                                ->where('user_id', Auth::id())
                                                                ->max('score');
                                                        @endphp
                                                        <a href="{{ route('student.exams.show', $exam->id) }}" 
                                                           class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors border border-indigo-200">
                                                            <div class="flex items-center gap-3">
                                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                                <div>
                                                                    <span class="text-sm font-medium text-gray-900">{{ $exam->title }}</span>
                                                                    <div class="flex items-center gap-2 mt-1">
                                                                        <span class="px-2 py-0.5 text-xs bg-indigo-100 text-indigo-800 rounded">
                                                                            {{ $exam->questions->count() }} pertanyaan
                                                                        </span>
                                                                        <span class="px-2 py-0.5 text-xs bg-orange-100 text-orange-800 rounded">
                                                                            Min: {{ $exam->minimum_passing_score }}%
                                                                        </span>
                                                                        @if($hasPassed)
                                                                            <span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded">
                                                                                Lulus
                                                                            </span>
                                                                        @elseif($bestScore !== null)
                                                                            <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">
                                                                                Skor: {{ $bestScore }}%
                                                                            </span>
                                                                        @endif
                                                                        @if($exam->is_required_for_progression)
                                                                            <span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded">
                                                                                Wajib
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                            </svg>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <p class="text-gray-500">Kursus ini belum memiliki konten.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

