<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Kelola Kursus</h1>
                <a href="{{ route('instructor.courses.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Buat Kursus Baru
                </a>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" 
                      action="{{ route('instructor.courses.index') }}" 
                      data-ajax-search="true"
                      data-results-container=".courses-table-container"
                      data-loading-indicator=".search-loading"
                      class="flex gap-4">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           data-live-search="true"
                           placeholder="Cari kursus..." 
                           class="flex-1 rounded-md border-gray-300">
                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">Semua Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Filter
                    </button>
                </form>
                <div class="search-loading hidden mt-4 text-center">
                    <div class="inline-flex items-center text-gray-600">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mencari...
                    </div>
                </div>
            </div>

            <!-- Courses List -->
            <div class="bg-white rounded-lg shadow overflow-hidden courses-table-container">
                @if($courses->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thumbnail</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($courses as $course)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($course->thumbnail)
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-16 h-16 object-cover rounded">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded"></div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $course->subtitle }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ $course->category->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">{{ $course->level->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($course->is_published)
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Published</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($course->totalEnrollments() > 0)
                                            <a href="{{ route('instructor.courses.students', $course->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ $course->totalEnrollments() }} siswa
                                            </a>
                                        @else
                                            <span class="text-gray-400">0 siswa</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2 flex-wrap">
                                            <a href="{{ route('instructor.courses.show', $course->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                            <a href="{{ route('instructor.courses.edit', $course->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                            @if($course->totalEnrollments() > 0)
                                                <a href="{{ route('instructor.courses.students', $course->id) }}" 
                                                   class="text-blue-600 hover:text-blue-900" 
                                                   title="Lihat Daftar Siswa">
                                                    Siswa
                                                </a>
                                            @endif
                                            <form action="{{ route('instructor.courses.publish', $course->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="{{ $course->is_published ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900' }}">
                                                    {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('instructor.courses.destroy', $course->id) }}" 
                                                  method="POST" 
                                                  class="inline" 
                                                  data-confirm-delete="true"
                                                  data-confirm-message="Apakah Anda yakin ingin menghapus kursus ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4">
                        {{ $courses->links() }}
                    </div>
                @else
                    <div class="p-6 text-center">
                        <p class="text-gray-500">Anda belum membuat kursus.</p>
                        <a href="{{ route('instructor.courses.create') }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Buat Kursus Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

