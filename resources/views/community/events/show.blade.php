<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <a href="{{ route('community.events.index') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Events
            </a>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Event Header -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                                {{ strtoupper($event->event_type) }}
                            </span>
                            @if($event->isFull())
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                    PENUH
                                </span>
                            @endif
                        </div>
                        
                        <!-- Event Date & Time -->
                        <div class="flex items-center gap-4 text-gray-600 mb-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-medium">{{ $event->start_date->format('d M Y, H:i') }}</span>
                                @if($event->end_date)
                                    <span> - {{ $event->end_date->format('H:i') }}</span>
                                @endif
                            </div>
                            @if($event->timezone)
                                <span class="text-sm">({{ $event->timezone }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Organizer Info -->
                <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-200">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold">
                            {{ strtoupper(substr($event->user->full_name ?? $event->user->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Organizer</p>
                        <p class="text-sm text-gray-600">{{ $event->user->full_name ?? $event->user->name }}</p>
                    </div>
                </div>

                <!-- Event Description -->
                @if($event->description)
                    <div class="prose max-w-none mb-6">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $event->description }}</p>
                    </div>
                @endif

                <!-- Event Details -->
                <div class="space-y-3 mb-6">
                    @if($event->location)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Lokasi</p>
                                <p class="text-gray-600">{{ $event->location }}</p>
                            </div>
                        </div>
                    @endif

                    @if($event->online_link)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Link Online</p>
                                <a href="{{ $event->online_link }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                                    {{ $event->online_link }}
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Peserta</p>
                            <p class="text-gray-600">
                                {{ $event->attendees->where('status', 'registered')->count() }}{{ $event->max_attendees ? '/' . $event->max_attendees : '' }} terdaftar
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Registration Actions -->
                @if($event->canRegister() && !$isRegistered)
                    <form action="{{ route('community.events.register', $event->id) }}" method="POST" class="mb-4">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                            Daftar Event
                        </button>
                    </form>
                @elseif($isRegistered && $attendee)
                    @if($attendee->status === 'registered')
                        <form action="{{ route('community.events.cancel', $event->id) }}" method="POST" class="mb-4" onsubmit="return confirm('Batalkan pendaftaran untuk event ini?')">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-colors">
                                Batalkan Pendaftaran
                            </button>
                        </form>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                            <p class="text-sm text-green-800 font-medium">✓ Anda sudah terdaftar untuk event ini</p>
                            @if($attendee->registered_at)
                                <p class="text-xs text-green-600 mt-1">Terdaftar pada {{ $attendee->registered_at->format('d M Y, H:i') }}</p>
                            @endif
                        </div>
                    @elseif($attendee->status === 'cancelled')
                        <form action="{{ route('community.events.register', $event->id) }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                                Daftar Kembali
                            </button>
                        </form>
                    @endif
                @elseif(!$event->canRegister())
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                        <p class="text-sm text-yellow-800 font-medium">
                            @if($event->isFull())
                                Event ini sudah penuh
                            @else
                                Pendaftaran sudah ditutup (event sudah dimulai)
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <!-- Attendees List -->
            @if($event->attendees->where('status', 'registered')->count() > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Peserta ({{ $event->attendees->where('status', 'registered')->count() }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($event->attendees->where('status', 'registered') as $attendee)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">
                                        {{ strtoupper(substr($attendee->user->full_name ?? $attendee->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $attendee->user->full_name ?? $attendee->user->name }}</p>
                                    @if($attendee->registered_at)
                                        <p class="text-xs text-gray-500">Terdaftar {{ $attendee->registered_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

