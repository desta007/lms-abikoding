<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('instructor.exams.show', $exam->id) }}" 
                   class="text-indigo-600 hover:text-indigo-800 mb-4 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Quiz</h1>
                <p class="text-gray-600">{{ $exam->title }}</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <ul class="list-disc list-inside text-sm text-red-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg p-6">
                <form action="{{ route('instructor.exams.update', $exam->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Course Selection -->
                    <div class="mb-6">
                        <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kursus <span class="text-red-500">*</span>
                        </label>
                        <select name="course_id" 
                                id="course_id" 
                                required
                                class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Pilih Kursus</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $exam->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Chapter Selection -->
                    <div class="mb-6">
                        <label for="chapter_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Bab (Opsional)
                        </label>
                        <select name="chapter_id" 
                                id="chapter_id" 
                                class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Tidak ada bab spesifik</option>
                            @if($exam->course && $exam->course->chapters)
                                @foreach($exam->course->chapters as $chapter)
                                    <option value="{{ $chapter->id }}" {{ old('chapter_id', $exam->chapter_id) == $chapter->id ? 'selected' : '' }}>
                                        Bab {{ $chapter->order }}: {{ $chapter->title }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            Judul Quiz <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $exam->title) }}"
                               required
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Contoh: Quiz Bab 1 - Pengenalan">
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Jelaskan tentang quiz ini...">{{ old('description', $exam->description) }}</textarea>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Mulai (Opsional)
                            </label>
                            <input type="datetime-local" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', $exam->start_date ? $exam->start_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Berakhir (Opsional)
                            </label>
                            <input type="datetime-local" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date', $exam->end_date ? $exam->end_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="mb-6">
                        <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                            Durasi (Menit) - Opsional
                        </label>
                        <input type="number" 
                               id="duration_minutes" 
                               name="duration_minutes" 
                               value="{{ old('duration_minutes', $exam->duration_minutes) }}"
                               min="1"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Contoh: 60">
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ada batas waktu</p>
                    </div>

                    <!-- Minimum Passing Score -->
                    <div class="mb-6">
                        <label for="minimum_passing_score" class="block text-sm font-semibold text-gray-700 mb-2">
                            Minimum Passing Score (%) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="minimum_passing_score" 
                               name="minimum_passing_score" 
                               value="{{ old('minimum_passing_score', $exam->minimum_passing_score) }}"
                               min="0"
                               max="100"
                               required
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">Nilai minimum yang harus dicapai siswa untuk lulus (0-100%)</p>
                    </div>

                    <!-- Progression Settings -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Progresi</h3>
                        
                        <div class="space-y-4">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" 
                                       name="is_required_for_progression" 
                                       value="1"
                                       {{ old('is_required_for_progression', $exam->is_required_for_progression) ? 'checked' : '' }}
                                       class="mt-1 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-900">Wajib Lulus untuk Lanjut</span>
                                    <p class="text-xs text-gray-600">Jika dicentang, siswa harus lulus quiz ini untuk mengakses materi/bab berikutnya</p>
                                </div>
                            </label>

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" 
                                       name="auto_complete_on_pass" 
                                       value="1"
                                       {{ old('auto_complete_on_pass', $exam->auto_complete_on_pass) ? 'checked' : '' }}
                                       class="mt-1 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-900">Auto-complete saat Lulus</span>
                                    <p class="text-xs text-gray-600">Jika dicentang, materi/bab akan otomatis selesai saat siswa lulus quiz (tanpa perlu approval instruktur)</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $exam->is_active) ? 'checked' : '' }}
                                   class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-gray-900">Aktif</span>
                        </label>
                        <p class="mt-1 ml-8 text-sm text-gray-500">Quiz yang tidak aktif tidak akan terlihat oleh siswa</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('instructor.exams.show', $exam->id) }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Load chapters when course is selected
        document.getElementById('course_id')?.addEventListener('change', function() {
            const courseId = this.value;
            const chapterSelect = document.getElementById('chapter_id');
            
            // Clear existing options except the first one
            chapterSelect.innerHTML = '<option value="">Tidak ada bab spesifik</option>';
            
            if (!courseId) {
                return;
            }
            
            // Fetch chapters for selected course
            fetch(`/api/courses/${courseId}/chapters`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(chapter => {
                        const option = document.createElement('option');
                        option.value = chapter.id;
                        option.textContent = `Bab ${chapter.order}: ${chapter.title}`;
                        chapterSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading chapters:', error);
                });
        });
    </script>
</x-app-layout>
