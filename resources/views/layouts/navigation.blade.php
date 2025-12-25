<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-500 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-xl">LMS</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900 hidden sm:block">DC Tech</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                        {{ __('Beranda') }}
                    </x-nav-link>
                    <x-nav-link :href="route('source-codes.index')" :active="request()->routeIs('source-codes.*')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                        {{ __('Source Code') }}
                    </x-nav-link>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @elseif(auth()->user()->isInstructor())
                            <x-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.dashboard')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @else
                            <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @endif
                        {{-- Hide Ujian menu for students --}}
                        {{-- @if(auth()->user()->isStudent())
                            <x-nav-link :href="route('student.exams.index')" :active="request()->routeIs('student.exams.*')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                {{ __('Ujian') }}
                            </x-nav-link>
                        @endif --}}
                        @if(auth()->user()->isInstructor() || auth()->user()->isAdmin())
                            <x-nav-link :href="route('instructor.courses.index')" :active="request()->routeIs('instructor.courses.*')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                {{ __('Kursus') }}
                            </x-nav-link>
                            <x-nav-link :href="route('instructor.source-codes.index')" :active="request()->routeIs('instructor.source-codes.*')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                {{ __('Kelola Source Code') }}
                            </x-nav-link>
                            <x-nav-link :href="route('instructor.progress.index')" :active="request()->routeIs('instructor.progress.*')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                {{ __('Progress Siswa') }}
                            </x-nav-link>
                            <x-nav-link :href="route('instructor.exams.retake-requests')" :active="request()->routeIs('instructor.exams.retake-requests')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                {{ __('Permintaan Ulang Quiz') }}
                            </x-nav-link>
                        @endif
                        <x-nav-link :href="route('community.index')" :active="request()->routeIs('community.*')" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                            {{ __('Komunitas') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @auth
                    <!-- Notifications Dropdown -->
                    @php
                        $initialUnreadCount = \App\Models\Notification::where('user_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    <div x-data="{ 
                        open: false, 
                        unreadCount: {{ $initialUnreadCount }}, 
                        notifications: [],
                        loadUnreadCount() {
                            fetch('{{ route('notifications.unread-count') }}')
                                .then(response => response.json())
                                .then(data => {
                                    this.unreadCount = parseInt(data.count) || 0;
                                })
                                .catch(error => {
                                    console.error('Error loading unread count:', error);
                                });
                        }
                    }" 
                         x-init="
                            loadUnreadCount();
                            setInterval(() => loadUnreadCount(), 30000);
                         "
                         class="relative">
                        <button @click="
                            open = !open;
                            if (open && notifications.length === 0) {
                                fetch('{{ route('notifications.index') }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        notifications = data.notifications.data || [];
                                    })
                                    .catch(error => console.error('Error loading notifications:', error));
                            }
                        " 
                                class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span x-show="unreadCount > 0" 
                                  x-text="unreadCount > 99 ? '99+' : unreadCount.toString()"
                                  class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1.25rem] h-5 transform -translate-y-1/2 translate-x-1/2"
                                  style="display: none;"></span>
                        </button>

                        <!-- Notifications Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.outside="open = false"
                             class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Notifikasi</h3>
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" 
                                          @submit.prevent="
                                              fetch('{{ route('notifications.mark-all-read') }}', { 
                                                  method: 'POST', 
                                                  headers: { 
                                                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                      'Content-Type': 'application/json',
                                                      'Accept': 'application/json'
                                                  } 
                                              })
                                              .then(() => {
                                                  unreadCount = 0;
                                                  notifications.forEach(n => n.is_read = true);
                                              });
                                          ">
                                        @csrf
                                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                            Tandai semua sudah dibaca
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="p-8 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <p class="mt-2 text-sm">Tidak ada notifikasi</p>
                                    </div>
                                </template>
                                <template x-for="notification in notifications" :key="notification.id">
                                    <a :href="notification.link || '#'" 
                                       @click.prevent="
                                           if (!notification.is_read) {
                                               fetch(`/notifications/${notification.id}/read`, { 
                                                   method: 'POST', 
                                                   headers: { 
                                                       'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                       'Content-Type': 'application/json',
                                                       'Accept': 'application/json'
                                                   } 
                                               })
                                               .then(response => response.json())
                                               .then(data => {
                                                   if (data.success) {
                                                       notification.is_read = true;
                                                       if (unreadCount > 0) {
                                                           unreadCount = Math.max(0, unreadCount - 1);
                                                       }
                                                       // Navigate to link after marking as read
                                                       if (notification.link && notification.link !== '#') {
                                                           window.location.href = notification.link;
                                                       }
                                                   }
                                               })
                                               .catch(error => {
                                                   console.error('Error marking as read:', error);
                                                   // Still navigate even if mark as read fails
                                                   if (notification.link && notification.link !== '#') {
                                                       window.location.href = notification.link;
                                                   }
                                               });
                                           } else {
                                               // Already read, just navigate
                                               if (notification.link && notification.link !== '#') {
                                                   window.location.href = notification.link;
                                               }
                                           }
                                       "
                                       class="block px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer"
                                       :class="notification.is_read ? 'bg-white' : 'bg-indigo-50'">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-1">
                                                <template x-if="notification.type === 'material_approval_request' || notification.type === 'quiz_retake_request'">
                                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                </template>
                                                <template x-if="notification.type === 'material_approved' || notification.type === 'quiz_retake_approved'">
                                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                </template>
                                                <template x-if="notification.type === 'material_rejected' || notification.type === 'quiz_retake_rejected'">
                                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                                                <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="notification.message"></p>
                                                <p class="text-xs text-gray-400 mt-1" x-text="new Date(notification.created_at).toLocaleString('id-ID')"></p>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <div x-show="!notification.is_read" class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                                <button @click.stop="
                                                    if (confirm('Hapus notifikasi ini?')) {
                                                        fetch(`/notifications/${notification.id}`, {
                                                            method: 'DELETE',
                                                            headers: {
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                'Content-Type': 'application/json',
                                                                'Accept': 'application/json'
                                                            }
                                                        })
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            if (data.success) {
                                                                // Remove from array
                                                                notifications = notifications.filter(n => n.id !== notification.id);
                                                                // Decrease unread count if it was unread
                                                                if (!notification.is_read && unreadCount > 0) {
                                                                    unreadCount = Math.max(0, unreadCount - 1);
                                                                }
                                                            }
                                                        })
                                                        .catch(error => {
                                                            console.error('Error deleting notification:', error);
                                                            alert('Gagal menghapus notifikasi');
                                                        });
                                                    }
                                                " 
                                                        class="text-gray-400 hover:text-red-600 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                            <div class="p-3 border-t border-gray-200 text-center">
                                <a href="{{ route('notifications.all') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Lihat semua notifikasi
                                </a>
                            </div>
                        </div>
                    </div>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-white text-xs font-bold">
                                        {{ strtoupper(substr(Auth::user()?->full_name ?? Auth::user()?->name ?? Auth::user()?->email ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <span class="hidden md:block">{{ Auth::user()?->full_name ?? Auth::user()?->name ?? Auth::user()?->email ?? 'User' }}</span>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.show')" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('Profil') }}
                            </x-dropdown-link>
                            
                            @if(auth()->user()->isStudent())
                                <x-dropdown-link :href="route('certificates.history')" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ __('Sertifikat') }}
                                </x-dropdown-link>
                            @endif
                            
                            @if(auth()->user()->isAdmin())
                                <x-dropdown-link :href="route('admin.settings.bank-account')" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    {{ __('Rekening Bank') }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('Pengaturan') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();" class="flex items-center text-red-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ __('Keluar') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Beranda') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('source-codes.index')" :active="request()->routeIs('source-codes.*')">
                {{ __('Source Code') }}
            </x-responsive-nav-link>
            @auth
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->isInstructor())
                    <x-responsive-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->isStudent())
                    <x-responsive-nav-link :href="route('student.exams.index')" :active="request()->routeIs('student.exams.*')">
                        {{ __('Ujian') }}
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->isInstructor() || auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('instructor.courses.index')" :active="request()->routeIs('instructor.courses.*')">
                        {{ __('Kursus') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('instructor.source-codes.index')" :active="request()->routeIs('instructor.source-codes.*')">
                        {{ __('Kelola Source Code') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('instructor.progress.index')" :active="request()->routeIs('instructor.progress.*')">
                        {{ __('Progress Siswa') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('instructor.exams.retake-requests')" :active="request()->routeIs('instructor.exams.retake-requests')">
                        {{ __('Permintaan Ulang Quiz') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()?->full_name ?? Auth::user()?->name ?? Auth::user()?->email ?? 'User' }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()?->email ?? '' }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.show')">
                        {{ __('Profil') }}
                    </x-responsive-nav-link>
                    @if(auth()->user()->isStudent())
                        <x-responsive-nav-link :href="route('certificates.history')">
                            {{ __('Sertifikat') }}
                        </x-responsive-nav-link>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <x-responsive-nav-link :href="route('admin.settings.bank-account')">
                            {{ __('Rekening Bank') }}
                        </x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Pengaturan') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Keluar') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200 px-4 space-y-2">
                <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Daftar
                </a>
            </div>
        @endauth
    </div>
</nav>
