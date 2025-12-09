<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Hasil Quiz</h1>
                <p class="text-gray-600 mt-2">{{ $attempt->exam->title }}</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-indigo-600">
                            @if($attempt->percentage !== null)
                                {{ number_format($attempt->percentage, 1) }}%
                            @else
                                -
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Nilai</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">
                            @if($attempt->score !== null)
                                {{ $attempt->score }}
                            @else
                                -
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Skor</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">
                            @if($attempt->total_points !== null)
                                {{ $attempt->total_points }}
                            @else
                                -
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Total Poin</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($attempt->status === 'passed') bg-green-100 text-green-800
                            @elseif($attempt->status === 'failed') bg-red-100 text-red-800
                            @elseif($attempt->status === 'graded') bg-green-100 text-green-800
                            @elseif($attempt->status === 'submitted') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($attempt->status === 'passed')
                                Lulus
                            @elseif($attempt->status === 'failed')
                                Tidak Lulus
                            @elseif($attempt->status === 'graded')
                                Selesai
                            @elseif($attempt->status === 'submitted')
                                Menunggu Penilaian
                            @else
                                {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                            @endif
                        </span>
                    </div>
                    @if($attempt->exam->minimum_passing_score)
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="text-gray-600">Nilai Minimum untuk Lulus:</span>
                            <span class="text-gray-900 font-medium">{{ $attempt->exam->minimum_passing_score }}%</span>
                        </div>
                    @endif
                    @if($attempt->submitted_at)
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="text-gray-600">Dikirim pada:</span>
                            <span class="text-gray-900">{{ $attempt->submitted_at->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-900">Review Jawaban</h2>
                
                @foreach($attempt->exam->questions as $index => $question)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="mb-4">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-indigo-100 text-indigo-800 rounded-full flex items-center justify-center font-semibold mr-3">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $question->question_text }}</h3>
                                    <p class="text-sm text-gray-500">Nilai: {{ $question->points }} poin</p>
                                </div>
                            </div>
                        </div>

                        <div class="ml-11">
                            @php
                                $userAnswers = $attempt->answers ?? [];
                                $isCorrect = false;
                                
                                if (isset($userAnswers[$question->id]) && in_array($question->question_type, ['multiple_choice', 'true_false'])) {
                                    $selectedAnswer = $question->answers()->find($userAnswers[$question->id]);
                                    if ($selectedAnswer) {
                                        $isCorrect = $selectedAnswer->is_correct;
                                    }
                                }
                            @endphp

                            @if($question->question_type === 'multiple_choice' || $question->question_type === 'true_false')
                                <div class="space-y-2">
                                    @foreach($question->answers as $answer)
                                        @php
                                            $isSelected = isset($userAnswers[$question->id]) && $userAnswers[$question->id] == $answer->id;
                                        @endphp
                                        <div class="flex items-center p-3 border rounded-lg
                                            @if($isSelected && $answer->is_correct) border-green-500 bg-green-50
                                            @elseif($isSelected && !$answer->is_correct) border-red-500 bg-red-50
                                            @elseif(!$isSelected && $answer->is_correct) border-green-500 bg-green-50
                                            @else border-gray-200
                                            @endif">
                                            <input type="radio" 
                                                   disabled
                                                   @if($isSelected) checked @endif
                                                   class="w-4 h-4 text-indigo-600">
                                            <span class="ml-3 flex-1
                                                @if($isSelected && $answer->is_correct) text-green-800 font-medium
                                                @elseif($isSelected && !$answer->is_correct) text-red-800 font-medium
                                                @elseif(!$isSelected && $answer->is_correct) text-green-800 font-medium
                                                @else text-gray-700
                                                @endif">
                                                {{ $answer->answer_text }}
                                            </span>
                                            @if($answer->is_correct)
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->question_type === 'essay')
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <p class="text-sm text-gray-600 mb-2">Jawaban Anda:</p>
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $userAnswers[$question->id] ?? 'Tidak ada jawaban' }}</p>
                                    @if($attempt->status === 'submitted')
                                        <p class="text-sm text-yellow-600 mt-2">Menunggu penilaian instruktur</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Retake Request Section -->
            @if($attempt->status === 'failed')
                <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-start mb-4">
                        <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-yellow-900 mb-2">Tidak Lulus Quiz</h3>
                            <p class="text-sm text-yellow-800 mb-2">
                                Anda tidak mencapai nilai minimum yang ditentukan ({{ $attempt->exam->minimum_passing_score ?? 70 }}%).
                            </p>
                            <p class="text-sm text-yellow-800">
                                Untuk mengulang quiz, Anda perlu meminta persetujuan instruktur terlebih dahulu dengan menekan tombol di bawah ini.
                            </p>
                        </div>
                    </div>
                    
                    @if($attempt->retake_requested)
                        @if($attempt->retake_approved)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium text-green-800">
                                        Permintaan ulang quiz Anda telah disetujui. Anda dapat mengulang quiz sekarang.
                                    </span>
                                </div>
                            </div>
                            <div class="flex justify-center gap-4">
                                <a href="{{ route('student.exams.show', $attempt->exam_id) }}" 
                                   class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                    Mulai Quiz Ulang
                                </a>
                                <a href="{{ route('courses.content', $attempt->exam->course_id) }}" 
                                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                                    Kembali
                                </a>
                            </div>
                        @elseif($attempt->retake_rejection_reason)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-red-800 mb-1">Permintaan Ulang Quiz Ditolak</h4>
                                        <p class="text-sm text-red-700">{{ $attempt->retake_rejection_reason }}</p>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('student.exams.request-retake', $attempt->id) }}" method="POST" class="flex justify-center">
                                @csrf
                                <button type="submit" 
                                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium shadow-md hover:shadow-lg">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Ajukan Ulang Permintaan
                                    </span>
                                </button>
                            </form>
                        @else
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-800">
                                        Permintaan ulang quiz Anda sedang menunggu persetujuan instruktur.
                                    </span>
                                </div>
                            </div>
                            <div class="flex justify-center">
                                <a href="{{ route('courses.content', $attempt->exam->course_id) }}" 
                                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                                    Kembali
                                </a>
                            </div>
                        @endif
                    @else
                        <form action="{{ route('student.exams.request-retake', $attempt->id) }}" method="POST" class="flex justify-center">
                            @csrf
                            <button type="submit" 
                                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium shadow-md hover:shadow-lg">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Minta Izin Quiz Ulang
                                </span>
                            </button>
                        </form>
                    @endif
                </div>
            @else
                <div class="mt-8 flex justify-center gap-4">
                    <a href="{{ route('courses.content', $attempt->exam->course_id) }}" 
                       class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        Kembali
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

