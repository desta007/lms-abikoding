<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <nav class="text-sm mb-2">
                        <a href="{{ route('instructor.exams.index') }}" class="text-indigo-600 hover:text-indigo-900">Quiz</a>
                        <span class="text-gray-400 mx-2">/</span>
                        <a href="{{ route('instructor.exams.show', $exam->id) }}" class="text-indigo-600 hover:text-indigo-900">{{ $exam->title }}</a>
                        <span class="text-gray-400 mx-2">/</span>
                        <span class="text-gray-500">Percobaan</span>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900">Percobaan Quiz: {{ $exam->title }}</h1>
                    <p class="text-gray-600 mt-2">Daftar semua percobaan siswa untuk quiz ini</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('instructor.exams.show', $exam->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-sm text-gray-500">Total Percobaan</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $attempts->count() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-sm text-gray-500">Lulus</div>
                    <div class="text-2xl font-bold text-green-600">{{ $attempts->where('status', 'passed')->count() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-sm text-gray-500">Tidak Lulus</div>
                    <div class="text-2xl font-bold text-red-600">{{ $attempts->where('status', 'failed')->count() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-sm text-gray-500">Rata-rata Nilai</div>
                    <div class="text-2xl font-bold text-indigo-600">
                        {{ $attempts->count() > 0 ? number_format($attempts->avg('percentage'), 1) : 0 }}%
                    </div>
                </div>
            </div>

            <!-- Attempts List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if($attempts->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Selesai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attempts as $attempt)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $attempt->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $attempt->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $attempt->score ?? 0 }}/{{ $attempt->total_points ?? 0 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium {{ $attempt->percentage >= $exam->passing_score ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $attempt->percentage ? number_format($attempt->percentage, 1) . '%' : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attempt->status === 'passed')
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                Lulus
                                            </span>
                                        @elseif($attempt->status === 'failed')
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Tidak Lulus
                                            </span>
                                        @elseif($attempt->status === 'in_progress')
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                Sedang Mengerjakan
                                            </span>
                                        @elseif($attempt->status === 'submitted')
                                            <span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">
                                                Menunggu Penilaian
                                            </span>
                                        @elseif($attempt->status === 'retake_requested')
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                Minta Ulang
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                                {{ ucfirst(str_replace('_', ' ', $attempt->status ?? 'unknown')) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $attempt->started_at ? $attempt->started_at->format('d M Y, H:i') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($attempt->status === 'submitted')
                                            <a href="{{ route('instructor.exams.grade-attempt', $attempt->id) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Nilai Essay
                                            </a>
                                        @elseif($attempt->status === 'retake_requested')
                                            <div class="flex gap-2">
                                                <form action="{{ route('instructor.exams.approve-retake', $attempt->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900 font-medium"
                                                            onclick="return confirm('Setujui permintaan ulang quiz untuk {{ $attempt->user->name }}?')">
                                                        Setujui
                                                    </button>
                                                </form>
                                                <button type="button" 
                                                        onclick="showRejectModal({{ $attempt->id }}, '{{ $attempt->user->name }}')"
                                                        class="text-red-600 hover:text-red-900 font-medium">
                                                    Tolak
                                                </button>
                                            </div>
                                        @elseif(in_array($attempt->status, ['passed', 'failed']))
                                            <a href="{{ route('instructor.exams.grade-attempt', $attempt->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 text-xs">
                                                Lihat Detail
                                            </a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="mt-4">Belum ada siswa yang mengerjakan quiz ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Permintaan Ulang Quiz</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Alasan penolakan untuk <span id="studentName"></span>:
                </p>
                <form id="rejectForm" method="POST">
                    @csrf
                    <textarea name="rejection_reason" 
                              rows="4" 
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Masukkan alasan penolakan..."
                              required></textarea>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" 
                                onclick="closeRejectModal()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showRejectModal(attemptId, studentName) {
            document.getElementById('studentName').textContent = studentName;
            document.getElementById('rejectForm').action = '{{ route("instructor.exams.reject-retake", ":id") }}'.replace(':id', attemptId);
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('rejectModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>
