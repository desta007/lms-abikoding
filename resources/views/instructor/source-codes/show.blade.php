<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <a href="{{ route('instructor.source-codes.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('instructor.source-codes.edit', $sourceCode->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                        Edit
                    </a>
                    <form action="{{ route('instructor.source-codes.publish', $sourceCode->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 {{ $sourceCode->is_published ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-md">
                            {{ $sourceCode->is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Thumbnail -->
                @if($sourceCode->thumbnail)
                    <img src="{{ asset('storage/' . $sourceCode->thumbnail) }}" alt="{{ $sourceCode->title }}" class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 bg-gradient-to-br from-emerald-400 via-teal-400 to-cyan-400 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                @endif

                <div class="p-8">
                    <!-- Status Badge -->
                    <div class="flex gap-2 mb-4">
                        @if($sourceCode->is_published)
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Published</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Draft</span>
                        @endif
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-full">{{ $sourceCode->category->name }}</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">{{ $sourceCode->level->name }}</span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $sourceCode->title }}</h1>
                    @if($sourceCode->subtitle)
                        <p class="text-lg text-gray-600 mb-6">{{ $sourceCode->subtitle }}</p>
                    @endif

                    <!-- Price -->
                    <div class="mb-6">
                        @if($sourceCode->price == 0)
                            <span class="text-2xl font-bold text-green-600">Gratis</span>
                        @else
                            <span class="text-2xl font-bold text-emerald-600">Rp {{ number_format($sourceCode->price, 0, ',', '.') }}</span>
                        @endif
                    </div>

                    <!-- Technologies -->
                    @if($sourceCode->technologies && count($sourceCode->technologies) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Teknologi:</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($sourceCode->technologies as $tech)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">{{ $tech }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- URLs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        @if($sourceCode->github_url)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">GitHub</h4>
                                <a href="{{ $sourceCode->github_url }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 break-all text-sm">
                                    {{ $sourceCode->github_url }}
                                </a>
                            </div>
                        @endif
                        @if($sourceCode->demo_url)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Demo</h4>
                                <a href="{{ $sourceCode->demo_url }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 break-all text-sm">
                                    {{ $sourceCode->demo_url }}
                                </a>
                            </div>
                        @endif
                        @if($sourceCode->download_url)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Download</h4>
                                <a href="{{ $sourceCode->download_url }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 break-all text-sm">
                                    {{ $sourceCode->download_url }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Deskripsi</h3>
                        <div class="prose max-w-none text-gray-600">
                            {!! nl2br(e($sourceCode->description)) !!}
                        </div>
                    </div>

                    <!-- Meta Info -->
                    <div class="border-t pt-6 mt-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Slug:</span>
                                <p class="font-medium text-gray-900">{{ $sourceCode->slug }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Dibuat:</span>
                                <p class="font-medium text-gray-900">{{ $sourceCode->created_at->format('d M Y') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Diupdate:</span>
                                <p class="font-medium text-gray-900">{{ $sourceCode->updated_at->format('d M Y') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Public URL:</span>
                                @if($sourceCode->is_published)
                                    <a href="{{ route('source-codes.show', $sourceCode->slug) }}" target="_blank" class="font-medium text-emerald-600 hover:text-emerald-800">
                                        Lihat
                                    </a>
                                @else
                                    <p class="font-medium text-gray-400">Not Published</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
