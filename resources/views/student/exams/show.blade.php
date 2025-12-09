<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $exam->title }}</h1>
                <p class="text-gray-600 mt-2">{{ $exam->course->title }}</p>
            </div>

            @if($exam->description)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">{{ $exam->description }}</p>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($exam->duration_minutes)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-gray-600">Durasi: <strong>{{ $exam->duration_minutes }} menit</strong></span>
                        </div>
                    @endif
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm text-gray-600">{{ $exam->questions()->count() }} pertanyaan</span>
                    </div>
                    @if($exam->end_date)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm text-gray-600">Berakhir: {{ $exam->end_date->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <form action="{{ route('student.exams.submit', $exam->id) }}" method="POST" id="examForm">
                @csrf
                
                <div class="space-y-6">
                    @foreach($exam->questions as $index => $question)
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
                                @endphp
                                @if($question->question_type === 'multiple_choice')
                                    <div class="space-y-2">
                                        @foreach($question->answers as $answer)
                                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                <input type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ $answer->id }}"
                                                       class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                                                       @if(isset($userAnswers[$question->id]) && $userAnswers[$question->id] == $answer->id) checked @endif>
                                                <span class="ml-3 text-gray-700">{{ $answer->answer_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($question->question_type === 'true_false')
                                    <div class="space-y-2">
                                        @foreach($question->answers as $answer)
                                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                <input type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ $answer->id }}"
                                                       class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                                                       @if(isset($userAnswers[$question->id]) && $userAnswers[$question->id] == $answer->id) checked @endif>
                                                <span class="ml-3 text-gray-700">{{ $answer->answer_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($question->question_type === 'essay')
                                    <textarea name="answers[{{ $question->id }}]" 
                                              rows="5" 
                                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                              placeholder="Tulis jawaban Anda di sini...">@if(isset($userAnswers[$question->id])){{ $userAnswers[$question->id] }}@endif</textarea>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            Pastikan semua pertanyaan telah dijawab sebelum mengirimkan quiz.
                        </p>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
                                onclick="return confirm('Apakah Anda yakin ingin mengirimkan quiz? Pastikan semua pertanyaan telah dijawab.')">
                            Kirim Quiz
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

