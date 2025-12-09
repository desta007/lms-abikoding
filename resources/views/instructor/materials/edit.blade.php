<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Materi</h1>
                        <p class="text-gray-600 mt-2">Bab: {{ $material->chapter->title }}</p>
                        <p class="text-sm text-gray-500 mt-1">Kursus: {{ $material->chapter->course->title }}</p>
                    </div>
                    <a href="{{ route('instructor.courses.show', $material->chapter->course_id) }}" 
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
                <p class="text-sm text-gray-600 mb-6">Anda dapat mengubah atau menambahkan jenis materi. Semua input bersifat opsional, namun minimal satu jenis materi harus diisi.</p>
                
                <form action="{{ route('instructor.materials.update', $material->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Video URL Input -->
                    <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                        <label for="material_video_url" class="block text-sm font-semibold text-gray-700 mb-2">
                            Video URL (Opsional)
                        </label>
                        @if($material->video_url)
                            <div class="mb-2 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-600 mb-1">URL saat ini:</p>
                                <p class="text-sm text-gray-800 break-all">{{ $material->video_url }}</p>
                            </div>
                        @endif
                        <input type="url" 
                               id="material_video_url" 
                               name="video_url" 
                               value="{{ old('video_url', $material->video_url) }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="https://www.youtube.com/watch?v=... atau URL video lainnya">
                        <p class="mt-1 text-xs text-gray-500">Masukkan URL video (YouTube, Vimeo, dll). Kosongkan jika tidak ingin mengubah.</p>
                        @error('video_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Video File Input -->
                    <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-blue-50">
                        <label for="material_video_file" class="block text-sm font-semibold text-gray-700 mb-2">
                            Upload Video dari Komputer (Opsional)
                        </label>
                        @if($material->video_file_path)
                            <div class="mb-2 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-600 mb-1">Video saat ini:</p>
                                <div class="flex items-center gap-2">
                                    <video controls class="max-w-xs rounded-lg border border-gray-300" preload="metadata">
                                        <source src="{{ asset('storage/' . $material->video_file_path) }}" type="video/mp4">
                                        Browser Anda tidak mendukung video tag.
                                    </video>
                                    <div>
                                        <p class="text-sm text-gray-800">{{ basename($material->video_file_path) }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($material->video_file_size / 1024 / 1024, 2) }} MB</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <input type="file" 
                               id="material_video_file" 
                               name="video_file"
                               accept="video/*,.mp4,.avi,.mov,.wmv,.flv,.webm"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Format: MP4, AVI, MOV, WMV, FLV, WebM. Maksimal 50MB. Kosongkan jika tidak ingin mengubah file.</p>
                        @error('video_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PDF File Input -->
                    <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                        <label for="material_pdf_file" class="block text-sm font-semibold text-gray-700 mb-2">
                            File PDF (Opsional)
                        </label>
                        @if($material->pdf_file_path)
                            <div class="mb-2 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-600 mb-1">File saat ini:</p>
                                <a href="{{ asset('storage/' . $material->pdf_file_path) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                                    {{ basename($material->pdf_file_path) }}
                                </a>
                            </div>
                        @endif
                        <input type="file" 
                               id="material_pdf_file" 
                               name="pdf_file"
                               accept=".pdf,application/pdf"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF. Maksimal 10MB. Kosongkan jika tidak ingin mengubah file.</p>
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
                                  placeholder="Tulis konten teks materi di sini...">{{ old('text_content', $material->text_content) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Masukkan konten teks untuk materi. Kosongkan jika tidak ingin mengubah.</p>
                        @error('text_content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image File Input -->
                    <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                        <label for="material_image_file" class="block text-sm font-semibold text-gray-700 mb-2">
                            File Gambar (Opsional)
                        </label>
                        @if($material->image_file_path)
                            <div class="mb-2 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-600 mb-2">Gambar saat ini:</p>
                                <img src="{{ asset('storage/' . $material->image_file_path) }}" 
                                     alt="Current image" 
                                     class="max-w-xs rounded-lg border border-gray-300">
                            </div>
                        @endif
                        <input type="file" 
                               id="material_image_file" 
                               name="image_file"
                               accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 5MB. Kosongkan jika tidak ingin mengubah file.</p>
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
                               value="{{ old('title', $material->title) }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Judul materi">
                        <p class="mt-1 text-xs text-gray-500">Ubah judul materi jika diperlukan</p>
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
                        <a href="{{ route('instructor.courses.show', $material->chapter->course_id) }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

