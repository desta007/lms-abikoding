<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <nav class="text-sm mb-2">
                    <a href="{{ route('instructor.exams.index') }}" class="text-indigo-600 hover:text-indigo-900">Quiz</a>
                    <span class="text-gray-400 mx-2">/</span>
                    <a href="{{ route('instructor.exams.attempts', $attempt->exam_id) }}" class="text-indigo-600 hover:text-indigo-900">Percobaan</a>
                    <span class="text-gray-400 mx-2">/</span>
                    <span class="text-gray-500">Penilaian</span>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900">Penilaian Quiz</h1>
                <p class="text-gray-600 mt-2">{{ $attempt->exam->title }} - {{ $attempt->user->name }}</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <ul class="list-disc list-inside text-sm text-red-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Student Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Siswa</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama</p>
                        <p class="font-medium text-gray-900">{{ $attempt->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium text-gray-900">{{ $attempt->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Submit</p>
                        <p class="font-medium text-gray-900">{{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($attempt->status === 'passed') bg-green-100 text-green-800
                            @elseif($attempt->status === 'failed') bg-red-100 text-red-800
                            @elseif($attempt->status === 'submitted') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($attempt->status === 'passed') Lulus
                            @elseif($attempt->status === 'failed') Tidak Lulus
                            @elseif($attempt->status === 'submitted') Menunggu Penilaian
                            @else {{ ucfirst($attempt->status) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <form action="{{ route('instructor.exams.save-grade', $attempt->id) }}" method="POST">
                @csrf

                <div class="space-y-6">
                    @php
                        $userAnswers = $attempt->answers ?? [];
                        $essayScores = $attempt->essay_scores ?? [];
                        $questionNumber = 0;
                    @endphp

                    @foreach($attempt->exam->questions as $question)
                        @php $questionNumber++; @endphp
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="mb-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-indigo-100 text-indigo-800 rounded-full flex items-center justify-center font-semibold mr-3">
                                            {{ $questionNumber }}
                                        </span>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $question->question_text }}</h3>
                                            <p class="text-sm text-gray-500">
                                                Tipe: {{ $question->question_type === 'essay' ? 'Essay' : ($question->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Benar/Salah') }}
                                                | Nilai Maks: {{ $question->points }} poin
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-11">
                                @if($question->question_type === 'essay')
                                    <!-- Essay Answer -->
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 mb-4">
                                        <p class="text-sm text-gray-600 mb-2">Jawaban Siswa:</p>
                                        <div class="text-gray-900 whitespace-pre-wrap bg-white p-3 rounded border">{{ $userAnswers[$question->id] ?? 'Tidak ada jawaban' }}</div>
                                    </div>
                                    
                                    <!-- Score Input -->
                                    <div class="flex items-center gap-4 p-4 bg-indigo-50 rounded-lg">
                                        <label class="text-sm font-medium text-gray-700">Nilai:</label>
                                        <input type="number" 
                                               name="essay_scores[{{ $question->id }}]" 
                                               value="{{ old('essay_scores.' . $question->id, $essayScores[$question->id] ?? '') }}"
                                               min="0" 
                                               max="{{ $question->points }}"
                                               step="0.5"
                                               required
                                               class="w-24 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                        <span class="text-sm text-gray-600">/ {{ $question->points }} poin</span>
                                    </div>
                                @else
                                    <!-- Multiple Choice / True False -->
                                    @php
                                        $isCorrect = false;
                                        $selectedAnswerId = $userAnswers[$question->id] ?? null;
                                    @endphp
                                    
                                    <div class="space-y-2">
                                        @foreach($question->answers as $answer)
                                            @php
                                                $isSelected = $selectedAnswerId == $answer->id;
                                                if ($isSelected && $answer->is_correct) $isCorrect = true;
                                            @endphp
                                            <div class="flex items-center p-3 border rounded-lg
                                                @if($isSelected && $answer->is_correct) border-green-500 bg-green-50
                                                @elseif($isSelected && !$answer->is_correct) border-red-500 bg-red-50
                                                @elseif(!$isSelected && $answer->is_correct) border-green-500 bg-green-50
                                                @else border-gray-200
                                                @endif">
                                                <input type="radio" disabled @if($isSelected) checked @endif class="w-4 h-4 text-indigo-600">
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
                                    
                                    <div class="mt-3 p-3 rounded-lg {{ $isCorrect ? 'bg-green-50' : 'bg-red-50' }}">
                                        <p class="text-sm font-medium {{ $isCorrect ? 'text-green-800' : 'text-red-800' }}">
                                            {{ $isCorrect ? '✓ Benar' : '✗ Salah' }} - {{ $isCorrect ? $question->points : 0 }}/{{ $question->points }} poin
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex items-center justify-between">
                    <a href="{{ route('instructor.exams.attempts', $attempt->exam_id) }}" 
                       class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold shadow-md">
                        Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
