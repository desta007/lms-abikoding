<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Administrasi • Pengguna</p>
                    <h1 class="text-3xl font-bold text-gray-900 mt-1">Kelola Pengguna</h1>
                    <p class="text-gray-600 mt-2">Kelola semua pengguna sistem, termasuk siswa, instruktur, dan admin. Ubah peran pengguna sesuai kebutuhan.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Dashboard</a>
                    <a href="{{ route('admin.users.create-instructor') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Buat Instruktur
                    </a>
                </div>
            </div>

            <!-- Role Filter Tabs -->
            <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.index', ['role' => '']) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('role') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        Semua ({{ $roleCounts['all'] }})
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'student']) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('role') === 'student' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        Siswa ({{ $roleCounts['student'] }})
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'instructor']) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('role') === 'instructor' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        Instruktur ({{ $roleCounts['instructor'] }})
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('role') === 'admin' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        Admin ({{ $roleCounts['admin'] }})
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <form method="GET" class="flex flex-col md:flex-row md:items-center gap-4">
                        <input type="hidden" name="role" value="{{ request('role') }}">
                        <div class="flex-1">
                            <label class="sr-only" for="search">Cari pengguna</label>
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
                            <a href="{{ route('admin.users.index', ['role' => request('role')]) }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Terdaftar</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-400 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($user->full_name ?? $user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $user->full_name ?? $user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->role === 'admin')
                                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                                Admin
                                            </span>
                                        @elseif($user->role === 'instructor')
                                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                                Instruktur
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                                Siswa
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $user->email_verified_at ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $user->email_verified_at ? 'Terverifikasi' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $user->created_at->format('d M Y') }}
                                        <span class="block text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-600 hover:border-indigo-500 hover:text-indigo-600">
                                                Detail
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-600 hover:border-indigo-500 hover:text-indigo-600">
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <p class="font-semibold text-gray-700">Belum ada pengguna yang ditemukan.</p>
                                        <p class="text-sm mt-1">Coba ubah filter atau kata kunci pencarian.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

