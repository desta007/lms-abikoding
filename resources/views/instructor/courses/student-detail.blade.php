<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Siswa</h1>
                    <p class="text-gray-600 mt-2">{{ $course->title }}</p>
                </div>
                <a href="{{ route('instructor.courses.students', $course->id) }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition-colors">
                    Kembali ke Daftar Siswa
                </a>
            </div>

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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Student Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="text-center mb-6">
                            <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-white font-bold text-3xl">
                                    {{ strtoupper(substr($enrollment->user->full_name ?? $enrollment->user->name ?? 'S', 0, 1)) }}
                                </span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $enrollment->user->full_name ?? $enrollment->user->name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ $enrollment->user->email }}</p>
                        </div>

                        <div class="space-y-4 border-t border-gray-200 pt-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Tanggal Daftar</span>
                                <p class="text-sm text-gray-900 mt-1">{{ $enrollment->enrolled_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Progress</span>
                                <div class="mt-2">
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $enrollment->calculateProgress() }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-900 mt-1">{{ $enrollment->calculateProgress() }}% selesai</p>
                                </div>
                            </div>
                            @if($enrollment->completed_at)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Selesai</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ $enrollment->completed_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Rating Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
                            {{ $studentRating ? 'Edit Rating Siswa' : 'Berikan Rating Siswa' }}
                        </h3>

                        <form action="{{ route('instructor.student-ratings.store', [$course->id, $enrollment->user->id]) }}" method="POST" id="rating-form">
                            @csrf
                            @if($studentRating)
                                @method('PUT')
                            @endif

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                                <div class="flex items-center gap-2" id="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-btn focus:outline-none" data-rating="{{ $i }}">
                                            <svg class="w-10 h-10 {{ $studentRating && $i <= $studentRating->rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="{{ $studentRating ? $studentRating->rating : '' }}" required>
                                @error('rating')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Review</label>
                                <textarea name="review" id="review" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tulis review tentang performa siswa ini...">{{ $studentRating ? $studentRating->review : old('review') }}</textarea>
                                @error('review')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    {{ $studentRating ? 'Perbarui Rating' : 'Simpan Rating' }}
                                </button>
                                @if($studentRating)
                                    <form action="{{ route('instructor.student-ratings.destroy', $studentRating->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rating ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                            Hapus Rating
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </form>

                        @if($studentRating)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Rating Saat Ini</h4>
                                <div class="flex items-center gap-2 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $studentRating->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="text-sm text-gray-600">{{ $studentRating->created_at->format('d M Y') }}</span>
                                </div>
                                @if($studentRating->review)
                                    <p class="text-sm text-gray-700">{{ $studentRating->review }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Star rating functionality
        document.addEventListener('DOMContentLoaded', function() {
            const starButtons = document.querySelectorAll('.star-btn');
            const ratingInput = document.getElementById('rating-input');
            
            if (starButtons.length > 0 && ratingInput) {
                let currentRating = ratingInput.value ? parseInt(ratingInput.value) : 0;
                
                starButtons.forEach((btn, index) => {
                    const rating = index + 1;
                    
                    btn.addEventListener('click', function() {
                        currentRating = rating;
                        ratingInput.value = rating;
                        updateStars(rating);
                    });
                    
                    btn.addEventListener('mouseenter', function() {
                        updateStars(rating);
                    });
                });
                
                const starContainer = document.getElementById('star-rating');
                if (starContainer) {
                    starContainer.addEventListener('mouseleave', function() {
                        updateStars(currentRating);
                    });
                }
                
                function updateStars(rating) {
                    starButtons.forEach((btn, index) => {
                        const star = btn.querySelector('svg');
                        if (index < rating) {
                            star.classList.remove('text-gray-300');
                            star.classList.add('text-yellow-400');
                        } else {
                            star.classList.remove('text-yellow-400');
                            star.classList.add('text-gray-300');
                        }
                    });
                }
            }
        });
    </script>
</x-app-layout>

