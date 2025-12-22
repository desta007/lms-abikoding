<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Edit Kursus</h1>
                <p class="text-gray-600 mt-2">{{ $course->title }}</p>
            </div>

            <form action="{{ route('instructor.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" id="courseForm">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Dasar</h2>
                    
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                Judul Kursus <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $course->title) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Contoh: Flutter Dasar untuk Pemula">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subtitle -->
                        <div>
                            <label for="subtitle" class="block text-sm font-semibold text-gray-700 mb-2">
                                Subjudul Kursus <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="subtitle" 
                                   name="subtitle" 
                                   value="{{ old('subtitle', $course->subtitle) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Deskripsi singkat kursus">
                            @error('subtitle')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thumbnail -->
                        <div>
                            <label for="thumbnail" class="block text-sm font-semibold text-gray-700 mb-2">
                                Thumbnail
                            </label>
                            @if($course->thumbnail)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Current thumbnail" class="max-w-xs rounded-lg border border-gray-300">
                                </div>
                            @endif
                            <input type="file" 
                                   id="thumbnail" 
                                   name="thumbnail" 
                                   accept="image/*"
                                   onchange="previewThumbnail(this)"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <div id="thumbnailPreview" class="mt-4 hidden">
                                <img id="previewImg" src="" alt="Preview" class="max-w-xs rounded-lg border border-gray-300">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG. Maksimal 2MB. Biarkan kosong jika tidak ingin mengubah thumbnail</p>
                            @error('thumbnail')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category and Level -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select id="category_id" 
                                        name="category_id" 
                                        required
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="level_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tingkat Instruksional <span class="text-red-500">*</span>
                                </label>
                                <select id="level_id" 
                                        name="level_id" 
                                        required
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Pilih Level</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('level_id', $course->level_id) == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('level_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Price and Language -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Harga (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', $course->price) }}"
                                       min="0"
                                       step="1000"
                                       required
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="0">
                                <p class="mt-1 text-sm text-gray-500">Masukkan 0 untuk kursus gratis</p>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="language" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Bahasa <span class="text-red-500">*</span>
                                </label>
                                <select id="language" 
                                        name="language" 
                                        required
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Indonesian" {{ old('language', $course->language) == 'Indonesian' ? 'selected' : '' }}>Indonesian</option>
                                    <option value="English" {{ old('language', $course->language) == 'English' ? 'selected' : '' }}>English</option>
                                </select>
                                @error('language')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- About Course -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Tentang Kursus</h2>
                    <div>
                        <label for="about_course" class="block text-sm font-semibold text-gray-700 mb-2">
                            Deskripsi Lengkap Kursus <span class="text-red-500">*</span>
                        </label>
                        <textarea id="about_course" 
                                  name="about_course" 
                                  rows="10"
                                  required
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Jelaskan secara detail tentang kursus ini...">{{ old('about_course', $course->about_course) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Gunakan format HTML atau teks biasa</p>
                        @error('about_course')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- About Instructor -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Tentang Instruktur</h2>
                    <div>
                        <label for="about_instructor" class="block text-sm font-semibold text-gray-700 mb-2">
                            Profil Instruktur <span class="text-red-500">*</span>
                        </label>
                        <textarea id="about_instructor" 
                                  name="about_instructor" 
                                  rows="8"
                                  required
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Ceritakan tentang diri Anda sebagai instruktur...">{{ old('about_instructor', $course->about_instructor) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Gunakan format HTML atau teks biasa</p>
                        @error('about_instructor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('instructor.courses.index') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                        <span class="button-text">Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Thumbnail preview
        function previewThumbnail(input) {
            const preview = document.getElementById('thumbnailPreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>

