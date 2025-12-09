<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Kalender Event</h1>
                    <p class="text-gray-600">Lihat semua event dalam kalender</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('community.events.index') }}" 
                       class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors border border-gray-300">
                        Daftar Event
                    </a>
                    <a href="{{ route('community.events.create') }}" 
                       class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                        Buat Event
                    </a>
                </div>
            </div>

            <!-- Month Navigation -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ \Carbon\Carbon::create($year, $month, 1)->locale('id')->translatedFormat('F Y') }}
                    </h2>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('community.events.calendar', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <a href="{{ route('community.events.calendar') }}" 
                           class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-sm font-medium">
                            Hari Ini
                        </a>
                        <a href="{{ route('community.events.calendar', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-2">
                    <!-- Day Headers -->
                    @php
                        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    @endphp
                    @foreach($days as $day)
                        <div class="text-center font-semibold text-gray-700 py-2">
                            {{ $day }}
                        </div>
                    @endforeach

                    <!-- Calendar Days -->
                    @php
                        $firstDay = \Carbon\Carbon::create($year, $month, 1);
                        $lastDay = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
                        $startDate = $firstDay->copy()->startOfWeek();
                        $endDate = $lastDay->copy()->endOfWeek();
                        $currentDate = $startDate->copy();
                        $today = now();
                    @endphp

                    @while($currentDate <= $endDate)
                        <div class="min-h-[100px] border border-gray-200 rounded-lg p-2 {{ $currentDate->month != $month ? 'bg-gray-50' : 'bg-white' }} {{ $currentDate->isToday() ? 'ring-2 ring-indigo-500' : '' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium {{ $currentDate->month != $month ? 'text-gray-400' : ($currentDate->isToday() ? 'text-indigo-600 font-bold' : 'text-gray-900') }}">
                                    {{ $currentDate->day }}
                                </span>
                                @if($currentDate->month == $month)
                                    @php
                                        $dayEvents = $events->filter(function($event) use ($currentDate) {
                                            return $event->start_date->format('Y-m-d') == $currentDate->format('Y-m-d');
                                        });
                                    @endphp
                                    @if($dayEvents->count() > 0)
                                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-semibold">
                                            {{ $dayEvents->count() }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                            <div class="space-y-1 mt-1">
                                @if($currentDate->month == $month)
                                    @foreach($dayEvents->take(2) as $event)
                                        <a href="{{ route('community.events.show', $event->id) }}" 
                                           class="block text-xs px-2 py-1 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 transition-colors truncate"
                                           title="{{ $event->title }}">
                                            {{ $event->start_date->format('H:i') }} - {{ Str::limit($event->title, 15) }}
                                        </a>
                                    @endforeach
                                    @if($dayEvents->count() > 2)
                                        <div class="text-xs text-gray-500 px-2">
                                            +{{ $dayEvents->count() - 2 }} lainnya
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        @php
                            $currentDate->addDay();
                        @endphp
                    @endwhile
                </div>
            </div>

            <!-- Events List for Current Month -->
            @if($events->count() > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Event Bulan Ini</h3>
                    <div class="space-y-4">
                        @foreach($events as $event)
                            <div class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex flex-col items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ $event->start_date->format('d') }}</span>
                                        <span class="text-white text-xs">{{ $event->start_date->format('M') }}</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-1">{{ $event->title }}</h4>
                                    <div class="flex items-center gap-4 text-sm text-gray-600 mb-2">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>{{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }}</span>
                                        </div>
                                        @if($event->location)
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span>{{ $event->location }}</span>
                                            </div>
                                        @endif
                                        @if($event->online_link)
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                </svg>
                                                <span>Online</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($event->description)
                                        <p class="text-sm text-gray-600 line-clamp-2 mb-2">{{ $event->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs font-semibold">
                                            {{ strtoupper($event->event_type) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $event->attendees->where('status', 'registered')->count() }}{{ $event->max_attendees ? '/' . $event->max_attendees : '' }} peserta
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('community.events.show', $event->id) }}" 
                                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada event bulan ini</h3>
                    <p class="text-gray-500 mb-4">Belum ada event yang dijadwalkan untuk bulan ini.</p>
                    <a href="{{ route('community.events.create') }}" 
                       class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                        Buat Event
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

