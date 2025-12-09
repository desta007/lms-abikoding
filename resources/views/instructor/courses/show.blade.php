<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $course->title }}</h1>
                    <p class="text-gray-600 mt-2">{{ $course->subtitle }}</p>
                </div>
                <div class="flex gap-4 flex-wrap">
                    @if($course->is_published)
                        <form action="{{ route('instructor.courses.publish', $course->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition-colors shadow-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin menyembunyikan kursus ini? Kursus tidak akan terlihat oleh siswa.');">
                                Unpublish Kursus
                            </button>
                        </form>
                    @else
                        <form action="{{ route('instructor.courses.publish', $course->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition-colors shadow-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin mempublikasikan kursus ini? Kursus akan terlihat oleh semua siswa.');">
                                Publish Kursus
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('instructor.courses.edit', $course->id) }}" 
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors shadow-sm">
                        Edit Kursus
                    </a>
                    <form action="{{ route('instructor.courses.destroy', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-colors shadow-sm">
                            Hapus Kursus
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Course Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kursus</h3>
                        <div class="space-y-2 text-sm">
                            <div><span class="font-medium">Kategori:</span> {{ $course->category->name }}</div>
                            <div><span class="font-medium">Level:</span> {{ $course->level->name }}</div>
                            <div><span class="font-medium">Harga:</span> {{ $course->price == 0 ? 'Gratis' : 'Rp ' . number_format($course->price, 0, ',', '.') }}</div>
                            <div><span class="font-medium">Bahasa:</span> {{ $course->language }}</div>
                            <div><span class="font-medium">Status:</span> 
                                <span class="px-2 py-1 text-xs rounded {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $course->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
                        <div class="space-y-2 text-sm">
                            <div><span class="font-medium">Total Bab:</span> {{ $course->chapters()->count() }}</div>
                            <div><span class="font-medium">Total Materi:</span> {{ $course->chapters->sum(fn($ch) => $ch->materials->count()) }}</div>
                            <div class="flex items-center justify-between">
                                <span><span class="font-medium">Siswa Terdaftar:</span> {{ $course->totalEnrollments() }}</span>
                                @if($course->totalEnrollments() > 0)
                                    <a href="{{ route('instructor.courses.students', $course->id) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                        Lihat Daftar →
                                    </a>
                                @endif
                            </div>
                            <div><span class="font-medium">Total Views:</span> {{ $course->totalViews() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chapters Management -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Kelola Bab dan Materi</h2>
                    <button onclick="showAddChapterModal()" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                        + Tambah Bab
                    </button>
                </div>

                @if($course->chapters->count() > 0)
                    <div class="space-y-4" id="chaptersList">
                        @foreach($course->chapters as $chapter)
                            <div class="border border-gray-200 rounded-lg p-4" data-chapter-id="{{ $chapter->id }}">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-lg font-semibold text-gray-900">Bab {{ $chapter->order }}: {{ $chapter->title }}</span>
                                            @if($chapter->is_published)
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Published</span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Draft</span>
                                            @endif
                                        </div>
                                        @if($chapter->description)
                                            <p class="text-sm text-gray-600 mb-2">{{ $chapter->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">
                                            {{ $chapter->materials->count() }} materi
                                            @if($chapter->exams->count() > 0)
                                                • {{ $chapter->exams->count() }} quiz
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex gap-2 flex-wrap">
                                        <button onclick="editChapter({{ $chapter->id }})" 
                                                class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200">
                                            Edit
                                        </button>
                                        <a href="{{ route('instructor.materials.create', $chapter->id) }}" 
                                           class="px-3 py-1 text-sm bg-indigo-100 text-indigo-800 rounded hover:bg-indigo-200">
                                            + Materi
                                        </a>
                                        <a href="{{ route('instructor.chapters.exams.create', $chapter->id) }}" 
                                           class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded hover:bg-green-200">
                                            + Quiz
                                        </a>
                                        <form action="{{ route('instructor.chapters.destroy', $chapter->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus bab ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded hover:bg-red-200">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Quiz List -->
                                @if($chapter->exams->count() > 0)
                                    <div class="ml-4 mt-4 mb-4">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Quiz untuk Bab Ini:</h4>
                                        <div class="space-y-2">
                                            @foreach($chapter->exams as $exam)
                                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                                    <div class="flex items-center gap-3">
                                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <div>
                                                            <span class="text-sm font-medium text-gray-900">{{ $exam->title }}</span>
                                                            <div class="text-xs text-gray-600">
                                                                Passing Score: {{ $exam->minimum_passing_score }}%
                                                                @if($exam->is_required_for_progression)
                                                                    <span class="ml-2 px-2 py-0.5 bg-orange-100 text-orange-800 rounded">Wajib Lulus</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <a href="{{ route('instructor.exams.questions', $exam->id) }}" 
                                                           class="text-sm text-indigo-600 hover:text-indigo-800">
                                                            Kelola
                                                        </a>
                                                        <a href="{{ route('instructor.exams.show', $exam->id) }}" 
                                                           class="text-sm text-blue-600 hover:text-blue-800">
                                                            Detail
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Materials List -->
                                @if($chapter->materials->count() > 0)
                                    <div class="ml-4 mt-4 space-y-2">
                                        @foreach($chapter->materials as $material)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-sm font-medium text-gray-700">{{ $material->title }}</span>
                                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ strtoupper($material->material_type) }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('instructor.materials.edit', $material->id) }}" 
                                                            class="text-sm text-yellow-600 hover:text-yellow-800">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('instructor.materials.destroy', $material->id) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Hapus materi ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="ml-4 mt-4 text-sm text-gray-500">
                                        Belum ada materi. Klik "+ Materi" untuk menambahkan.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-gray-500 mb-4">Belum ada bab. Tambahkan bab pertama untuk memulai!</p>
                        <button onclick="showAddChapterModal()" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                            Tambah Bab Pertama
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Chapter Modal -->
    <div id="addChapterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Tambah Bab Baru</h3>
            <form action="{{ route('instructor.chapters.store', $course->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="chapter_title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Judul Bab <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="chapter_title" 
                           name="title" 
                           required
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Contoh: Bab 1 - Pengenalan">
                </div>

                <div class="mb-4">
                    <label for="chapter_description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="chapter_description" 
                              name="description" 
                              rows="3"
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Deskripsi singkat bab ini..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <button type="button" onclick="hideAddChapterModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Tambah Bab
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Chapter Modal -->
    <div id="editChapterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Edit Bab</h3>
            <form action="" method="POST" id="editChapterForm">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="edit_chapter_title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Judul Bab <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="edit_chapter_title" 
                           name="title" 
                           required
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Contoh: Bab 1 - Pengenalan">
                </div>

                <div class="mb-4">
                    <label for="edit_chapter_description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="edit_chapter_description" 
                              name="description" 
                              rows="3"
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Deskripsi singkat bab ini..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <button type="button" onclick="hideEditChapterModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Material Modal -->
    <div id="editMaterialModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Edit Materi</h3>
            <form action="" method="POST" enctype="multipart/form-data" id="editMaterialForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="chapter_id" id="edit_material_chapter_id">
                <input type="hidden" id="edit_material_type_hidden" name="material_type">
                
                <div class="mb-4">
                    <label for="edit_material_type" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipe Materi
                    </label>
                    <input type="text" 
                           id="edit_material_type" 
                           readonly
                           class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-600 cursor-not-allowed"
                           placeholder="Tipe materi">
                    <p class="mt-1 text-xs text-gray-500">Tipe materi tidak dapat diubah</p>
                </div>

                <div class="mb-4">
                    <label for="edit_material_title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Judul Materi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="edit_material_title" 
                           name="title" 
                           required
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Judul materi">
                </div>

                <div id="edit_material_file_input" class="mb-4 hidden">
                    <label for="edit_material_file" class="block text-sm font-semibold text-gray-700 mb-2">
                        File Baru (Opsional)
                    </label>
                    <p id="edit_current_file" class="text-xs text-gray-600 mb-2"></p>
                    <input type="file" 
                           id="edit_material_file" 
                           name="file"
                           accept=""
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500" id="edit_file_help"></p>
                </div>

                <div id="edit_material_content_input" class="mb-4 hidden">
                    <label for="edit_material_content" class="block text-sm font-semibold text-gray-700 mb-2">
                        Konten <span class="text-red-500">*</span>
                    </label>
                    <textarea id="edit_material_content" 
                              name="content" 
                              rows="5"
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Tulis konten materi di sini..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <button type="button" onclick="hideEditMaterialModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Chapters and Materials data for editing
        const chaptersData = @json($chaptersData);
        const materialsData = @json($materialsData);


        function showAddChapterModal() {
            document.getElementById('addChapterModal').classList.remove('hidden');
        }

        function hideAddChapterModal() {
            document.getElementById('addChapterModal').classList.add('hidden');
        }


        function editChapter(chapterId) {
            const chapter = chaptersData.find(c => c.id == chapterId);
            if (!chapter) {
                console.error('Chapter not found:', chapterId, chaptersData);
                alert('Bab tidak ditemukan');
                return;
            }
            
            document.getElementById('editChapterForm').action = '{{ route("instructor.chapters.update", ":id") }}'.replace(':id', chapterId);
            document.getElementById('edit_chapter_title').value = chapter.title || '';
            document.getElementById('edit_chapter_description').value = chapter.description || '';
            document.getElementById('editChapterModal').classList.remove('hidden');
        }

        function hideEditChapterModal() {
            document.getElementById('editChapterModal').classList.add('hidden');
            document.getElementById('editChapterForm').reset();
        }

        function editMaterial(materialId) {
            const material = materialsData.find(m => m.id == materialId);
            if (!material) {
                console.error('Material not found:', materialId, materialsData);
                alert('Materi tidak ditemukan');
                return;
            }
            
            document.getElementById('editMaterialForm').action = '{{ route("instructor.materials.update", ":id") }}'.replace(':id', materialId);
            document.getElementById('edit_material_chapter_id').value = material.chapter_id;
            document.getElementById('edit_material_title').value = material.title || '';
            
            // Set material type (read-only)
            const materialTypeDisplay = material.material_type ? material.material_type.toUpperCase() : '';
            document.getElementById('edit_material_type').value = materialTypeDisplay;
            document.getElementById('edit_material_type_hidden').value = material.material_type || '';
            
            // Show/hide inputs based on material type
            toggleEditMaterialInputs();
            
            // Set content if text type
            if (material.material_type === 'text') {
                document.getElementById('edit_material_content').value = material.content || '';
            } else if (material.file_path) {
                // Show current file info
                const fileName = material.file_path.split('/').pop();
                document.getElementById('edit_current_file').textContent = 'File saat ini: ' + fileName;
            } else {
                document.getElementById('edit_current_file').textContent = '';
            }
            
            document.getElementById('editMaterialModal').classList.remove('hidden');
        }

        function hideEditMaterialModal() {
            document.getElementById('editMaterialModal').classList.add('hidden');
            document.getElementById('editMaterialForm').reset();
            document.getElementById('edit_material_file_input').classList.add('hidden');
            document.getElementById('edit_material_content_input').classList.add('hidden');
            document.getElementById('edit_current_file').textContent = '';
        }

        function toggleEditMaterialInputs() {
            const materialType = document.getElementById('edit_material_type_hidden').value;
            const fileInput = document.getElementById('edit_material_file_input');
            const contentInput = document.getElementById('edit_material_content_input');
            const fileHelp = document.getElementById('edit_file_help');
            const fileInputField = document.getElementById('edit_material_file');
            const contentField = document.getElementById('edit_material_content');

            if (materialType === 'text') {
                fileInput.classList.add('hidden');
                contentInput.classList.remove('hidden');
                fileInputField.removeAttribute('required');
                contentField.setAttribute('required', 'required');
                fileInputField.removeAttribute('accept');
            } else if (['pdf', 'image', 'video', 'audio'].includes(materialType)) {
                fileInput.classList.remove('hidden');
                contentInput.classList.add('hidden');
                fileInputField.removeAttribute('required'); // Not required for edit
                contentField.removeAttribute('required');
                
                const acceptTypes = {
                    'pdf': '.pdf,application/pdf',
                    'image': '.jpg,.jpeg,.png,image/jpeg,image/png',
                    'video': '.mp4,video/mp4',
                    'audio': '.mp3,.wav,audio/mpeg,audio/wav,audio/mp3'
                };
                fileInputField.setAttribute('accept', acceptTypes[materialType] || '');
                
                const helpTexts = {
                    'pdf': 'Format: PDF. Maksimal 10MB. Kosongkan jika tidak ingin mengubah file.',
                    'image': 'Format: JPG, PNG. Maksimal 5MB. Kosongkan jika tidak ingin mengubah file.',
                    'video': 'Format: MP4. Maksimal 100MB. Kosongkan jika tidak ingin mengubah file.',
                    'audio': 'Format: MP3, WAV. Maksimal 50MB. Kosongkan jika tidak ingin mengubah file.'
                };
                fileHelp.textContent = helpTexts[materialType] || '';
            } else {
                fileInput.classList.add('hidden');
                contentInput.classList.add('hidden');
                fileInputField.removeAttribute('required');
                contentField.removeAttribute('required');
                fileInputField.value = '';
                contentField.value = '';
                fileInputField.removeAttribute('accept');
            }
        }

    </script>
</x-app-layout>

