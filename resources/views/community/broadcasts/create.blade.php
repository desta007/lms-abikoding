<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('community.broadcasts.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Broadcast
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Buat Broadcast Baru</h1>
                <p class="text-gray-600">Bagikan pembelajaran Anda melalui siaran langsung atau rekaman</p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <ul class="list-disc list-inside text-sm text-red-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Create Form -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <form action="{{ route('community.broadcasts.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      id="broadcast-form"
                      data-no-loading="true">
                    @csrf
                    
                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            Judul Broadcast <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               required
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Contoh: Belajar Hiragana Dasar">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Jelaskan tentang broadcast Anda...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Video URL -->
                    <div class="mb-6">
                        <label for="video_url" class="block text-sm font-semibold text-gray-700 mb-2">
                            URL Video (YouTube, Vimeo, dll)
                        </label>
                        <input type="url" 
                               id="video_url" 
                               name="video_url" 
                               value="{{ old('video_url') }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="https://youtube.com/watch?v=...">
                        @error('video_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Masukkan URL video dari YouTube, Vimeo, atau platform lainnya</p>
                    </div>

                    <!-- Thumbnail -->
                    <div class="mb-6">
                        <label for="thumbnail" class="block text-sm font-semibold text-gray-700 mb-2">
                            Thumbnail
                        </label>
                        <input type="file" 
                               id="thumbnail" 
                               name="thumbnail" 
                               accept="image/*"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('thumbnail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG. Maksimal 2MB</p>
                    </div>

                    <!-- Scheduled At -->
                    <div class="mb-6">
                        <label for="scheduled_at" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jadwalkan Siaran (Opsional)
                        </label>
                        <input type="datetime-local" 
                               id="scheduled_at" 
                               name="scheduled_at" 
                               value="{{ old('scheduled_at') }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('scheduled_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika ingin membuat siaran langsung sekarang</p>
                    </div>

                    <!-- Zoom Meeting Option -->
                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   id="is_zoom_meeting" 
                                   name="is_zoom_meeting" 
                                   value="1"
                                   {{ old('is_zoom_meeting') ? 'checked' : '' }}
                                   class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-gray-900">
                                Gunakan Zoom Meeting untuk siaran ini
                            </span>
                        </label>
                        <p class="mt-1 text-sm text-gray-500 ml-8">Zoom meeting akan dibuat otomatis saat broadcast dibuat</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('community.broadcasts.index') }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                                id="submit-btn"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                            Buat Broadcast
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('broadcast-form');
            const submitBtn = document.getElementById('submit-btn');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form submitting...');
                    // Disable submit button to prevent double submission
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Menyimpan...';
                    }
                    // Let form submit normally
                });
            }
        });
    </script>
</x-app-layout>

