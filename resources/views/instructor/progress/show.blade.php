<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('instructor.progress.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Progress
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Detail Progress Siswa</h1>
                <p class="text-gray-600 mt-2">Informasi lengkap tentang progress siswa pada materi kursus</p>
            </div>

            <!-- Progress Details Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <!-- Student Information -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Siswa</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Nama Siswa</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $progress->courseEnrollment->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $progress->courseEnrollment->user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Course Information -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kursus</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Kursus</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $progress->courseEnrollment->course->title }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Bab</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $progress->chapter->title ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Materi</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $progress->chapterMaterial->title ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Tipe Materi</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                        {{ strtoupper($progress->chapterMaterial->material_type ?? 'N/A') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Status -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Progress</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status Completion</label>
                                <p class="mt-1">
                                    @if($progress->is_completed)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            Belum Selesai
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Metode Completion</label>
                                <p class="mt-1">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $progress->completion_method === 'quiz_passed' ? 'bg-blue-100 text-blue-800' : 
                                           ($progress->completion_method === 'instructor_approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                        @if($progress->completion_method === 'quiz_passed')
                                            Lulus Quiz
                                        @elseif($progress->completion_method === 'instructor_approved')
                                            Disetujui Instruktur
                                        @else
                                            Manual
                                        @endif
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status Approval</label>
                                <p class="mt-1">
                                    @if($progress->is_instructor_approved || $progress->completion_method === 'quiz_passed')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            Menunggu Persetujuan
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Progress Percentage</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $progress->progress_percentage }}%</p>
                            </div>
                            @if($progress->completed_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Waktu Selesai</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $progress->completed_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                            @if($progress->approved_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Waktu Disetujui</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $progress->approved_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                            @if($progress->approvedBy)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Disetujui Oleh</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $progress->approvedBy->name }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quiz Information (if applicable) -->
                    @if($progress->quizAttempt && $progress->quizAttempt->exam)
                        <div class="mb-6 pb-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Quiz</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Judul Quiz</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $progress->quizAttempt->exam->title }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Status</label>
                                    <p class="mt-1">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $progress->quizAttempt->status === 'passed' ? 'bg-green-100 text-green-800' : 
                                               ($progress->quizAttempt->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($progress->quizAttempt->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Skor</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $progress->quizAttempt->score }}%</p>
                                </div>
                                @if($progress->quizAttempt->exam->minimum_passing_score)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Minimum Passing Score</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $progress->quizAttempt->exam->minimum_passing_score }}%</p>
                                    </div>
                                @endif
                                @if($progress->quizAttempt->completed_at)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Waktu Selesai Quiz</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $progress->quizAttempt->completed_at->format('d M Y H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6">
                        <div class="flex gap-3">
                            @if(!$progress->is_instructor_approved && $progress->completion_method !== 'quiz_passed')
                                <form action="{{ route('instructor.progress.approve', $progress->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                                        Setujui Progress
                                    </button>
                                </form>
                                <form action="{{ route('instructor.progress.reject', $progress->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors" onclick="return confirm('Yakin ingin menolak progress ini? Progress akan direset.')">
                                        Tolak Progress
                                    </button>
                                </form>
                            @else
                                <div class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg">
                                    Progress sudah disetujui atau lulus quiz
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('instructor.progress.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

