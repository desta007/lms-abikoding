<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Tambah Materi</h1>
                        <p class="text-gray-600 mt-2">Bab: {{ $chapter->title }}</p>
                        <p class="text-sm text-gray-500 mt-1">Kursus: {{ $chapter->course->title }}</p>
                    </div>
                    <a href="{{ route('instructor.courses.show', $chapter->course_id) }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Kembali
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-600 mb-6">Anda dapat menambahkan satu atau lebih jenis materi sekaligus. Semua input bersifat opsional, namun minimal satu jenis materi harus diisi.</p>
                
                <form action="{{ route('instructor.materials.store', $chapter->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Video URL Input -->
                    <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                        <label for="material_video_url" class="block text-sm font-semibold text-gray-700 mb-2">
                            Video URL (Opsional)
                        </label>
                        <input type="url" 
                               id="material_video_url" 
                               name="video_url" 
                               value="{{ old('video_url') }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="https://www.youtube.com/watch?v=... atau URL video lainnya">
                        <p class="mt-1 text-xs text-gray-500">Masukkan URL video (YouTube, Vimeo, dll)</p>
                        @error('video_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Video File Input -->
                    <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-blue-50">
                        <label for="material_video_file" class="block text-sm font-semibold text-gray-700 mb-2">
                            Upload Video dari Komputer (Opsional)
                        </label>
                        <input type="file" 
                               id="material_video_file" 
                               name="video_file"
                               accept="video/*,.mp4,.avi,.mov,.wmv,.flv,.webm"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Format: MP4, AVI, MOV, WMV, FLV, WebM. Maksimal 50MB</p>
                        @error('video_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PDF File Input -->
                    <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                        <label for="material_pdf_file" class="block text-sm font-semibold text-gray-700 mb-2">
                            File PDF (Opsional)
                        </label>
                        <input type="file" 
                               id="material_pdf_file" 
                               name="pdf_file"
                               accept=".pdf,application/pdf"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF. Maksimal 10MB</p>
                        @error('pdf_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Text Content Input -->
                    <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                        <label for="material_text_content" class="block text-sm font-semibold text-gray-700 mb-2">
                            Konten Teks (Opsional)
                        </label>
                        <textarea id="material_text_content" 
                                  name="text_content" 
                                  rows="8"
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Tulis konten teks materi di sini...">{{ old('text_content') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Masukkan konten teks untuk materi</p>
                        @error('text_content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image File Input -->
                    <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                        <label for="material_image_file" class="block text-sm font-semibold text-gray-700 mb-2">
                            File Gambar (Opsional)
                        </label>
                        <input type="file" 
                               id="material_image_file" 
                               name="image_file"
                               accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 5MB</p>
                        @error('image_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title Input (Optional, will be auto-generated if not provided) -->
                    <div class="mb-6">
                        <label for="material_title" class="block text-sm font-semibold text-gray-700 mb-2">
                            Judul Materi (Opsional)
                        </label>
                        <input type="text" 
                               id="material_title" 
                               name="title" 
                               value="{{ old('title') }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Judul materi (akan dibuat otomatis jika dikosongkan)">
                        <p class="mt-1 text-xs text-gray-500">Jika dikosongkan, judul akan dibuat otomatis berdasarkan jenis materi yang diinput</p>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-800 font-semibold mb-2">Terjadi kesalahan:</p>
                            <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('instructor.courses.show', $chapter->course_id) }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                            Tambah Materi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

