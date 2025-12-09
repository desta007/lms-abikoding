<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Events</h1>
                    <p class="text-gray-600">Ikuti dan buat event komunitas</p>
                </div>
                <a href="{{ route('community.events.create') }}" 
                   class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                    Buat Event
                </a>
            </div>

            <!-- Filters -->
            <div class="mb-6 flex items-center gap-4">
                <a href="{{ route('community.events.index', ['filter' => 'upcoming']) }}" 
                   class="px-4 py-2 rounded-lg {{ request('filter') !== 'past' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
                    Mendatang
                </a>
                <a href="{{ route('community.events.index', ['filter' => 'past']) }}" 
                   class="px-4 py-2 rounded-lg {{ request('filter') === 'past' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors">
                    Selesai
                </a>
                <a href="{{ route('community.events.calendar') }}" 
                   class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Kalender
                </a>
            </div>

            <!-- Events Grid -->
            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            <!-- Event Header -->
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $event->title }}</h3>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-semibold">
                                                {{ strtoupper($event->event_type) }}
                                            </span>
                                            @if($event->isFull())
                                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                                    PENUH
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Content -->
                            <div class="p-6">
                                @if($event->description)
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $event->description }}</p>
                                @endif

                                <!-- Event Details -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $event->start_date->format('d M Y, H:i') }}</span>
                                    </div>
                                    @if($event->location)
                                        <div class="flex items-center gap-2 text-sm text-gray-700">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>{{ $event->location }}</span>
                                        </div>
                                    @endif
                                    @if($event->online_link)
                                        <div class="flex items-center gap-2 text-sm text-gray-700">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <span>Online</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Organizer & Attendees -->
                                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">
                                                {{ strtoupper(substr($event->user->full_name ?? $event->user->name ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $event->user->full_name ?? $event->user->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-sm text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <span>{{ $event->attendees->where('status', 'registered')->count() }}{{ $event->max_attendees ? '/' . $event->max_attendees : '' }}</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <a href="{{ route('community.events.show', $event->id) }}" 
                                   class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada event</h3>
                    <p class="text-gray-500 mb-4">Jadilah yang pertama membuat event!</p>
                    <a href="{{ route('community.events.create') }}" 
                       class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                        Buat Event Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

