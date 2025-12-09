<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Breadcrumb -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('courses.content', $course->id) }}" class="text-gray-400 hover:text-gray-500">
                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <a href="{{ route('courses.content', $course->id) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                                    {{ $course->title }}
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <a href="{{ route('courses.chapter', [$course->id, $chapter->id]) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                                    Bab {{ $chapter->order }}
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-900">{{ $material->title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar: Materials List -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-4 sticky top-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Materi Bab {{ $chapter->order }}</h3>
                        <div class="space-y-2">
                            @foreach($chapter->materials as $mat)
                                @php
                                    $matProgress = \App\Models\StudentProgress::where('course_enrollment_id', $enrollment->id)
                                        ->where('chapter_material_id', $mat->id)
                                        ->first();
                                    $isCompleted = $matProgress && $matProgress->is_completed;
                                    $isActive = $mat->id == $material->id;
                                @endphp
                                <a href="{{ route('courses.material', [$course->id, $chapter->id, $mat->id]) }}" 
                                   class="block p-2 rounded-lg {{ $isActive ? 'bg-indigo-50 border border-indigo-200' : 'hover:bg-gray-50' }} transition-colors material-item"
                                   data-material-id="{{ $mat->id }}">
                                    <div class="flex items-center gap-2">
                                        @if($isCompleted)
                                            <svg class="w-4 h-4 text-green-500 flex-shrink-0 material-check-icon material-check-icon-{{ $mat->id }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="w-4 h-4 border border-gray-300 rounded flex-shrink-0 material-uncheck-icon material-uncheck-icon-{{ $mat->id }}" style="display: none;"></div>
                                        @else
                                            <svg class="w-4 h-4 text-green-500 flex-shrink-0 material-check-icon material-check-icon-{{ $mat->id }}" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="w-4 h-4 border border-gray-300 rounded flex-shrink-0 material-uncheck-icon material-uncheck-icon-{{ $mat->id }}"></div>
                                        @endif
                                        <span class="text-sm {{ $isActive ? 'font-semibold text-indigo-900' : 'text-gray-700' }} truncate">
                                            {{ $mat->title }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Main Content: Material Viewer -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $material->title }}</h1>
                            @if($material->material_type)
                                <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded">
                                    {{ strtoupper($material->material_type) }}
                                </span>
                            @endif
                        </div>

                        <!-- Material Content - Display All Types -->
                        <div class="space-y-6">
                            <!-- Video URL Content -->
                            @if($material->video_url)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Video</h3>
                                    <div class="w-full rounded-lg overflow-hidden bg-gray-100" style="min-height: 600px;">
                                        @php
                                            // Check if it's a YouTube URL
                                            $isYouTube = preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $material->video_url, $matches);
                                            $isVimeo = preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $material->video_url, $vimeoMatches);
                                        @endphp
                                        @if($isYouTube)
                                            <iframe 
                                                src="https://www.youtube.com/embed/{{ $matches[1] }}" 
                                                class="w-full"
                                                style="height: 600px;"
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen>
                                            </iframe>
                                        @elseif($isVimeo)
                                            <iframe 
                                                src="https://player.vimeo.com/video/{{ $vimeoMatches[1] }}" 
                                                class="w-full"
                                                style="height: 600px;"
                                                frameborder="0" 
                                                allow="autoplay; fullscreen; picture-in-picture" 
                                                allowfullscreen>
                                            </iframe>
                                        @else
                                            <video 
                                                id="material-video-url"
                                                class="w-full" 
                                                style="height: 600px;"
                                                controls
                                                @if($progress->last_position)
                                                    data-last-position="{{ $progress->last_position }}"
                                                @endif>
                                                <source src="{{ $material->video_url }}" type="video/mp4">
                                                Browser Anda tidak mendukung video.
                                            </video>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Video File Content (Uploaded from Local Computer) -->
                            @if($material->video_file_path)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Video</h3>
                                    <div class="w-full rounded-lg overflow-hidden bg-gray-100" style="min-height: 600px;">
                                        <video 
                                            id="material-video"
                                            class="w-full" 
                                            style="height: 600px;"
                                            controls
                                            preload="metadata"
                                            @if($progress->last_position)
                                                data-last-position="{{ $progress->last_position }}"
                                            @endif>
                                            <source src="{{ asset('storage/' . $material->video_file_path) }}" type="{{ $material->video_file_mime_type ?? 'video/mp4' }}">
                                            Browser Anda tidak mendukung video.
                                        </video>
                                    </div>
                                    @if($material->video_file_size)
                                        <p class="mt-2 text-sm text-gray-500">
                                            Ukuran file: {{ number_format($material->video_file_size / 1024 / 1024, 2) }} MB
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <!-- PDF Content -->
                            @if($material->pdf_file_path)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Dokumen PDF</h3>
                                    <iframe 
                                        src="{{ asset('storage/' . $material->pdf_file_path) }}" 
                                        class="w-full h-screen rounded-lg border border-gray-200"
                                        frameborder="0">
                                    </iframe>
                                    <div class="mt-4">
                                        <a href="{{ asset('storage/' . $material->pdf_file_path) }}" 
                                           download
                                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Download PDF
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- Text Content -->
                            @if($material->text_content)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Konten Teks</h3>
                                    <div class="prose max-w-none">
                                        {!! nl2br(e($material->text_content)) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Image Content -->
                            @if($material->image_file_path)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Gambar</h3>
                                    <img src="{{ asset('storage/' . $material->image_file_path) }}" 
                                         alt="{{ $material->title }}" 
                                         class="w-full rounded-lg">
                                </div>
                            @endif

                            <!-- Legacy Support: Old single-type materials -->
                            @if(!$material->video_url && !$material->pdf_file_path && !$material->text_content && !$material->image_file_path)
                                @if($material->material_type === 'video')
                                    <div class="mb-6">
                                        <video 
                                            id="material-video"
                                            class="w-full rounded-lg" 
                                            style="height: 600px;"
                                            controls
                                            @if($progress->last_position)
                                                data-last-position="{{ $progress->last_position }}"
                                            @endif>
                                            <source src="{{ asset('storage/' . $material->file_path) }}" type="video/mp4">
                                            Browser Anda tidak mendukung video.
                                        </video>
                                    </div>
                                @elseif($material->material_type === 'audio')
                                    <div class="mb-6">
                                        <audio 
                                            id="material-audio"
                                            class="w-full" 
                                            controls
                                            @if($progress->last_position)
                                                data-last-position="{{ $progress->last_position }}"
                                            @endif>
                                            <source src="{{ asset('storage/' . $material->file_path) }}" type="audio/mpeg">
                                            Browser Anda tidak mendukung audio.
                                        </audio>
                                    </div>
                                @elseif($material->material_type === 'pdf')
                                    <div class="mb-6">
                                        <iframe 
                                            src="{{ asset('storage/' . $material->file_path) }}" 
                                            class="w-full h-screen rounded-lg border border-gray-200"
                                            frameborder="0">
                                        </iframe>
                                        <div class="mt-4">
                                            <a href="{{ asset('storage/' . $material->file_path) }}" 
                                               download
                                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Download PDF
                                            </a>
                                        </div>
                                    </div>
                                @elseif($material->material_type === 'image')
                                    <div class="mb-6">
                                        <img src="{{ asset('storage/' . $material->file_path) }}" 
                                             alt="{{ $material->title }}" 
                                             class="w-full rounded-lg">
                                    </div>
                                @elseif($material->material_type === 'text')
                                    <div class="mb-6 prose max-w-none">
                                        {!! nl2br(e($material->content)) !!}
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Rejection Notification -->
                        @if($progress->is_rejected)
                            <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-red-800 mb-1">Materi Ditolak oleh Instruktur</h4>
                                        <p class="text-sm text-red-700">
                                            Instruktur telah menolak penyelesaian materi ini. Silakan pelajari kembali materi dan tandai selesai untuk mengajukan ulang persetujuan.
                                        </p>
                                        @if($progress->rejection_reason)
                                            <p class="text-sm text-red-600 mt-2 italic">{{ $progress->rejection_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Completion Status & Checkbox -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            @if($progress->is_completed && ($progress->is_instructor_approved || $progress->completion_method === 'quiz_passed'))
                                <!-- Approved or Quiz Passed -->
                                <div class="flex items-center text-green-600">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-medium">
                                        @if($progress->completion_method === 'quiz_passed')
                                            Materi selesai (Lulus Quiz)
                                        @else
                                            Materi selesai (Disetujui Instruktur)
                                        @endif
                                    </span>
                                </div>
                            @elseif($progress->is_completed && !$progress->is_instructor_approved && $progress->completion_method !== 'quiz_passed')
                                <!-- Pending Approval -->
                                <div class="flex items-center text-yellow-600 mb-4">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-medium">Menunggu persetujuan instruktur</span>
                                </div>
                                <!-- Show checkbox so student can uncheck if needed -->
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="material-complete-checkbox" 
                                           class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                                           checked
                                           data-material-id="{{ $material->id }}"
                                           data-enrollment-id="{{ $enrollment->id }}"
                                           data-chapter-id="{{ $chapter->id }}">
                                    <label for="material-complete-checkbox" class="ml-3 text-sm font-medium text-gray-700">
                                        Materi sudah selesai dipelajari
                                    </label>
                                </div>
                            @else
                                <!-- Not Completed or Rejected - Show Checkbox -->
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="material-complete-checkbox" 
                                           class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                                           {{ $progress->is_completed ? 'checked' : '' }}
                                           data-material-id="{{ $material->id }}"
                                           data-enrollment-id="{{ $enrollment->id }}"
                                           data-chapter-id="{{ $chapter->id }}">
                                    <label for="material-complete-checkbox" class="ml-3 text-sm font-medium text-gray-700">
                                        Materi sudah selesai dipelajari
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 ml-8">
                                    @if($progress->is_rejected)
                                        Centang kotak ini untuk mengajukan ulang persetujuan setelah mempelajari kembali materi.
                                    @else
                                        Centang kotak ini untuk menandai materi selesai. Instruktur akan meninjau dan menyetujui sebelum Anda dapat melanjutkan ke materi berikutnya.
                                    @endif
                                </p>
                            @endif
                        </div>

                        <!-- Navigation -->
                        <div class="mt-6 flex items-center justify-between pt-6 border-t border-gray-200">
                            @php
                                $allMaterials = $chapter->materials->sortBy('order');
                                $currentIndex = $allMaterials->search(function($m) use ($material) {
                                    return $m->id == $material->id;
                                });
                                $prevMaterial = $currentIndex > 0 ? $allMaterials->values()[$currentIndex - 1] : null;
                                $nextMaterial = $currentIndex < $allMaterials->count() - 1 ? $allMaterials->values()[$currentIndex + 1] : null;
                            @endphp
                            
                            @if($prevMaterial)
                                <a href="{{ route('courses.material', [$course->id, $chapter->id, $prevMaterial->id]) }}" 
                                   class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Sebelumnya
                                </a>
                            @else
                                <div></div>
                            @endif

                            <a href="{{ route('courses.chapter', [$course->id, $chapter->id]) }}" 
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Kembali ke Daftar Materi
                            </a>

                            @if($nextMaterial)
                                <a href="{{ route('courses.material', [$course->id, $chapter->id, $nextMaterial->id]) }}" 
                                   class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    Selanjutnya
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @else
                                <div></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Material completion checkbox handler
        const completionCheckbox = document.getElementById('material-complete-checkbox');
        if (completionCheckbox) {
            completionCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                const materialId = this.dataset.materialId;
                const enrollmentId = this.dataset.enrollmentId;
                const chapterId = this.dataset.chapterId;

                // Show loading state
                const originalLabel = this.nextElementSibling;
                const originalText = originalLabel.textContent;
                originalLabel.textContent = isChecked ? 'Mengirim...' : 'Mereset...';

                $.ajax({
                    url: '{{ route("progress.complete") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    contentType: 'application/json',
                    data: JSON.stringify({
                        course_enrollment_id: parseInt(enrollmentId),
                        chapter_id: parseInt(chapterId),
                        chapter_material_id: parseInt(materialId),
                        is_completed: isChecked
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            if (typeof showNotification === 'function') {
                                showNotification(response.message || (isChecked ? 'Materi ditandai selesai. Menunggu persetujuan instruktur.' : 'Status materi direset.'));
                            } else {
                                alert(response.message || (isChecked ? 'Materi ditandai selesai. Menunggu persetujuan instruktur.' : 'Status materi direset.'));
                            }
                            
                            // Reload page to show updated status
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            alert(response.message || 'Terjadi kesalahan');
                            completionCheckbox.checked = !isChecked; // Revert checkbox
                            originalLabel.textContent = originalText;
                        }
                    },
                    error: function(xhr) {
                        console.error('Completion update error:', xhr);
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat memperbarui status';
                        alert(message);
                        completionCheckbox.checked = !isChecked; // Revert checkbox
                        originalLabel.textContent = originalText;
                    }
                });
            });
        }

        // Video/Audio progress tracking
        // Check for both video URL and video file
        const video = document.getElementById('material-video') || document.getElementById('material-video-url');
        const audio = document.getElementById('material-audio');
        const media = video || audio;
        
        // Also check for video in iframe (YouTube/Vimeo) - note: can't track progress for embedded videos

        if (media) {
            // Restore last position
            const lastPosition = media.dataset.lastPosition;
            if (lastPosition) {
                media.currentTime = parseInt(lastPosition);
            }

            // Save progress on time update (debounced)
            let progressTimeout;
            media.addEventListener('timeupdate', function() {
                clearTimeout(progressTimeout);
                progressTimeout = setTimeout(() => {
                    const progress = Math.round((media.currentTime / media.duration) * 100);
                    
                    $.ajax({
                        url: '{{ route("progress.update") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        contentType: 'application/json',
                        data: JSON.stringify({
                            course_enrollment_id: {{ $enrollment->id }},
                            chapter_id: {{ $chapter->id }},
                            chapter_material_id: {{ $material->id }},
                            progress_percentage: progress,
                            last_position: Math.round(media.currentTime)
                        }),
                        error: function(xhr) {
                            console.error('Progress update error:', xhr);
                        }
                    });
                }, 5000); // Save every 5 seconds
            });

            // Note: Auto-completion on media end is disabled
            // Students must pass quiz or get instructor approval
        }
    </script>
</x-app-layout>

