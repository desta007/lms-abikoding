<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Permintaan Ulang Quiz</h1>
                    <p class="text-gray-600 mt-2">Kelola permintaan siswa untuk mengulang quiz yang tidak lulus</p>
                </div>
                @if($retakeRequests->count() > 0)
                    <div class="flex items-center">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                            {{ $retakeRequests->total() }} Permintaan Menunggu
                        </span>
                    </div>
                @endif
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

            <!-- Retake Requests List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if($retakeRequests->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kursus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diminta Pada</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($retakeRequests as $attempt)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $attempt->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $attempt->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $attempt->exam->course->title }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $attempt->exam->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <span class="font-medium text-red-600">
                                                {{ $attempt->percentage ? number_format($attempt->percentage, 1) . '%' : '-' }}
                                            </span>
                                            <div class="text-xs text-gray-500">
                                                ({{ $attempt->score ?? 0 }}/{{ $attempt->total_points ?? 0 }})
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $attempt->retake_requested_at ? $attempt->retake_requested_at->format('d M Y, H:i') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
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
                                            <a href="{{ route('instructor.exams.attempts', $attempt->exam_id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $retakeRequests->links() }}
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <p>Tidak ada permintaan ulang quiz yang menunggu persetujuan.</p>
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

