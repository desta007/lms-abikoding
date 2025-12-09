<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Progress Siswa</h1>
                <p class="text-gray-600 mt-2">Kelola dan setujui progress siswa untuk kursus Anda</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('instructor.progress.index') }}" class="flex gap-4 flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <label for="course" class="block text-sm font-medium text-gray-700 mb-2">Kursus</label>
                        <select name="course" id="course" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Kursus</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pending" {{ request('status') == 'pending' || !request('status') ? 'selected' : '' }}>Pending Approval</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="quiz_passed" {{ request('status') == 'quiz_passed' ? 'selected' : '' }}>Lulus Quiz</option>
                            <option value="" {{ request('status') === '' && request()->has('status') ? 'selected' : '' }}>Semua</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Progress List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bab</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($progresses as $progress)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="progress_ids[]" value="{{ $progress->id }}" class="progress-checkbox rounded border-gray-300">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $progress->courseEnrollment->user->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $progress->courseEnrollment->course->title }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $progress->chapter->title ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $progress->chapterMaterial->title ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $progress->completion_method === 'quiz_passed' ? 'bg-blue-100 text-blue-800' : 
                                           ($progress->completion_method === 'instructor_approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                        @if($progress->completion_method === 'quiz_passed')
                                            Quiz Passed
                                        @elseif($progress->completion_method === 'instructor_approved')
                                            Instructor Approved
                                        @else
                                            Manual
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($progress->is_instructor_approved || $progress->completion_method === 'quiz_passed')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        @if(!$progress->is_instructor_approved && $progress->completion_method !== 'quiz_passed')
                                            <form action="{{ route('instructor.progress.approve', $progress->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">Setujui</button>
                                            </form>
                                            <form action="{{ route('instructor.progress.reject', $progress->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menolak progress ini?')">Tolak</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('instructor.progress.show', $progress->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada progress yang perlu disetujui
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            @if($progresses->count() > 0)
                <div class="mt-4 flex items-center justify-between">
                    <form id="bulk-approve-form" action="{{ route('instructor.progress.bulk-approve') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="progress_ids" id="bulk-progress-ids">
                        <button type="submit" id="bulk-approve-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Setujui yang Dipilih
                        </button>
                    </form>
                    <div class="text-sm text-gray-600">
                        {{ $progresses->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('select-all')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.progress-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkButton();
        });

        document.querySelectorAll('.progress-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkButton);
        });

        function updateBulkButton() {
            const checked = document.querySelectorAll('.progress-checkbox:checked');
            const btn = document.getElementById('bulk-approve-btn');
            const input = document.getElementById('bulk-progress-ids');
            
            if (checked.length > 0) {
                btn.disabled = false;
                input.value = Array.from(checked).map(cb => cb.value).join(',');
            } else {
                btn.disabled = true;
                input.value = '';
            }
        }

        document.getElementById('bulk-approve-form')?.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.progress-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('Pilih setidaknya satu progress untuk disetujui');
                return false;
            }
            const ids = Array.from(checked).map(cb => cb.value);
            // Create hidden inputs for each ID
            const input = document.getElementById('bulk-progress-ids');
            input.value = JSON.stringify(ids);
        });
    </script>
</x-app-layout>

