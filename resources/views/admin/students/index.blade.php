<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Administrasi • Siswa</p>
                    <h1 class="text-3xl font-bold text-gray-900 mt-1">Kelola Siswa</h1>
                    <p class="text-gray-600 mt-2">Pantau perkembangan siswa, verifikasi akun, dan kelola status mereka.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Dashboard</a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <form method="GET" class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-1">
                            <label class="sr-only" for="search">Cari siswa</label>
                            <div class="relative">
                                <input id="search" name="search" value="{{ request('search') }}" type="search" placeholder="Cari berdasarkan nama atau email..." class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                            </div>
                        </div>
                        <div>
                            <label for="status" class="sr-only">Status verifikasi</label>
                            <select id="status" name="status" class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2">
                                <option value="">Semua Status</option>
                                <option value="verified" @selected(request('status') === 'verified')>Terverifikasi</option>
                                <option value="unverified" @selected(request('status') === 'unverified')>Belum Verifikasi</option>
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                            Terapkan
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Pendaftaran</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Terdaftar</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-400 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($student->full_name ?? $student->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $student->full_name ?? $student->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $student->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ number_format($student->enrollments->count()) }} kursus
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $student->email_verified_at ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $student->email_verified_at ? 'Terverifikasi' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $student->created_at->format('d M Y') }}
                                        <span class="block text-xs text-gray-400">{{ $student->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.students.show', $student->id) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-600 hover:border-indigo-500 hover:text-indigo-600">
                                                Detail
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student->id) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-600 hover:border-indigo-500 hover:text-indigo-600">
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <p class="font-semibold text-gray-700">Belum ada siswa yang terdaftar.</p>
                                        <p class="text-sm mt-1">Siswa baru akan muncul di sini setelah mendaftar platform.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $students->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

