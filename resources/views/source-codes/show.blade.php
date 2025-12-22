<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <!-- Breadcrumb -->
        <div class="bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <a href="{{ route('source-codes.index') }}" class="text-gray-500 hover:text-gray-700 ml-1 md:ml-2 text-sm font-medium">Source Code</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-emerald-600 ml-1 md:ml-2 text-sm font-medium">{{ $sourceCode->title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Thumbnail -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                        @if($sourceCode->thumbnail)
                            <img src="{{ asset('storage/' . $sourceCode->thumbnail) }}" alt="{{ $sourceCode->title }}" class="w-full h-72 object-cover">
                        @else
                            <div class="w-full h-72 bg-gradient-to-br from-emerald-400 via-teal-400 to-cyan-400 flex items-center justify-center">
                                <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Title and Meta -->
                    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-full">{{ $sourceCode->category->name }}</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">{{ $sourceCode->level->name }}</span>
                        </div>
                        
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $sourceCode->title }}</h1>
                        
                        @if($sourceCode->subtitle)
                            <p class="text-lg text-gray-600 mb-6">{{ $sourceCode->subtitle }}</p>
                        @endif

                        <!-- Technologies -->
                        @if($sourceCode->technologies && count($sourceCode->technologies) > 0)
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Teknologi yang Digunakan:</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($sourceCode->technologies as $tech)
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">{{ $tech }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Instructor -->
                        <div class="flex items-center gap-4 pt-6 border-t border-gray-200">
                            <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                                <span class="text-lg font-bold text-emerald-600">
                                    {{ substr($sourceCode->instructor->full_name ?? $sourceCode->instructor->name ?? 'I', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Dibuat oleh</p>
                                <p class="font-semibold text-gray-900">{{ $sourceCode->instructor->full_name ?? $sourceCode->instructor->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Deskripsi</h2>
                        <div class="prose max-w-none text-gray-600">
                            {!! nl2br(e($sourceCode->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                        <!-- Price -->
                        <div class="text-center mb-6">
                            @if($sourceCode->isFree())
                                <span class="text-3xl font-bold text-green-600">Gratis</span>
                            @else
                                <span class="text-3xl font-bold text-emerald-600">Rp {{ number_format($sourceCode->price, 0, ',', '.') }}</span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3 mb-6">
                            @if($sourceCode->download_url)
                                <a href="{{ $sourceCode->download_url }}" target="_blank" class="block w-full px-6 py-3 bg-emerald-600 text-white text-center rounded-lg font-semibold hover:bg-emerald-700 transition-colors">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                            @endif

                            @if($sourceCode->github_url)
                                <a href="{{ $sourceCode->github_url }}" target="_blank" class="block w-full px-6 py-3 bg-gray-800 text-white text-center rounded-lg font-semibold hover:bg-gray-900 transition-colors">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                                    </svg>
                                    View on GitHub
                                </a>
                            @endif

                            @if($sourceCode->demo_url)
                                <a href="{{ $sourceCode->demo_url }}" target="_blank" class="block w-full px-6 py-3 bg-blue-600 text-white text-center rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Live Demo
                                </a>
                            @endif
                        </div>

                        <!-- Info Cards -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span class="text-sm text-gray-700">Kode berkualitas tinggi</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="text-sm text-gray-700">Update berkala</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span class="text-sm text-gray-700">Dukungan teknis</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Source Codes -->
            @if($relatedSourceCodes->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Source Code Terkait</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedSourceCodes as $related)
                            <x-source-code-card :sourceCode="$related" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
