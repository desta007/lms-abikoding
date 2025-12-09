<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('instructor.exams.index') }}" 
                   class="text-indigo-600 hover:text-indigo-800 mb-4 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Quiz
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Kelola Pertanyaan: {{ $exam->title }}</h1>
                <p class="text-gray-600">
                    @if($exam->chapter)
                        Bab: {{ $exam->chapter->title }}
                    @endif
                    @if($exam->course)
                        | Kursus: {{ $exam->course->title }}
                    @endif
                </p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <ul class="list-disc list-inside text-sm text-red-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Exam Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-semibold text-gray-700">Minimum Passing Score:</span>
                        <span class="ml-2 text-gray-900">{{ $exam->minimum_passing_score }}%</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Durasi:</span>
                        <span class="ml-2 text-gray-900">{{ $exam->duration_minutes ? $exam->duration_minutes . ' menit' : 'Tidak ada batas' }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Total Pertanyaan:</span>
                        <span class="ml-2 text-gray-900">{{ $exam->questions->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add Question Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Tambah Pertanyaan</h2>
                        
                        <form action="{{ route('instructor.exams.questions.add', $exam->id) }}" method="POST">
                            @csrf
                            
                            <!-- Question Text -->
                            <div class="mb-4">
                                <label for="question_text" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Pertanyaan <span class="text-red-500">*</span>
                                </label>
                                <textarea id="question_text" 
                                          name="question_text" 
                                          rows="3"
                                          required
                                          class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                          placeholder="Masukkan pertanyaan..."></textarea>
                            </div>

                            <!-- Question Type -->
                            <div class="mb-4">
                                <label for="question_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tipe Pertanyaan <span class="text-red-500">*</span>
                                </label>
                                <select id="question_type" 
                                        name="question_type" 
                                        required
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                        onchange="toggleAnswerFields()">
                                    <option value="multiple_choice">Pilihan Ganda</option>
                                    <option value="true_false">Benar/Salah</option>
                                    <option value="essay">Essay</option>
                                </select>
                            </div>

                            <!-- Points -->
                            <div class="mb-4">
                                <label for="points" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Poin <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       id="points" 
                                       name="points" 
                                       value="1"
                                       min="1"
                                       required
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Answers Section (for multiple_choice and true_false) -->
                            <div id="answers-section" class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Jawaban <span class="text-red-500">*</span>
                                </label>
                                
                                <div id="answers-container" class="space-y-2">
                                    <!-- Answers will be dynamically added here -->
                                </div>
                                
                                <button type="button" 
                                        onclick="addAnswerOption()" 
                                        class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                    + Tambah Opsi Jawaban
                                </button>
                            </div>

                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                                Tambah Pertanyaan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Questions List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Daftar Pertanyaan ({{ $exam->questions->count() }})</h2>
                        
                        @if($exam->questions->count() > 0)
                            <div class="space-y-4">
                                @foreach($exam->questions->sortBy('order') as $question)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="px-2 py-1 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded">
                                                        #{{ $question->order }}
                                                    </span>
                                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                                                        {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                                    </span>
                                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">
                                                        {{ $question->points }} poin
                                                    </span>
                                                </div>
                                                <p class="text-gray-900 font-medium mb-2">{{ $question->question_text }}</p>
                                                
                                                @if($question->answers->count() > 0)
                                                    <div class="mt-3 space-y-1">
                                                        @foreach($question->answers as $answer)
                                                            <div class="flex items-center gap-2 text-sm">
                                                                @if($answer->is_correct)
                                                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span class="text-green-700 font-medium">{{ $answer->answer_text }}</span>
                                                                @else
                                                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span class="text-gray-600">{{ $answer->answer_text }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-sm text-gray-500 italic">(Essay - tidak ada opsi jawaban)</p>
                                                @endif
                                            </div>
                                            <form action="{{ route('instructor.exams.questions.delete', [$exam->id, $question->id]) }}" 
                                                  method="POST" 
                                                  class="ml-4"
                                                  onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded hover:bg-red-200">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-gray-500 mb-4">Belum ada pertanyaan. Tambahkan pertanyaan pertama!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let answerCount = 0;

        function toggleAnswerFields() {
            const questionType = document.getElementById('question_type').value;
            const answersSection = document.getElementById('answers-section');
            const answersContainer = document.getElementById('answers-container');
            
            if (questionType === 'multiple_choice' || questionType === 'true_false') {
                answersSection.style.display = 'block';
                
                // Clear existing answers
                answersContainer.innerHTML = '';
                answerCount = 0;
                
                // Add initial answers
                if (questionType === 'true_false') {
                    addAnswerOption('Benar', true);
                    addAnswerOption('Salah', false);
                } else {
                    addAnswerOption();
                    addAnswerOption();
                }
            } else {
                answersSection.style.display = 'none';
                answersContainer.innerHTML = '';
                answerCount = 0;
            }
        }

        function addAnswerOption(text = '', isCorrect = false) {
            answerCount++;
            const container = document.getElementById('answers-container');
            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 p-2 bg-gray-50 rounded';
            div.innerHTML = `
                <input type="text" 
                       name="answers[${answerCount}][text]" 
                       value="${text}"
                       placeholder="Teks jawaban"
                       required
                       class="flex-1 rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <label class="flex items-center cursor-pointer">
                    <input type="hidden" 
                           name="answers[${answerCount}][is_correct]" 
                           value="${isCorrect ? '1' : '0'}"
                           class="is-correct-hidden">
                    <input type="checkbox" 
                           value="1"
                           ${isCorrect ? 'checked' : ''}
                           onchange="this.previousElementSibling.value = this.checked ? '1' : '0'"
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-xs text-gray-700">Benar</span>
                </label>
                <button type="button" 
                        onclick="this.parentElement.remove()" 
                        class="text-red-600 hover:text-red-800 text-sm">
                    ✕
                </button>
            `;
            container.appendChild(div);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleAnswerFields();
        });
    </script>
</x-app-layout>

