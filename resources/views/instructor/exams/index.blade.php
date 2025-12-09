<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Kelola Quiz</h1>
                <a href="{{ route('instructor.exams.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                    + Buat Quiz Baru
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Filter -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <form method="GET" action="{{ route('instructor.exams.index') }}" class="flex gap-4">
                    <div class="flex-1">
                        <select name="course" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Kursus</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="status" class="rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Exams List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if($exams->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kursus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bab</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passing Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertanyaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($exams as $exam)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $exam->title }}</div>
                                        @if($exam->description)
                                            <div class="text-sm text-gray-500 mt-1">{{ Str::limit($exam->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $exam->course->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($exam->chapter)
                                            <span class="text-sm text-gray-900">{{ $exam->chapter->title }}</span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">
                                            {{ $exam->minimum_passing_score }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $exam->questions->count() }} pertanyaan</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($exam->is_active)
                                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">Tidak Aktif</span>
                                        @endif
                                        @if($exam->is_required_for_progression)
                                            <span class="ml-2 px-2 py-1 text-xs font-semibold bg-orange-100 text-orange-800 rounded">Wajib Lulus</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('instructor.exams.questions', $exam->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                Kelola
                                            </a>
                                            <a href="{{ route('instructor.exams.show', $exam->id) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                Detail
                                            </a>
                                            <a href="{{ route('instructor.exams.attempts', $exam->id) }}" 
                                               class="text-green-600 hover:text-green-900">
                                                Hasil
                                            </a>
                                            <form action="{{ route('instructor.exams.destroy', $exam->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus quiz ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 mb-4">Belum ada quiz. Buat quiz pertama Anda!</p>
                        <a href="{{ route('instructor.exams.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                            + Buat Quiz Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

