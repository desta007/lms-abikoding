<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Kelola Source Code</h1>
                <a href="{{ route('instructor.source-codes.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">
                    Buat Source Code Baru
                </a>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" 
                      action="{{ route('instructor.source-codes.index') }}" 
                      data-ajax-search="true"
                      data-results-container=".source-codes-table-container"
                      data-loading-indicator=".search-loading"
                      class="flex gap-4">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           data-live-search="true"
                           placeholder="Cari source code..." 
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

            <!-- Source Codes List -->
            <div class="bg-white rounded-lg shadow overflow-hidden source-codes-table-container">
                @if($sourceCodes->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thumbnail</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sourceCodes as $sourceCode)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($sourceCode->thumbnail)
                                            <img src="{{ asset('storage/' . $sourceCode->thumbnail) }}" alt="{{ $sourceCode->title }}" class="w-16 h-16 object-cover rounded">
                                        @else
                                            <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-cyan-400 rounded flex items-center justify-center">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $sourceCode->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $sourceCode->subtitle }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs bg-emerald-100 text-emerald-800 rounded">{{ $sourceCode->category->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ $sourceCode->level->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($sourceCode->price == 0)
                                            <span class="text-green-600 font-medium">Gratis</span>
                                        @else
                                            <span class="text-gray-900">Rp {{ number_format($sourceCode->price, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($sourceCode->is_published)
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Published</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2 flex-wrap">
                                            <a href="{{ route('instructor.source-codes.show', $sourceCode->id) }}" class="text-emerald-600 hover:text-emerald-900">Lihat</a>
                                            <a href="{{ route('instructor.source-codes.edit', $sourceCode->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                            <form action="{{ route('instructor.source-codes.publish', $sourceCode->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="{{ $sourceCode->is_published ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900' }}">
                                                    {{ $sourceCode->is_published ? 'Unpublish' : 'Publish' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('instructor.source-codes.destroy', $sourceCode->id) }}" 
                                                  method="POST" 
                                                  class="inline" 
                                                  data-confirm-delete="true"
                                                  data-confirm-message="Apakah Anda yakin ingin menghapus source code ini?">
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
                        {{ $sourceCodes->links() }}
                    </div>
                @else
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        <p class="text-gray-500 mb-4">Anda belum membuat source code.</p>
                        <a href="{{ route('instructor.source-codes.create') }}" class="inline-block px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">
                            Buat Source Code Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
