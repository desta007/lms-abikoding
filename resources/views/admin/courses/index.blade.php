<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Semua Kursus</h1>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" 
                      action="{{ route('admin.courses.index') }}" 
                      class="flex gap-4">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari kursus..." 
                           class="flex-1 rounded-md border-gray-300">
                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">Semua Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Courses List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if($courses->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thumbnail</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instruktur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
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
                                        <div class="text-sm text-gray-900">{{ $course->instructor->full_name ?? $course->instructor->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $course->instructor->email }}</div>
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
                                        {{ $course->totalEnrollments() }} siswa
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
                        <p class="text-gray-500">Belum ada kursus yang dibuat.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

