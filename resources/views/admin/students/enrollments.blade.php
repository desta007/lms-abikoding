<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <a href="{{ route('admin.students.show', $student->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Kembali</a>
                    <h1 class="text-3xl font-bold text-gray-900 mt-2">Kursus {{ $student->full_name ?? $student->name }}</h1>
                    <p class="text-gray-600 mt-1">Daftar pendaftaran kursus dan status penyelesaian.</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                    Total: {{ number_format($enrollments->total()) }} kursus
                </span>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Kursus</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Bergabung</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($enrollments as $enrollment)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $enrollment->course->title }}</p>
                                        <p class="text-xs text-gray-500">Instruktur: {{ $enrollment->course->instructor->full_name ?? $enrollment->course->instructor->name }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $enrollment->course->category->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $enrollment->course->level->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $enrollment->completed_at ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $enrollment->completed_at ? 'Selesai' : 'Sedang Belajar' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $enrollment->created_at->format('d M Y') }}
                                        <span class="block text-xs text-gray-400">{{ $enrollment->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $enrollment->completed_at ? $enrollment->completed_at->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <p class="font-semibold text-gray-700">Belum ada pendaftaran kursus.</p>
                                        <p class="text-sm mt-1">Siswa ini belum mengikuti kursus apa pun.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $enrollments->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

