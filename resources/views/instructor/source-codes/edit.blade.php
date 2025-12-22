<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-emerald-600">
                    <h1 class="text-2xl font-bold text-white">Edit Source Code</h1>
                </div>

                <form action="{{ route('instructor.source-codes.update', $sourceCode->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul *</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $sourceCode->title) }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subtitle -->
                    <div>
                        <label for="subtitle" class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $sourceCode->subtitle) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('subtitle')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                        @if($sourceCode->thumbnail)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $sourceCode->thumbnail) }}" alt="{{ $sourceCode->title }}" class="w-32 h-32 object-cover rounded">
                            </div>
                        @endif
                        <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('thumbnail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Category -->
                        <div>
                            <label for="source_code_category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                            <select name="source_code_category_id" id="source_code_category_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('source_code_category_id', $sourceCode->source_code_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('source_code_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Level -->
                        <div>
                            <label for="level_id" class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                            <select name="level_id" id="level_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Pilih Level</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ old('level_id', $sourceCode->level_id) == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('level_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                        <input type="number" name="price" id="price" value="{{ old('price', $sourceCode->price) }}" min="0" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <p class="mt-1 text-xs text-gray-500">Masukkan 0 untuk gratis</p>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Technologies -->
                    <div>
                        <label for="technologies" class="block text-sm font-medium text-gray-700 mb-1">Teknologi</label>
                        <input type="text" name="technologies" id="technologies" 
                               value="{{ old('technologies', is_array($sourceCode->technologies) ? implode(', ', $sourceCode->technologies) : $sourceCode->technologies) }}"
                               placeholder="Laravel, Vue.js, TailwindCSS (pisahkan dengan koma)"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('technologies')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- URLs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="github_url" class="block text-sm font-medium text-gray-700 mb-1">GitHub URL</label>
                            <input type="url" name="github_url" id="github_url" value="{{ old('github_url', $sourceCode->github_url) }}"
                                   placeholder="https://github.com/..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('github_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="demo_url" class="block text-sm font-medium text-gray-700 mb-1">Demo URL</label>
                            <input type="url" name="demo_url" id="demo_url" value="{{ old('demo_url', $sourceCode->demo_url) }}"
                                   placeholder="https://demo.example.com"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('demo_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="download_url" class="block text-sm font-medium text-gray-700 mb-1">Download URL</label>
                            <input type="url" name="download_url" id="download_url" value="{{ old('download_url', $sourceCode->download_url) }}"
                                   placeholder="https://download.example.com/..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('download_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
                        <textarea name="description" id="description" rows="6" required
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $sourceCode->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('instructor.source-codes.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
