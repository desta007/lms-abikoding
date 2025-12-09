<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Breadcrumb -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('courses.content', $course->id) }}" class="text-gray-400 hover:text-gray-500">
                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <a href="{{ route('courses.content', $course->id) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                                    {{ $course->title }}
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-900">Bab {{ $chapter->order }}: {{ $chapter->title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Bab {{ $chapter->order }}: {{ $chapter->title }}</h1>
                    @if($chapter->description)
                        <p class="text-gray-600">{{ $chapter->description }}</p>
                    @endif
                </div>

                @if($chapter->materials->count() > 0)
                    <div class="space-y-4">
                        @foreach($chapter->materials as $index => $material)
                            @php
                                $materialProgress = $progress->get($material->id);
                                $isCompleted = $materialProgress && $materialProgress->is_completed;
                                $isApproved = $materialProgress && $materialProgress->isCompleted();
                                $isPendingApproval = $materialProgress && $materialProgress->is_completed && !$materialProgress->isCompleted();
                                
                                // Check if previous materials are completed and approved
                                $canAccess = true;
                                if ($index > 0) {
                                    for ($i = 0; $i < $index; $i++) {
                                        $prevMaterial = $chapter->materials->sortBy('order')->values()[$i];
                                        $prevProgress = $progress->get($prevMaterial->id);
                                        if (!$prevProgress || !$prevProgress->isCompleted()) {
                                            $canAccess = false;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <a href="{{ route('courses.material', [$course->id, $chapter->id, $material->id]) }}" 
                               class="block p-4 border rounded-lg transition-all {{ $canAccess ? 'border-gray-200 hover:border-indigo-500 hover:bg-indigo-50' : 'border-gray-300 bg-gray-50 opacity-60 cursor-not-allowed' }}"
                               @if(!$canAccess) onclick="event.preventDefault(); alert('Anda harus menyelesaikan dan mendapatkan persetujuan untuk materi sebelumnya terlebih dahulu.');" @endif>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0">
                                            @if(!$canAccess)
                                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                    </svg>
                                                </div>
                                            @elseif($isApproved)
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @elseif($isPendingApproval)
                                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <span class="text-gray-600 font-semibold">{{ $index + 1 }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-lg font-semibold {{ $canAccess ? 'text-gray-900' : 'text-gray-500' }}">{{ $material->title }}</h3>
                                                @if(!$canAccess)
                                                    <span class="px-2 py-1 text-xs font-medium bg-gray-200 text-gray-700 rounded flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                        Terkunci
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="px-2 py-1 text-xs font-medium {{ $canAccess ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-600' }} rounded">
                                                    {{ strtoupper($material->material_type) }}
                                                </span>
                                                @if($materialProgress && $materialProgress->progress_percentage > 0 && $canAccess)
                                                    <span class="text-sm text-gray-500">{{ $materialProgress->progress_percentage }}% selesai</span>
                                                @endif
                                                @if($isApproved)
                                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                                        Disetujui
                                                    </span>
                                                @elseif($isPendingApproval)
                                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">
                                                        Menunggu Persetujuan
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($canAccess)
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500">Belum ada materi dalam bab ini.</p>
                    </div>
                @endif

                {{-- Display Quizzes for this Chapter --}}
                @if(isset($exams) && $exams->count() > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quiz</h3>
                        <div class="space-y-3">
                            @foreach($exams->sortBy('order') as $index => $exam)
                                @php
                                    $attempt = \App\Models\ExamAttempt::where('exam_id', $exam->id)
                                        ->where('user_id', Auth::id())
                                        ->latest()
                                        ->first();
                                    
                                    // Check if quiz is passed
                                    $hasPassed = false;
                                    if ($attempt) {
                                        if ($attempt->status === 'passed') {
                                            $hasPassed = true;
                                        } elseif ($attempt->status === 'graded' && $attempt->percentage && $attempt->percentage >= $exam->minimum_passing_score) {
                                            $hasPassed = true;
                                        } elseif ($attempt->status === 'failed' && $attempt->percentage && $attempt->percentage >= $exam->minimum_passing_score) {
                                            $hasPassed = true;
                                        }
                                    }
                                    
                                    $bestScore = \App\Models\ExamAttempt::where('exam_id', $exam->id)
                                        ->where('user_id', Auth::id())
                                        ->max('percentage');
                                    
                                    // Check if previous quizzes are passed (for sequential access)
                                    $canAccessQuiz = true;
                                    $blockingQuiz = null;
                                    if ($index > 0) {
                                        $previousExams = $exams->sortBy('order')->take($index);
                                        foreach ($previousExams as $prevExam) {
                                            if ($prevExam->is_required_for_progression) {
                                                $prevAttempt = \App\Models\ExamAttempt::where('exam_id', $prevExam->id)
                                                    ->where('user_id', Auth::id())
                                                    ->latest()
                                                    ->first();
                                                
                                                $prevPassed = false;
                                                if ($prevAttempt) {
                                                    if ($prevAttempt->status === 'passed') {
                                                        $prevPassed = true;
                                                    } elseif ($prevAttempt->status === 'graded' && $prevAttempt->percentage && $prevAttempt->percentage >= $prevExam->minimum_passing_score) {
                                                        $prevPassed = true;
                                                    } elseif ($prevAttempt->status === 'failed' && $prevAttempt->percentage && $prevAttempt->percentage >= $prevExam->minimum_passing_score) {
                                                        $prevPassed = true;
                                                    }
                                                }
                                                
                                                if (!$prevPassed) {
                                                    $canAccessQuiz = false;
                                                    $blockingQuiz = $prevExam;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                <div class="block p-4 border rounded-lg transition-all {{ $canAccessQuiz ? 'border-indigo-200 hover:border-indigo-500 hover:bg-indigo-50 bg-indigo-50' : 'border-gray-300 bg-gray-50 opacity-60 cursor-not-allowed' }}">
                                    @if($canAccessQuiz)
                                        <a href="{{ route('student.exams.show', $exam->id) }}" class="flex items-center justify-between">
                                    @else
                                        <div class="flex items-center justify-between" onclick="event.preventDefault(); alert('Anda harus menyelesaikan dan lulus quiz \'{{ $blockingQuiz->title }}\' terlebih dahulu sebelum dapat mengakses quiz ini.');">
                                    @endif
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 {{ $canAccessQuiz ? 'bg-indigo-100' : 'bg-gray-200' }} rounded-full flex items-center justify-center">
                                                    @if($canAccessQuiz)
                                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold {{ $canAccessQuiz ? 'text-gray-900' : 'text-gray-500' }}">{{ $exam->title }}</h4>
                                                @if($exam->description)
                                                    <p class="text-sm {{ $canAccessQuiz ? 'text-gray-600' : 'text-gray-400' }} mt-1">{{ Str::limit($exam->description, 100) }}</p>
                                                @endif
                                                <div class="flex items-center gap-2 mt-2 flex-wrap">
                                                    <span class="px-2 py-1 text-xs font-medium {{ $canAccessQuiz ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-200 text-gray-600' }} rounded">
                                                        {{ $exam->questions->count() }} pertanyaan
                                                    </span>
                                                    <span class="px-2 py-1 text-xs font-medium {{ $canAccessQuiz ? 'bg-orange-100 text-orange-800' : 'bg-gray-200 text-gray-600' }} rounded">
                                                        Min: {{ $exam->minimum_passing_score }}%
                                                    </span>
                                                    @if($hasPassed)
                                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                                            ✓ Lulus
                                                        </span>
                                                    @elseif($bestScore !== null)
                                                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">
                                                            Skor: {{ number_format($bestScore, 1) }}%
                                                        </span>
                                                    @endif
                                                    @if($exam->is_required_for_progression)
                                                        <span class="px-2 py-1 text-xs font-medium {{ $canAccessQuiz ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-600' }} rounded">
                                                            Wajib Lulus
                                                        </span>
                                                    @endif
                                                    @if(!$canAccessQuiz)
                                                        <span class="px-2 py-1 text-xs font-medium bg-gray-300 text-gray-700 rounded">
                                                            🔒 Terkunci
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if($canAccessQuiz)
                                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        @endif
                                    @if($canAccessQuiz)
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

