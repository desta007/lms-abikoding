<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Komunitas</h1>
                <p class="text-gray-600">Bagikan pengalaman belajar dan berinteraksi dengan sesama siswa</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Feed -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Create Post Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold">
                                    {{ strtoupper(substr(Auth::user()?->full_name ?? Auth::user()?->name ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <form action="{{ route('community.posts.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
                                    @csrf
                                    <textarea name="content" 
                                              id="postContent"
                                              rows="3" 
                                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 resize-none" 
                                              placeholder="Apa yang ingin Anda bagikan hari ini?"></textarea>
                                    
                                    <input type="hidden" name="post_type" id="postType" value="text">
                                    
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 text-gray-600 hover:text-indigo-600 cursor-pointer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <input type="file" 
                                                       name="media" 
                                                       id="postMedia"
                                                       accept="image/*,video/*,.mp4,.mov,.avi,.wmv,.flv,.webm,.mkv" 
                                                       class="hidden">
                                                <span class="text-sm">Foto/Video</span>
                                            </label>
                                            <div id="filePreview" class="hidden">
                                                <span id="fileName" class="text-sm text-gray-600"></span>
                                            </div>
                                        </div>
                                        <button type="submit" 
                                                id="postSubmitBtn"
                                                disabled
                                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                            <span id="postButtonText">Post</span>
                                            <svg id="postLoadingIcon" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Posts Feed -->
                    @if($posts->count() > 0)
                        @foreach($posts as $post)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <!-- Post Header -->
                                <div class="p-6 border-b border-gray-100">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start gap-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold">
                                                    {{ strtoupper(substr($post->user->full_name ?? $post->user->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900">{{ $post->user->full_name ?? $post->user->name }}</h3>
                                                <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        @if($post->user_id === Auth::id())
                                            <form action="{{ route('community.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Hapus post ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <!-- Post Content -->
                                <div class="p-6">
                                    <p class="text-gray-800 mb-4 whitespace-pre-wrap">{{ $post->content }}</p>
                                    
                                    @if($post->media_path)
                                        <div class="mb-4 rounded-lg overflow-hidden">
                                            @php
                                                $isImage = str_contains($post->media_path, 'image') || 
                                                          str_ends_with($post->media_path, '.jpg') || 
                                                          str_ends_with($post->media_path, '.jpeg') || 
                                                          str_ends_with($post->media_path, '.png') || 
                                                          str_ends_with($post->media_path, '.gif');
                                                $isVideo = str_contains($post->media_path, 'video') || 
                                                          str_ends_with($post->media_path, '.mp4') || 
                                                          str_ends_with($post->media_path, '.mov') || 
                                                          str_ends_with($post->media_path, '.avi');
                                            @endphp
                                            @if($isImage)
                                                <img src="{{ asset('storage/' . $post->media_path) }}" alt="Post media" class="w-full max-h-96 object-cover">
                                            @elseif($isVideo)
                                                <video src="{{ asset('storage/' . $post->media_path) }}" controls class="w-full max-h-96"></video>
                                            @endif
                                        </div>
                                    @endif

                                    @if($post->link_url)
                                        @php
                                            // Check if it's a YouTube URL
                                            $isYouTube = preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $post->link_url, $matches);
                                            $isVimeo = preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $post->link_url, $vimeoMatches);
                                        @endphp
                                        @if($isYouTube)
                                            <div class="mb-4 rounded-lg overflow-hidden bg-black">
                                                <div class="w-full" style="min-height: 450px; height: 0; padding-bottom: 56.25%; position: relative;">
                                                    <iframe 
                                                        src="https://www.youtube.com/embed/{{ $matches[1] }}" 
                                                        class="absolute top-0 left-0 w-full h-full"
                                                        frameborder="0" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                        allowfullscreen>
                                                    </iframe>
                                                </div>
                                            </div>
                                        @elseif($isVimeo)
                                            <div class="mb-4 rounded-lg overflow-hidden bg-black">
                                                <div class="w-full" style="min-height: 450px; height: 0; padding-bottom: 56.25%; position: relative;">
                                                    <iframe 
                                                        src="https://player.vimeo.com/video/{{ $vimeoMatches[1] }}" 
                                                        class="absolute top-0 left-0 w-full h-full"
                                                        frameborder="0" 
                                                        allow="autoplay; fullscreen; picture-in-picture" 
                                                        allowfullscreen>
                                                    </iframe>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ $post->link_url }}" target="_blank" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 mb-4">
                                                <div class="flex items-center gap-2 text-indigo-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                    </svg>
                                                    <span class="text-sm font-medium">{{ $post->link_url }}</span>
                                                </div>
                                            </a>
                                        @endif
                                    @endif

                                    <!-- Post Actions -->
                                    <div class="flex items-center gap-6 pt-4 border-t border-gray-100">
                                        <button type="button" 
                                                class="like-btn flex items-center gap-2 text-gray-600 hover:text-red-500 transition-colors" 
                                                data-post-id="{{ $post->id }}"
                                                data-liked="{{ $post->likes->contains('user_id', Auth::id()) ? 'true' : 'false' }}">
                                            <svg class="w-5 h-5 like-icon-{{ $post->id }} {{ $post->likes->contains('user_id', Auth::id()) ? 'text-red-500 fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                            <span class="like-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
                                        </button>
                                        
                                        <button type="button" 
                                                class="comment-toggle-btn flex items-center gap-2 text-gray-600 hover:text-indigo-600 transition-colors" 
                                                data-post-id="{{ $post->id }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span class="comment-count-{{ $post->id }}">{{ $post->comments->count() }}</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Comment Section -->
                                <div class="comment-section-{{ $post->id }} px-6 pb-6 border-t border-gray-100" style="display: none;">
                                    <!-- Comment Form -->
                                    <form class="comment-form-{{ $post->id }} mt-4 mb-4" data-post-id="{{ $post->id }}">
                                        @csrf
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-white text-xs font-bold">
                                                    {{ strtoupper(substr(Auth::user()?->full_name ?? Auth::user()?->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <textarea name="content" 
                                                          rows="2" 
                                                          class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 resize-none comment-input-{{ $post->id }}" 
                                                          placeholder="Tulis komentar..."></textarea>
                                                <button type="submit" 
                                                        class="comment-submit-btn comment-submit-btn-{{ $post->id }} mt-2 px-4 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed" 
                                                        disabled>
                                                    Kirim
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Comments List -->
                                    <div class="comments-list-{{ $post->id }} space-y-3">
                                        @if($post->comments->count() > 0)
                                            @foreach($post->comments->take(5) as $comment)
                                                <div class="comment-item flex items-start gap-3" data-comment-id="{{ $comment->id }}">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="text-white text-xs font-bold">
                                                            {{ strtoupper(substr($comment->user->full_name ?? $comment->user->name ?? 'U', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="bg-gray-50 rounded-lg p-3">
                                                            <div class="flex items-center justify-between mb-1">
                                                                <span class="font-semibold text-sm text-gray-900">{{ $comment->user->full_name ?? $comment->user->name }}</span>
                                                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                                            </div>
                                                            <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                                                        </div>
                                                        @if($comment->user_id === Auth::id())
                                                            <button type="button" 
                                                                    class="delete-comment-btn mt-1 text-xs text-red-500 hover:text-red-700" 
                                                                    data-comment-id="{{ $comment->id }}"
                                                                    data-post-id="{{ $post->id }}">
                                                                Hapus
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($post->comments->count() > 5)
                                                <a href="{{ route('community.posts.show', $post->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium block">
                                                    Lihat semua {{ $post->comments->count() }} komentar
                                                </a>
                                            @endif
                                        @else
                                            <p class="text-gray-500 text-sm text-center py-4">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada post</h3>
                            <p class="text-gray-600">Jadilah yang pertama untuk berbagi di komunitas!</p>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Links -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Menu Komunitas</h3>
                        <div class="space-y-2">
                            <a href="{{ route('community.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span class="font-medium text-gray-700">Feed</span>
                            </a>
                            <a href="{{ route('community.broadcasts.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-medium text-gray-700">Broadcast</span>
                            </a>
                            <a href="{{ route('community.events.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-medium text-gray-700">Events</span>
                            </a>
                        </div>
                    </div>

                    <!-- Stats Card -->
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <h3 class="text-lg font-bold mb-4">Statistik Anda</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span>Points</span>
                                <span class="font-bold text-xl">{{ Auth::user()->profile->points ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Posts</span>
                                <span class="font-bold text-xl">{{ Auth::user()->posts->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Post form handling
            const $postForm = $('#postForm');
            const $postMedia = $('#postMedia');
            const $postContent = $('#postContent');
            const $postSubmitBtn = $('#postSubmitBtn');
            const $postButtonText = $('#postButtonText');
            const $postLoadingIcon = $('#postLoadingIcon');
            const $filePreview = $('#filePreview');
            const $fileName = $('#fileName');
            const $postType = $('#postType');

            // Handle file selection
            $postMedia.on('change', function(e) {
                const file = this.files[0];
                if (file) {
                    // Show file name
                    $fileName.text(file.name);
                    $filePreview.removeClass('hidden');
                    
                    // Determine post type
                    if (file.type.startsWith('image/')) {
                        $postType.val('image');
                    } else if (file.type.startsWith('video/')) {
                        $postType.val('video');
                    }
                    
                    // Set loading state (only spinner, no text)
                    setLoadingState();
                    
                    // Auto-submit form
                    $postForm.submit();
                }
            });

            // Function to check if button should be enabled
            function checkButtonState() {
                const content = $postContent.val().trim();
                const hasFile = $postMedia[0].files.length > 0;
                
                // Enable button if there's content or a file selected
                if (content || hasFile) {
                    $postSubmitBtn.prop('disabled', false);
                } else {
                    $postSubmitBtn.prop('disabled', true);
                }
            }
            
            // Function to reset post button state
            function resetPostButton() {
                $postButtonText.text('Post').removeClass('hidden');
                $postLoadingIcon.addClass('hidden');
                // Check if button should be enabled based on content/file
                checkButtonState();
            }
            
            // Function to set loading state (show "Posting" text and spinner)
            function setLoadingState() {
                $postSubmitBtn.prop('disabled', true);
                $postButtonText.text('Posting').removeClass('hidden');
                $postLoadingIcon.removeClass('hidden');
            }
            
            // Monitor content changes to enable/disable button
            $postContent.on('input', function() {
                checkButtonState();
            });
            
            // Monitor file selection changes
            $postMedia.on('change', function() {
                checkButtonState();
            });
            
            // Initialize button state on page load
            checkButtonState();

            // Handle form submission
            $postForm.on('submit', function(e) {
                const content = $postContent.val().trim();
                const hasFile = $postMedia[0].files.length > 0;
                
                // Validate FIRST before setting any loading state
                if (!hasFile && !content) {
                    e.preventDefault();
                    // Make sure button is in normal state
                    resetPostButton();
                    alert('Silakan masukkan konten atau pilih file untuk diposting.');
                    return false;
                }
                
                // If file is selected, allow submission even without content
                // Loading state already set in file change handler if file was just selected
                if (hasFile) {
                    // Make sure loading state is set (in case form was submitted manually after file selection)
                    if (!$postLoadingIcon.hasClass('hidden')) {
                        // Already in loading state, proceed
                        return true;
                    } else {
                        // Set loading state now
                        setLoadingState();
                        return true;
                    }
                }
                
                // Check for YouTube/Vimeo URL in content
                const youtubeRegex = /(https?:\/\/(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11}))/;
                const vimeoRegex = /(https?:\/\/(?:www\.)?vimeo\.com\/(?:.*\/)?(\d+))/;
                const youtubeMatch = content.match(youtubeRegex);
                const vimeoMatch = content.match(vimeoRegex);
                
                if (youtubeMatch || vimeoMatch) {
                    $postType.val('link');
                    // Extract URL from content
                    const urlMatch = youtubeMatch ? youtubeMatch[1] : (vimeoMatch ? vimeoMatch[1] : null);
                    if (urlMatch) {
                        // Remove existing link_url input if any
                        $('input[name="link_url"]').remove();
                        // Add hidden input for link_url
                        $postForm.append('<input type="hidden" name="link_url" value="' + urlMatch + '">');
                    }
                }
                
                // Set loading state (only spinner, no text)
                setLoadingState();
            });
            
            // Reset form state on page load (in case of errors)
            $(window).on('load', function() {
                $postButtonText.text('Post').removeClass('hidden');
                $postLoadingIcon.addClass('hidden');
                $filePreview.addClass('hidden');
                checkButtonState();
            });

            // Like functionality
            $('.like-btn').on('click', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const postId = $btn.data('post-id');
                const isLiked = $btn.data('liked') === 'true' || $btn.data('liked') === true;
                const $icon = $('.like-icon-' + postId);
                const $count = $('.like-count-' + postId);
                
                // Disable button during request
                $btn.prop('disabled', true);
                
                $.ajax({
                    url: '{{ url("/community/posts") }}/' + postId + '/like',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        // Update like state
                        $btn.data('liked', response.liked);
                        
                        // Update icon
                        if (response.liked) {
                            $icon.addClass('text-red-500 fill-current');
                        } else {
                            $icon.removeClass('text-red-500 fill-current');
                        }
                        
                        // Update count
                        $count.text(response.likes_count);
                    },
                    error: function(xhr) {
                        console.error('Like error:', xhr);
                        alert('Terjadi kesalahan saat menyukai post. Silakan coba lagi.');
                    },
                    complete: function() {
                        // Re-enable button
                        $btn.prop('disabled', false);
                    }
                });
            });

            // Toggle comment section
            $('.comment-toggle-btn').on('click', function(e) {
                e.preventDefault();
                const postId = $(this).data('post-id');
                const $commentSection = $('.comment-section-' + postId);
                
                if ($commentSection.is(':visible')) {
                    $commentSection.slideUp();
                } else {
                    $commentSection.slideDown();
                    // Focus on comment input
                    $('.comment-input-' + postId).focus();
                }
            });

            // Function to update comment submit button state
            function updateCommentButtonState(postId) {
                const $input = $('.comment-input-' + postId);
                const $submitBtn = $('.comment-submit-btn-' + postId);
                
                if ($input.length === 0 || $submitBtn.length === 0) {
                    return;
                }
                
                const content = $input.val().trim();
                
                if (content) {
                    $submitBtn.prop('disabled', false);
                } else {
                    $submitBtn.prop('disabled', true);
                }
            }
            
            // Handle comment input changes to enable/disable submit button
            $(document).on('input', '[class*="comment-input-"]', function() {
                const $input = $(this);
                const classes = $input.attr('class') || '';
                const match = classes.match(/comment-input-(\d+)/);
                if (!match) return;
                
                const postId = match[1];
                updateCommentButtonState(postId);
            });
            
            // Handle comment submit button click (alternative to form submit)
            $(document).on('click', '[class*="comment-submit-btn-"]', function(e) {
                const $btn = $(this);
                if ($btn.prop('disabled')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                const classes = $btn.attr('class') || '';
                const match = classes.match(/comment-submit-btn-(\d+)/);
                if (!match) return;
                
                const postId = match[1];
                const $form = $('.comment-form-' + postId);
                
                if ($form.length > 0) {
                    $form.trigger('submit');
                }
            });

            // Submit comment (use event delegation for dynamically loaded forms)
            $(document).on('submit', '[class*="comment-form-"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $form = $(this);
                const postId = $form.data('post-id');
                
                console.log('Comment form submitted for post:', postId);
                
                if (!postId) {
                    console.error('Post ID not found');
                    alert('Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
                    return false;
                }
                
                const $input = $('.comment-input-' + postId);
                const $submitBtn = $('.comment-submit-btn-' + postId);
                const $commentsList = $('.comments-list-' + postId);
                const $commentCount = $('.comment-count-' + postId);
                
                if ($input.length === 0) {
                    console.error('Comment input not found for post:', postId);
                    alert('Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
                    return false;
                }
                
                if ($submitBtn.length === 0) {
                    console.error('Submit button not found for post:', postId);
                    alert('Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
                    return false;
                }
                
                const content = $input.val().trim();
                
                if (!content) {
                    alert('Komentar tidak boleh kosong');
                    return false;
                }
                
                // Disable form during submission
                $submitBtn.prop('disabled', true).text('Mengirim...');
                
                console.log('Submitting comment:', { postId, content });
                
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    alert('Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
                    $submitBtn.prop('disabled', false).text('Kirim');
                    return;
                }
                
                $.ajax({
                    url: '{{ route("community.comments.store") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: {
                        post_id: postId,
                        content: content
                    },
                    success: function(response) {
                        console.log('Comment submitted successfully:', response);
                        
                        // Clear input
                        $input.val('');
                        
                        // Reset button to disabled state (input is empty after submit)
                        $submitBtn.prop('disabled', true).text('Kirim');
                        
                        // Update button state explicitly
                        updateCommentButtonState(postId);
                        
                        // Focus back to input for next comment
                        setTimeout(function() {
                            $input.focus();
                        }, 100);
                        
                        // Get user info
                        const userInitial = '{{ strtoupper(substr(Auth::user()?->full_name ?? Auth::user()?->name ?? "U", 0, 1)) }}';
                        const userName = '{{ Auth::user()?->full_name ?? Auth::user()?->name ?? "User" }}';
                        const escapedContent = $('<div>').text(content).html().replace(/\n/g, '<br>');
                        
                        // Remove "no comments" message if exists
                        $commentsList.find('p.text-center').remove();
                        
                        // Create new comment HTML
                        const commentHtml = `
                            <div class="comment-item flex items-start gap-3" data-comment-id="${response.comment_id || 'new'}">
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-xs font-bold">${userInitial}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-semibold text-sm text-gray-900">${userName}</span>
                                            <span class="text-xs text-gray-500">Baru saja</span>
                                        </div>
                                        <p class="text-sm text-gray-700">${escapedContent}</p>
                                    </div>
                                    <button type="button" 
                                            class="delete-comment-btn mt-1 text-xs text-red-500 hover:text-red-700" 
                                            data-comment-id="${response.comment_id}"
                                            data-post-id="${postId}">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        `;
                        
                        // Add comment to top of list with animation
                        const $newComment = $(commentHtml).hide();
                        $commentsList.prepend($newComment);
                        $newComment.fadeIn(300);
                        
                        // Update comment count
                        const currentCount = parseInt($commentCount.text()) || 0;
                        $commentCount.text(currentCount + 1);
                        
                        // Update "see all comments" link if it exists
                        const $seeAllLink = $commentsList.find('a').first();
                        if ($seeAllLink.length > 0) {
                            const newTotalCount = currentCount + 1;
                            $seeAllLink.text(`Lihat semua ${newTotalCount} komentar`);
                        } else if (currentCount >= 4) {
                            // Show link if we're about to hit the limit
                            $commentsList.append(`<a href="{{ url('/community/posts') }}/${postId}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium block mt-2">Lihat semua ${currentCount + 1} komentar</a>`);
                        }
                    },
                    error: function(xhr) {
                        console.error('Comment error:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        
                        let errorMsg = 'Terjadi kesalahan saat mengirim komentar.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMsg = errors.join(', ');
                            }
                        } else if (xhr.status === 419) {
                            errorMsg = 'Session expired. Silakan refresh halaman dan coba lagi.';
                        } else if (xhr.status === 404) {
                            errorMsg = 'Route tidak ditemukan. Silakan refresh halaman.';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Server error. Silakan coba lagi nanti.';
                        }
                        
                        alert(errorMsg);
                        
                        // Reset button based on current input content
                        updateCommentButtonState(postId);
                        $submitBtn.text('Kirim');
                    }
                });
            });

            // Delete comment
            $(document).on('click', '.delete-comment-btn', function(e) {
                e.preventDefault();
                if (!confirm('Hapus komentar ini?')) {
                    return;
                }
                
                const $btn = $(this);
                const commentId = $btn.data('comment-id');
                const postId = $btn.data('post-id');
                const $commentItem = $btn.closest('.comment-item');
                const $commentsList = $('.comments-list-' + postId);
                const $commentCount = $('.comment-count-' + postId);
                
                $.ajax({
                    url: '{{ url("/community/comments") }}/' + commentId,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        // Remove comment from DOM
                        $commentItem.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Update comment count
                            const currentCount = parseInt($commentCount.text()) || 0;
                            $commentCount.text(Math.max(0, currentCount - 1));
                            
                            // Show "no comments" message if list is empty
                            if ($commentsList.find('.comment-item').length === 0) {
                                $commentsList.html('<p class="text-gray-500 text-sm text-center py-4">Belum ada komentar. Jadilah yang pertama berkomentar!</p>');
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Delete comment error:', xhr);
                        alert('Terjadi kesalahan saat menghapus komentar. Silakan coba lagi.');
                    }
                });
            });
        });
    </script>
</x-app-layout>

