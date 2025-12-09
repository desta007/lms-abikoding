<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Broadcast</h1>
                    <p class="text-gray-600">Siaran langsung dan rekaman pembelajaran</p>
                </div>
                <a href="{{ route('community.broadcasts.create') }}" 
                   class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                    Buat Broadcast
                </a>
            </div>

            <!-- Live Broadcasts -->
            @if($liveBroadcasts->count() > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                        Siaran Langsung
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($liveBroadcasts as $broadcast)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                @if($broadcast->thumbnail)
                                    <img src="{{ Storage::url($broadcast->thumbnail) }}" alt="{{ $broadcast->title }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                            LIVE
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ $broadcast->views()->count() }} viewers
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $broadcast->title }}</h3>
                                    @if($broadcast->description)
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $broadcast->description }}</p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">
                                                    {{ strtoupper(substr($broadcast->user->full_name ?? $broadcast->user->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $broadcast->user->full_name ?? $broadcast->user->name }}</span>
                                        </div>
                                        <a href="{{ route('community.broadcasts.show', $broadcast->id) }}" 
                                           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                                            Tonton
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upcoming Broadcasts -->
            @if($upcomingBroadcasts->count() > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Siaran Mendatang</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($upcomingBroadcasts as $broadcast)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                @if($broadcast->thumbnail)
                                    <img src="{{ Storage::url($broadcast->thumbnail) }}" alt="{{ $broadcast->title }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                            UPCOMING
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ $broadcast->scheduled_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $broadcast->title }}</h3>
                                    @if($broadcast->description)
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $broadcast->description }}</p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">
                                                    {{ strtoupper(substr($broadcast->user->full_name ?? $broadcast->user->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $broadcast->user->full_name ?? $broadcast->user->name }}</span>
                                        </div>
                                        <a href="{{ route('community.broadcasts.show', $broadcast->id) }}" 
                                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition-colors">
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Broadcasts -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Rekaman Siaran</h2>
                @if($recentBroadcasts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($recentBroadcasts as $broadcast)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                @if($broadcast->thumbnail)
                                    <img src="{{ Storage::url($broadcast->thumbnail) }}" alt="{{ $broadcast->title }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center relative">
                                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-xs">
                                            {{ $broadcast->views()->count() }} views
                                        </div>
                                    </div>
                                @endif
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $broadcast->title }}</h3>
                                    @if($broadcast->description)
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $broadcast->description }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">
                                                    {{ strtoupper(substr($broadcast->user->full_name ?? $broadcast->user->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $broadcast->user->full_name ?? $broadcast->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $broadcast->ended_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                                {{ $broadcast->likes()->count() }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                </svg>
                                                {{ $broadcast->comments()->count() }}
                                            </span>
                                        </div>
                                        <a href="{{ route('community.broadcasts.show', $broadcast->id) }}" 
                                           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                                            Tonton
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $recentBroadcasts->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada siaran</h3>
                        <p class="text-gray-500 mb-4">Jadilah yang pertama membuat siaran!</p>
                        <a href="{{ route('community.broadcasts.create') }}" 
                           class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                            Buat Broadcast Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

