<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('instructor.exams.index') }}" 
                   class="text-indigo-600 hover:text-indigo-800 mb-4 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Quiz
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $exam->title }}</h1>
                <p class="text-gray-600">
                    @if($exam->course)
                        Kursus: {{ $exam->course->title }}
                    @endif
                    @if($exam->chapter)
                        | Bab: {{ $exam->chapter->title }}
                    @endif
                </p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Exam Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block mb-1">Status</span>
                        @if($exam->is_active)
                            <span class="px-3 py-1 text-sm font-semibold bg-green-100 text-green-800 rounded">Aktif</span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold bg-gray-100 text-gray-800 rounded">Tidak Aktif</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block mb-1">Minimum Passing Score</span>
                        <span class="text-lg font-bold text-indigo-600">{{ $exam->minimum_passing_score }}%</span>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block mb-1">Total Pertanyaan</span>
                        <span class="text-lg font-bold text-gray-900">{{ $exam->questions->count() }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-gray-700 block mb-1">Total Attempts</span>
                        <span class="text-lg font-bold text-gray-900">{{ $exam->attempts->count() }}</span>
                    </div>
                </div>

                @if($exam->description)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <span class="text-sm font-semibold text-gray-700 block mb-2">Deskripsi</span>
                        <p class="text-gray-600">{{ $exam->description }}</p>
                    </div>
                @endif

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        @if($exam->duration_minutes)
                            <div>
                                <span class="font-semibold text-gray-700">Durasi:</span>
                                <span class="ml-2 text-gray-900">{{ $exam->duration_minutes }} menit</span>
                            </div>
                        @endif
                        @if($exam->start_date)
                            <div>
                                <span class="font-semibold text-gray-700">Tanggal Mulai:</span>
                                <span class="ml-2 text-gray-900">{{ $exam->start_date->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        @if($exam->end_date)
                            <div>
                                <span class="font-semibold text-gray-700">Tanggal Berakhir:</span>
                                <span class="ml-2 text-gray-900">{{ $exam->end_date->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex flex-wrap gap-2">
                        @if($exam->is_required_for_progression)
                            <span class="px-3 py-1 text-xs font-semibold bg-orange-100 text-orange-800 rounded">Wajib Lulus untuk Lanjut</span>
                        @endif
                        @if($exam->auto_complete_on_pass)
                            <span class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">Auto-complete saat Lulus</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('instructor.exams.questions', $exam->id) }}" 
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                        Kelola Pertanyaan
                    </a>
                    <a href="{{ route('instructor.exams.attempts', $exam->id) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                        Lihat Hasil Siswa ({{ $exam->attempts->count() }})
                    </a>
                    <a href="{{ route('instructor.exams.edit', $exam->id) }}" 
                       class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium transition-colors">
                        Edit Quiz
                    </a>
                </div>
            </div>

            <!-- Questions Summary -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Daftar Pertanyaan ({{ $exam->questions->count() }})</h2>
                    <a href="{{ route('instructor.exams.questions', $exam->id) }}" 
                       class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Kelola Pertanyaan →
                    </a>
                </div>

                @if($exam->questions->count() > 0)
                    <div class="space-y-3">
                        @foreach($exam->questions->sortBy('order') as $question)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
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
                                        <p class="text-gray-900 font-medium">{{ Str::limit($question->question_text, 100) }}</p>
                                        @if($question->answers->count() > 0)
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $question->answers->count() }} opsi jawaban
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">Belum ada pertanyaan. Tambahkan pertanyaan sekarang!</p>
                        <a href="{{ route('instructor.exams.questions', $exam->id) }}" 
                           class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                            Tambah Pertanyaan
                        </a>
                    </div>
                @endif
            </div>

            <!-- Recent Attempts -->
            @if($exam->attempts->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Attempt Terbaru ({{ $exam->attempts->count() }})</h2>
                        <a href="{{ route('instructor.exams.attempts', $exam->id) }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            Lihat Semua →
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach($exam->attempts->sortByDesc('submitted_at')->take(5) as $attempt)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $attempt->user->name ?? $attempt->user->email }}</p>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                            @if($attempt->submitted_at)
                                                <span>Dikirim: {{ $attempt->submitted_at->format('d M Y, H:i') }}</span>
                                            @endif
                                            @if($attempt->percentage !== null)
                                                <span>Skor: {{ number_format($attempt->percentage, 1) }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <span class="px-3 py-1 text-xs font-semibold rounded
                                            @if($attempt->status === 'passed') bg-green-100 text-green-800
                                            @elseif($attempt->status === 'failed') bg-red-100 text-red-800
                                            @elseif($attempt->status === 'graded') bg-blue-100 text-blue-800
                                            @elseif($attempt->status === 'submitted') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            @if($attempt->status === 'passed')
                                                Lulus
                                            @elseif($attempt->status === 'failed')
                                                Tidak Lulus
                                            @elseif($attempt->status === 'graded')
                                                Dinilai
                                            @elseif($attempt->status === 'submitted')
                                                Dikirim
                                            @else
                                                {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

