<x-app-layout>
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                    Source Code Berkualitas
                    <span class="block text-yellow-300">Siap Pakai & Mudah Dikustomisasi</span>
                </h1>
                <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                    Temukan berbagai source code berkualitas tinggi untuk mempercepat pengembangan aplikasi Anda
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#source-codes" class="px-8 py-4 bg-white text-emerald-600 rounded-lg font-semibold hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg">
                        Jelajahi Source Code
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-emerald-700 text-white rounded-lg font-semibold hover:bg-emerald-800 transition-all transform hover:scale-105 shadow-lg">
                            Daftar Sekarang
                        </a>
                    @endguest
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
            </svg>
        </div>
    </div>

    <div class="bg-gray-50 py-12" id="source-codes">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search and Filter Section -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <form method="GET" 
                          action="{{ route('source-codes.index') }}" 
                          data-ajax-search="true"
                          data-results-container="#source-codes-container-wrapper"
                          data-loading-indicator=".search-loading"
                          class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       data-live-search="true"
                                       placeholder="Cari source code, teknologi, atau topik..." 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>
                        <input type="hidden" name="view" value="{{ request('view', 'grid') }}">
                        <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition-all shadow-md hover:shadow-lg">
                            Cari
                        </button>
                    </form>
                    <div class="search-loading hidden mt-4 text-center">
                        <div class="inline-flex items-center text-emerald-600">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mencari...
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Filters Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter
                        </h2>
                        
                        <form id="filter-form" 
                              method="GET" 
                              action="{{ route('source-codes.index') }}"
                              data-ajax-filter="true"
                              data-ajax-search="true"
                              data-results-container="#source-codes-container-wrapper"
                              data-loading-indicator=".search-loading">
                            @foreach(request()->except(['category', 'level', 'sort', 'price_range', 'technology']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="hidden" name="view" value="{{ request('view', 'grid') }}">
                            
                            <!-- Sort -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Urutkan</label>
                                    <select name="sort" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                                </select>
                            </div>

                            <!-- Price Filter -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Harga</label>
                                <div class="space-y-2">
                                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="price_range" value="all" {{ !request('price_range') || request('price_range') == 'all' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-3 text-sm text-gray-700">Semua</span>
                                    </label>
                                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="price_range" value="free" {{ request('price_range') == 'free' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-3 text-sm text-gray-700">Gratis</span>
                                    </label>
                                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="price_range" value="paid" {{ request('price_range') == 'paid' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-3 text-sm text-gray-700">Berbayar</span>
                                    </label>
                                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="price_range" value="under_100k" {{ request('price_range') == 'under_100k' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-3 text-sm text-gray-700">Di bawah Rp 100.000</span>
                                    </label>
                                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="price_range" value="100k_500k" {{ request('price_range') == '100k_500k' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-3 text-sm text-gray-700">Rp 100.000 - Rp 500.000</span>
                                    </label>
                                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="price_range" value="over_500k" {{ request('price_range') == 'over_500k' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-3 text-sm text-gray-700">Di atas Rp 500.000</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Kategori</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($categories as $category)
                                        <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" 
                                                   name="category[]" 
                                                   value="{{ $category->id }}"
                                                   {{ in_array($category->id, (array)request('category')) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                            <span class="ml-3 text-sm text-gray-700">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Level Filter -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Level</label>
                                <div class="space-y-2">
                                    @foreach($levels as $level)
                                        <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" 
                                                   name="level[]" 
                                                   value="{{ $level->id }}"
                                                   {{ in_array($level->id, (array)request('level')) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                            <span class="ml-3 text-sm text-gray-700">{{ $level->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Technology Filter -->
                            @if($technologies->count() > 0)
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Teknologi</label>
                                    <select name="technology" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="">Semua Teknologi</option>
                                        @foreach($technologies as $technology)
                                            <option value="{{ $technology }}" {{ request('technology') == $technology ? 'selected' : '' }}>{{ $technology }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <button type="submit" class="w-full px-4 py-3 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition-all shadow-md hover:shadow-lg">
                                Terapkan Filter
                            </button>
                            <div class="search-loading hidden mt-4 text-center">
                                <div class="inline-flex items-center text-emerald-600">
                                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memuat...
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Source Codes Grid -->
                <div class="lg:col-span-3" id="source-codes-results-section">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Daftar Source Code</h2>
                            <p class="text-gray-600 mt-1" id="source-codes-count">{{ $sourceCodes->total() }} source code tersedia</p>
                        </div>
                        <!-- View Toggle -->
                        <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                            <button onclick="setView('grid')" id="grid-view-btn" class="p-2 rounded {{ request('view', 'grid') == 'grid' ? 'bg-white shadow-sm' : '' }} transition-all">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </button>
                            <button onclick="setView('list')" id="list-view-btn" class="p-2 rounded {{ request('view') == 'list' ? 'bg-white shadow-sm' : '' }} transition-all">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div id="source-codes-container-wrapper">
                        @include('partials.source-codes-list', ['sourceCodes' => $sourceCodes])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setView(view) {
            const url = new URL(window.location.href);
            url.searchParams.set('view', view);
            window.location.href = url.toString();
        }

        // Update view toggle buttons on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currentView = '{{ request("view", "grid") }}';
            const gridBtn = document.getElementById('grid-view-btn');
            const listBtn = document.getElementById('list-view-btn');
            
            if (currentView === 'grid') {
                gridBtn.classList.add('bg-white', 'shadow-sm');
                listBtn.classList.remove('bg-white', 'shadow-sm');
            } else {
                listBtn.classList.add('bg-white', 'shadow-sm');
                gridBtn.classList.remove('bg-white', 'shadow-sm');
            }
        });
    </script>
</x-app-layout>
