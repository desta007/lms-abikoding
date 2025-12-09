<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Instruktur</h1>
                <a href="{{ route('instructor.courses.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                    + Buat Kursus Baru
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <x-instructor.stat-card 
                    title="Total Siswa Terdaftar" 
                    :value="$stats['total_enrolled_students']" 
                    icon="users" 
                    color="blue" />
                
                <x-instructor.stat-card 
                    title="Total Pelajaran" 
                    :value="$stats['total_lessons']" 
                    icon="book" 
                    color="green" />
                
                <x-instructor.stat-card 
                    title="Total Kunjungan" 
                    :value="$stats['total_visits']" 
                    icon="eye" 
                    color="purple" />
                
                <x-instructor.stat-card 
                    title="Pengguna Aktif" 
                    :value="$stats['active_users']" 
                    icon="user-check" 
                    color="yellow" />
                
                <x-instructor.stat-card 
                    title="Quiz Aktif" 
                    :value="$stats['active_exams']" 
                    icon="clipboard-check" 
                    color="red" />
            </div>

            <!-- Recent Courses -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Kursus Anda</h2>
                    <a href="{{ route('instructor.courses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                        Lihat Semua →
                    </a>
                </div>
                
                @if($courses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kursus</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($courses as $course)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($course->is_published)
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Published</span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Draft</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $course->totalEnrollments() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('instructor.courses.edit', $course->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">Anda belum membuat kursus. <a href="{{ route('instructor.courses.create') }}" class="text-indigo-600 hover:text-indigo-900">Buat kursus pertama Anda</a></p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

