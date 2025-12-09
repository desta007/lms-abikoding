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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $broadcast->title }}</h1>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-bold">
                                {{ strtoupper(substr($broadcast->user->full_name ?? $broadcast->user->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                        <span>{{ $broadcast->user->full_name ?? $broadcast->user->name }}</span>
                    </div>
                    <span>•</span>
                    <span>{{ $broadcast->created_at->format('d M Y, H:i') }}</span>
                    @if($broadcast->is_live)
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                            LIVE
                        </span>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Broadcast Content -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                @if($broadcast->thumbnail)
                    <div class="mb-6 flex justify-center">
                        <img src="{{ Storage::url($broadcast->thumbnail) }}" alt="{{ $broadcast->title }}" class="max-w-full max-h-96 rounded-lg object-contain">
                    </div>
                @endif

                @if($broadcast->video_url)
                    <div class="mb-6">
                        <div class="relative w-full" style="padding-bottom: 56.25%; min-height: 500px;">
                            @php
                                $videoUrl = $broadcast->video_url;
                                $embedUrl = null;
                                
                                // Check if it's a YouTube URL
                                if (str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')) {
                                    $videoId = null;
                                    
                                    // Extract video ID from various YouTube URL formats
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                    
                                    if ($videoId) {
                                        $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                                    }
                                }
                            @endphp
                            
                            @if($embedUrl)
                                <iframe src="{{ $embedUrl }}" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen
                                        class="absolute top-0 left-0 w-full h-full rounded-lg"></iframe>
                            @else
                                <video controls class="absolute top-0 left-0 w-full h-full rounded-lg">
                                    <source src="{{ $broadcast->video_url }}" type="video/mp4">
                                    Browser Anda tidak mendukung video player.
                                </video>
                            @endif
                        </div>
                    </div>
                @endif

                @if($broadcast->description)
                    <div class="prose max-w-none mb-6">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $broadcast->description }}</p>
                    </div>
                @endif

                <!-- Zoom Meeting Info -->
                @if($broadcast->is_zoom_meeting)
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">Zoom Meeting</h3>
                        @if($broadcast->zoom_join_url)
                            <div class="space-y-2">
                                @if($broadcast->user_id === Auth::id() && $broadcast->zoom_start_url)
                                    <a href="{{ $broadcast->zoom_start_url }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Mulai Meeting (Host)
                                    </a>
                                @endif
                                <a href="{{ $broadcast->zoom_join_url }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Bergabung ke Meeting
                                </a>
                                @if($broadcast->zoom_meeting_id)
                                    <p class="text-sm text-gray-600 mt-2">
                                        Meeting ID: <span class="font-mono">{{ $broadcast->zoom_meeting_id }}</span>
                                        @if($broadcast->zoom_meeting_password)
                                            | Password: <span class="font-mono">{{ $broadcast->zoom_meeting_password }}</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @else
                            @if($broadcast->user_id === Auth::id())
                                <form action="{{ route('community.broadcasts.zoom', $broadcast->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                        Buat Zoom Meeting
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                @endif

                <!-- Actions -->
                @if($broadcast->user_id === Auth::id())
                    <div class="flex items-center gap-4 pt-6 border-t border-gray-200">
                        @if(!$broadcast->is_zoom_meeting && !$broadcast->is_live && !$broadcast->ended_at)
                            <form action="{{ route('community.broadcasts.zoom', $broadcast->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                    Tambah Zoom Meeting
                                </button>
                            </form>
                        @endif
                        @if(!$broadcast->is_live && !$broadcast->ended_at)
                            <form action="{{ route('community.broadcasts.start', $broadcast->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                                    Mulai Siaran
                                </button>
                            </form>
                        @endif
                        @if($broadcast->is_live)
                            <form action="{{ route('community.broadcasts.end', $broadcast->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                                    Akhiri Siaran
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $broadcast->views()->count() }}</div>
                    <div class="text-sm text-gray-600">Views</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div id="likes-count" class="text-2xl font-bold text-gray-900">{{ $broadcast->likes()->count() }}</div>
                    <div class="text-sm text-gray-600">Likes</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $broadcast->comments()->count() }}</div>
                    <div class="text-sm text-gray-600">Comments</div>
                </div>
            </div>

            <!-- Like Button -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <form id="like-form" action="{{ route('community.broadcasts.like', $broadcast->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" id="like-button" class="flex items-center gap-2 px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                        <svg id="like-icon" class="w-5 h-5 {{ $broadcast->likes()->where('user_id', Auth::id())->exists() ? 'text-red-500 fill-current' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="font-medium">Like</span>
                    </button>
                </form>
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Komentar</h2>

                <!-- Comment Form -->
                <form action="{{ route('community.broadcasts.comments.store') }}" method="POST" class="mb-6">
                    @csrf
                    <input type="hidden" name="broadcast_id" value="{{ $broadcast->id }}">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">
                                {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <textarea name="content" 
                                      rows="3" 
                                      required
                                      class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                      placeholder="Tulis komentar..."></textarea>
                            <button type="submit" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                                Kirim Komentar
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Comments List -->
                <div class="space-y-4">
                    @forelse($broadcast->comments as $comment)
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-sm font-bold">
                                    {{ strtoupper(substr($comment->user->full_name ?? $comment->user->name ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-semibold text-gray-900">{{ $comment->user->full_name ?? $comment->user->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ $comment->content }}</p>
                                    @if($comment->user_id === Auth::id())
                                        <form action="{{ route('community.broadcasts.comments.destroy', $comment->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('like-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const button = document.getElementById('like-button');
            const icon = document.getElementById('like-icon');
            const likesCount = document.getElementById('likes-count');
            
            // Disable button during request
            button.disabled = true;
            button.style.cursor = 'wait';
            button.style.opacity = '0.6';
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Update like count
                likesCount.textContent = data.likes_count;
                
                // Update icon appearance
                if (data.liked) {
                    icon.classList.add('text-red-500', 'fill-current');
                    icon.classList.remove('text-gray-500');
                } else {
                    icon.classList.remove('text-red-500', 'fill-current');
                    icon.classList.add('text-gray-500');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyukai broadcast.');
            })
            .finally(() => {
                button.disabled = false;
                button.style.cursor = 'pointer';
                button.style.opacity = '1';
            });
        });
    </script>
</x-app-layout>

