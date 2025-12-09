<x-app-layout>
    <!-- Course Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-sm font-semibold rounded-full">
                            {{ $course->category->name }}
                        </span>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-sm font-semibold rounded-full">
                            {{ $course->level->name }}
                        </span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ $course->title }}</h1>
                    <p class="text-xl text-white/90 mb-6">{{ $course->subtitle }}</p>
                    
                    <div class="flex flex-wrap items-center gap-6 mb-6">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= round($course->average_rating) ? 'text-yellow-300' : 'text-white/40' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-white font-semibold">{{ number_format($course->average_rating, 1) }}</span>
                            <span class="text-white/80 text-sm">({{ $course->ratings->count() }} ulasan)</span>
                        </div>
                        <div class="flex items-center gap-2 text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="font-semibold">{{ $course->totalEnrollments() }}</span>
                            <span class="text-sm">peserta</span>
                        </div>
                        <div class="flex items-center gap-2 text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>{{ $course->instructor->full_name ?? $course->instructor->name }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-2xl p-6">
                        <div class="text-center mb-6">
                            @if($course->isFree())
                                <div class="text-4xl font-bold text-green-600 mb-2">Gratis</div>
                            @else
                                <div class="text-4xl font-bold text-indigo-600 mb-2">Rp {{ number_format($course->price, 0, ',', '.') }}</div>
                            @endif
                        </div>
                        
                        @if(!$isEnrolled)
                            <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:scale-105 mb-4">
                                    <span class="button-text">
                                        @if($course->isFree())
                                            Daftar Gratis Sekarang
                                        @else
                                            Beli Sekarang
                                        @endif
                                    </span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('courses.content', $course->id) }}" class="block w-full px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:scale-105 mb-4 text-center">
                                Lanjutkan Belajar
                            </a>
                        @endif
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Akses seumur hidup</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Sertifikat penyelesaian</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Dukungan instruktur</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Tabs -->
                    <div class="bg-white rounded-xl shadow-lg mb-6">
                        <div class="border-b border-gray-200">
                            <nav class="flex -mb-px" aria-label="Tabs">
                                <button onclick="showTab('description')" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600">
                                    Deskripsi
                                </button>
                                <button onclick="showTab('curriculum')" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    Kurikulum
                                </button>
                                <button onclick="showTab('instructor')" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    Instruktur
                                </button>
                                <button onclick="showTab('reviews')" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    Ulasan
                                </button>
                            </nav>
                        </div>
                        
                        <div class="p-6">
                            <!-- Description Tab -->
                            <div id="description-tab" class="tab-content">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Tentang Kursus Ini</h3>
                                <div class="prose max-w-none text-gray-700">
                                    {!! nl2br(e($course->about_course)) !!}
                                </div>
                            </div>
                            
                            <!-- Curriculum Tab -->
                            <div id="curriculum-tab" class="tab-content hidden">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Kurikulum Kursus</h3>
                                <div class="space-y-4">
                                    @foreach($course->chapters as $chapter)
                                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                                            <div class="bg-gray-50 px-4 py-3 font-semibold text-gray-900">
                                                {{ $chapter->title }}
                                            </div>
                                            <div class="divide-y divide-gray-200">
                                                @foreach($chapter->materials as $material)
                                                    @php
                                                        // Determine material type and content
                                                        $isMixedType = ($material->material_type === 'mixed');
                                                        $hasVideoUrl = !empty($material->video_url);
                                                        $hasPdf = !empty($material->pdf_file_path);
                                                        $hasImage = !empty($material->image_file_path);
                                                        $hasText = !empty($material->text_content);
                                                        $hasLegacyFile = !empty($material->file_path);
                                                        
                                                        // Determine display type
                                                        $displayType = 'mixed';
                                                        $iconType = 'mixed';
                                                        $typeLabel = '';
                                                        
                                                        if ($isMixedType) {
                                                            $iconType = 'mixed';
                                                            $typeLabel = 'Mixed';
                                                            // For mixed type, don't show video URL
                                                            $hasVideoUrl = false;
                                                        } elseif ($hasVideoUrl) {
                                                            $iconType = 'video';
                                                            $typeLabel = 'Video';
                                                        } elseif ($hasPdf) {
                                                            $iconType = 'pdf';
                                                            $typeLabel = 'PDF';
                                                        } elseif ($hasImage) {
                                                            $iconType = 'image';
                                                            $typeLabel = 'Gambar';
                                                        } elseif ($hasText) {
                                                            $iconType = 'text';
                                                            $typeLabel = 'Teks';
                                                        } elseif ($hasLegacyFile) {
                                                            $iconType = $material->material_type ?? 'file';
                                                            $typeLabel = ucfirst($material->material_type ?? 'File');
                                                        } else {
                                                            $iconType = $material->material_type ?? 'file';
                                                            $typeLabel = ucfirst($material->material_type ?? 'File');
                                                        }
                                                        
                                                        // Get duration (for video/audio)
                                                        $duration = null;
                                                        if ($iconType === 'video' || $iconType === 'audio') {
                                                            $duration = $material->duration ?? null;
                                                        }
                                                    @endphp
                                                    <div class="px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                                        <div class="flex items-center gap-3 flex-1">
                                                            @if($iconType === 'video')
                                                                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                                                </svg>
                                                            @elseif($iconType === 'pdf')
                                                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @elseif($iconType === 'image')
                                                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                            @elseif($iconType === 'text')
                                                                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                            @elseif($iconType === 'audio')
                                                                <svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.793L4.383 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.383l4-3.617a1 1 0 011.617.793zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @elseif($iconType === 'mixed')
                                                                <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                                </svg>
                                                            @else
                                                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                                </svg>
                                                            @endif
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center gap-2 flex-wrap">
                                                                    <span class="text-gray-700 font-medium">{{ $material->title }}</span>
                                                                    @if($typeLabel)
                                                                        <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded">
                                                                            {{ $typeLabel }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                @if($isMixedType)
                                                                    @php
                                                                        $mixedTypes = [];
                                                                        if (!empty($material->video_url)) $mixedTypes[] = 'Video';
                                                                        if (!empty($material->pdf_file_path)) $mixedTypes[] = 'PDF';
                                                                        if (!empty($material->image_file_path)) $mixedTypes[] = 'Gambar';
                                                                        if (!empty($material->text_content)) $mixedTypes[] = 'Teks';
                                                                    @endphp
                                                                    <p class="text-xs text-gray-500 mt-0.5">{{ implode(', ', $mixedTypes) }}</p>
                                                                @elseif($hasVideoUrl)
                                                                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $material->video_url }}</p>
                                                                @elseif($hasPdf && $material->pdf_file_path)
                                                                    <p class="text-xs text-gray-500 mt-0.5">File PDF</p>
                                                                @elseif($hasImage && $material->image_file_path)
                                                                    <p class="text-xs text-gray-500 mt-0.5">File Gambar</p>
                                                                @elseif($hasText && $material->text_content)
                                                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ mb_substr(strip_tags($material->text_content), 0, 50) }}{{ mb_strlen(strip_tags($material->text_content)) > 50 ? '...' : '' }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if($isMixedType)
                                                            <span class="text-sm text-gray-500 whitespace-nowrap ml-4">Mixed</span>
                                                        @elseif($duration)
                                                            <span class="text-sm text-gray-500 whitespace-nowrap ml-4">{{ gmdate('i:s', $duration) }}</span>
                                                        @elseif($hasVideoUrl)
                                                            <span class="text-sm text-gray-500 whitespace-nowrap ml-4">Video</span>
                                                        @elseif($hasPdf)
                                                            <span class="text-sm text-gray-500 whitespace-nowrap ml-4">PDF</span>
                                                        @elseif($hasImage)
                                                            <span class="text-sm text-gray-500 whitespace-nowrap ml-4">Gambar</span>
                                                        @elseif($hasText)
                                                            <span class="text-sm text-gray-500 whitespace-nowrap ml-4">Teks</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Instructor Tab -->
                            <div id="instructor-tab" class="tab-content hidden">
                                <div class="flex items-start gap-6">
                                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-3xl font-bold">
                                            {{ strtoupper(substr($course->instructor->full_name ?? $course->instructor->name ?? 'I', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $course->instructor->full_name ?? $course->instructor->name }}</h3>
                                        <div class="prose max-w-none text-gray-700">
                                            {!! nl2br(e($course->about_instructor)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Reviews Tab -->
                            <div id="reviews-tab" class="tab-content hidden">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Ulasan Siswa</h3>
                                
                                @auth
                                    @if($canRate)
                                        <!-- Rating Form -->
                                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                                {{ $existingRating ? 'Edit Ulasan Anda' : 'Berikan Ulasan' }}
                                            </h4>
                                            <form action="{{ route('course-ratings.store', $course->id) }}" method="POST" id="rating-form">
                                                @csrf
                                                @if($existingRating)
                                                    @method('PUT')
                                                @endif
                                                
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                                    <div class="flex items-center gap-2" id="star-rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <button type="button" class="star-btn focus:outline-none" data-rating="{{ $i }}">
                                                                <svg class="w-8 h-8 {{ $existingRating && $i <= $existingRating->rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            </button>
                                                        @endfor
                                                    </div>
                                                    <input type="hidden" name="rating" id="rating-input" value="{{ $existingRating ? $existingRating->rating : '' }}" required>
                                                    @error('rating')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Ulasan</label>
                                                    <textarea name="review" id="review" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tulis ulasan Anda tentang kursus ini...">{{ $existingRating ? $existingRating->review : old('review') }}</textarea>
                                                    @error('review')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                
                                                <div class="flex gap-3">
                                                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                        {{ $existingRating ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                                                    </button>
                                                    @if($existingRating)
                                                        <form action="{{ route('course-ratings.destroy', $existingRating->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                                Hapus Ulasan
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                    @elseif($isEnrolled && !$canRate)
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                            <p class="text-yellow-800 text-sm">
                                                <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                Anda harus menyelesaikan semua materi kursus sebelum dapat memberikan rating.
                                            </p>
                                        </div>
                                    @endif
                                @endauth
                                
                                @if($course->ratings->count() > 0)
                                    <div class="space-y-6">
                                        @foreach($course->ratings as $rating)
                                            <div class="border-b border-gray-200 pb-6 last:border-0">
                                                <div class="flex items-start gap-4">
                                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-bold">
                                                            {{ strtoupper(substr($rating->user->full_name ?? $rating->user->name ?? 'U', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <div class="flex items-center">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <svg class="w-4 h-4 {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                    </svg>
                                                                @endfor
                                                            </div>
                                                            <span class="font-semibold text-gray-900">{{ $rating->user->full_name ?? $rating->user->name }}</span>
                                                            <span class="text-sm text-gray-500">{{ $rating->created_at->diffForHumans() }}</span>
                                                            @if($rating->user_id === Auth::id())
                                                                <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-800 rounded">Ulasan Anda</span>
                                                            @endif
                                                        </div>
                                                        @if($rating->review)
                                                            <p class="text-gray-700">{{ $rating->review }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-8">Belum ada ulasan untuk kursus ini.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Kursus Lainnya</h3>
                        <div class="space-y-4">
                            @foreach($similarCourses as $similar)
                                <a href="{{ route('courses.show', $similar->slug) }}" class="block group">
                                    <div class="flex gap-3">
                                        @if($similar->thumbnail)
                                            <img src="{{ asset('storage/' . $similar->thumbnail) }}" alt="{{ $similar->title }}" class="w-20 h-20 object-cover rounded-lg">
                                        @else
                                            <div class="w-20 h-20 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-lg"></div>
                                        @endif
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-sm text-gray-900 group-hover:text-indigo-600 line-clamp-2">{{ $similar->title }}</h4>
                                            <div class="flex items-center gap-1 mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= round($similar->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <p class="text-sm font-bold text-indigo-600 mt-1">
                                                @if($similar->isFree())
                                                    Gratis
                                                @else
                                                    Rp {{ number_format($similar->price, 0, ',', '.') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Add active class to clicked button
            event.target.classList.add('active', 'border-indigo-500', 'text-indigo-600');
            event.target.classList.remove('border-transparent', 'text-gray-500');
        }

        // Star rating functionality
        document.addEventListener('DOMContentLoaded', function() {
            const starButtons = document.querySelectorAll('.star-btn');
            const ratingInput = document.getElementById('rating-input');
            
            if (starButtons.length > 0 && ratingInput) {
                let currentRating = ratingInput.value ? parseInt(ratingInput.value) : 0;
                
                starButtons.forEach((btn, index) => {
                    const rating = index + 1;
                    
                    btn.addEventListener('click', function() {
                        currentRating = rating;
                        ratingInput.value = rating;
                        updateStars(rating);
                    });
                    
                    btn.addEventListener('mouseenter', function() {
                        updateStars(rating);
                    });
                });
                
                const starContainer = document.getElementById('star-rating');
                if (starContainer) {
                    starContainer.addEventListener('mouseleave', function() {
                        updateStars(currentRating);
                    });
                }
                
                function updateStars(rating) {
                    starButtons.forEach((btn, index) => {
                        const star = btn.querySelector('svg');
                        if (index < rating) {
                            star.classList.remove('text-gray-300');
                            star.classList.add('text-yellow-400');
                        } else {
                            star.classList.remove('text-yellow-400');
                            star.classList.add('text-gray-300');
                        }
                    });
                }
            }
        });
    </script>
</x-app-layout>
