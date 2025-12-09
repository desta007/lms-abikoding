<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <a href="{{ route('community.index') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Feed
            </a>

            <!-- Post Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
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
                            @if(str_contains($post->media_path, 'image') || str_ends_with($post->media_path, ['.jpg', '.jpeg', '.png', '.gif']))
                                <img src="{{ asset('storage/' . $post->media_path) }}" alt="Post media" class="w-full max-h-96 object-cover">
                            @elseif(str_contains($post->media_path, 'video') || str_ends_with($post->media_path, ['.mp4', '.mov', '.avi']))
                                <video src="{{ asset('storage/' . $post->media_path) }}" controls class="w-full max-h-96"></video>
                            @endif
                        </div>
                    @endif

                    @if($post->link_url)
                        <a href="{{ $post->link_url }}" target="_blank" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 mb-4">
                            <div class="flex items-center gap-2 text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                <span class="text-sm font-medium">{{ $post->link_url }}</span>
                            </div>
                        </a>
                    @endif

                    <!-- Post Actions -->
                    <div class="flex items-center gap-6 pt-4 border-t border-gray-100">
                        <form action="{{ route('community.posts.like', $post->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 text-gray-600 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5 {{ $post->likes->contains('user_id', Auth::id()) ? 'text-red-500 fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>{{ $post->likes->count() }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Komentar ({{ $post->comments->count() }})</h3>

                <!-- Comment Form -->
                <form action="{{ route('community.comments.store') }}" method="POST" class="mb-6">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()?->full_name ?? Auth::user()?->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <textarea name="content" 
                                      rows="3" 
                                      class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 resize-none" 
                                      placeholder="Tulis komentar..."></textarea>
                            <button type="submit" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                                Kirim Komentar
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Comments List -->
                @if($post->comments->count() > 0)
                    <div class="space-y-4">
                        @foreach($post->comments as $comment)
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-xs">
                                        {{ strtoupper(substr($comment->user->full_name ?? $comment->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-semibold text-sm text-gray-900">{{ $comment->user->full_name ?? $comment->user->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                                    </div>
                                    @if($comment->user_id === Auth::id())
                                        <form action="{{ route('community.comments.destroy', $comment->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

