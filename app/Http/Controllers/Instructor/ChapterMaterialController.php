<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ChapterMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChapterMaterialController extends Controller
{
    public function create($chapterId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Chapter::with('course')
            : Chapter::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            })->with('course');
        
        $chapter = $query->findOrFail($chapterId);

        return view('instructor.materials.create', compact('chapter'));
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? ChapterMaterial::with('chapter.course')
            : ChapterMaterial::whereHas('chapter.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            })->with('chapter.course');
        
        $material = $query->findOrFail($id);

        return view('instructor.materials.edit', compact('material'));
    }

    public function store(Request $request, $chapterId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Chapter::query()
            : Chapter::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        
        $chapter = $query->findOrFail($chapterId);

        // Validate all optional inputs
        $rules = [
            'video_url' => 'nullable|url|max:500',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm|max:51200', // 50MB
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB
            'text_content' => 'nullable|string',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120', // 5MB
            'title' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);

        // Check if at least one material type is provided
        $hasVideoUrl = !empty($validated['video_url']);
        $hasVideoFile = $request->hasFile('video_file');
        $hasPdf = $request->hasFile('pdf_file');
        $hasText = !empty($validated['text_content']);
        $hasImage = $request->hasFile('image_file');

        if (!$hasVideoUrl && !$hasVideoFile && !$hasPdf && !$hasText && !$hasImage) {
            return redirect()->back()
                ->withInput($request->merge(['chapter_id' => $chapterId])->all())
                ->withErrors(['general' => 'Minimal satu jenis materi harus diisi (Video URL, Video File, PDF, Teks, atau Gambar).']);
        }

        // Get next order number
        $maxOrder = ChapterMaterial::where('chapter_id', $chapterId)->max('order') ?? 0;
        
        // Determine material type based on what's provided
        $materialTypes = [];
        if ($hasVideoUrl || $hasVideoFile) $materialTypes[] = 'video';
        if ($hasPdf) $materialTypes[] = 'pdf';
        if ($hasText) $materialTypes[] = 'text';
        if ($hasImage) $materialTypes[] = 'image';
        
        $materialType = count($materialTypes) > 1 ? 'mixed' : ($materialTypes[0] ?? 'mixed');
        
        // Prepare data for single material record
        $materialData = [
            'chapter_id' => $chapterId,
            'material_type' => $materialType,
            'title' => $validated['title'] ?? 'Materi',
            'order' => $maxOrder + 1,
        ];
        
        // Handle video URL
        if ($hasVideoUrl) {
            $materialData['video_url'] = $validated['video_url'];
        }
        
        // Handle video file upload
        if ($hasVideoFile) {
            $file = $request->file('video_file');
            $materialData['video_file_path'] = $file->store('courses/materials/videos', 'public');
            $materialData['video_file_size'] = $file->getSize();
            $materialData['video_file_mime_type'] = $file->getMimeType();
        }
        
        // Handle PDF file
        if ($hasPdf) {
            $file = $request->file('pdf_file');
            $materialData['pdf_file_path'] = $file->store('courses/materials', 'public');
            $materialData['pdf_file_size'] = $file->getSize();
            $materialData['pdf_file_mime_type'] = $file->getMimeType();
        }
        
        // Handle text content
        if ($hasText) {
            $materialData['text_content'] = $validated['text_content'];
        }
        
        // Handle image file
        if ($hasImage) {
            $file = $request->file('image_file');
            $materialData['image_file_path'] = $file->store('courses/materials', 'public');
            $materialData['image_file_size'] = $file->getSize();
            $materialData['image_file_mime_type'] = $file->getMimeType();
        }
        
        // Create single material record with all content
        ChapterMaterial::create($materialData);
        
        $message = 'Materi berhasil ditambahkan!';

        return redirect()->route('instructor.courses.show', $chapter->course_id)
            ->with('success', $message);
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? ChapterMaterial::query()
            : ChapterMaterial::whereHas('chapter.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        
        $material = $query->findOrFail($id);

        // Validate all optional inputs (same as store method)
        $rules = [
            'video_url' => 'nullable|url|max:500',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm|max:51200', // 50MB
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB
            'text_content' => 'nullable|string',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120', // 5MB
            'title' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);

        // Check if at least one material type is provided
        $hasVideoUrl = !empty($validated['video_url']);
        $hasVideoFile = $request->hasFile('video_file');
        $hasPdf = $request->hasFile('pdf_file');
        $hasText = !empty($validated['text_content']);
        $hasImage = $request->hasFile('image_file');

        // If no new content is provided, at least keep existing data
        if (!$hasVideoUrl && !$hasVideoFile && !$hasPdf && !$hasText && !$hasImage) {
            // Only update title if provided
            if (!empty($validated['title'])) {
                $material->update(['title' => $validated['title']]);
            }
            
            return redirect()->route('instructor.courses.show', $material->chapter->course_id)
                ->with('success', 'Materi berhasil diperbarui!');
        }

        // Handle video URL
        if ($hasVideoUrl) {
            $material->video_url = $validated['video_url'];
        }
        
        // Handle video file upload
        if ($hasVideoFile) {
            // Delete old video file if exists
            if ($material->video_file_path) {
                Storage::disk('public')->delete($material->video_file_path);
            }
            
            $file = $request->file('video_file');
            $material->video_file_path = $file->store('courses/materials/videos', 'public');
            $material->video_file_size = $file->getSize();
            $material->video_file_mime_type = $file->getMimeType();
        }
        
        // Handle PDF file
        if ($hasPdf) {
            // Delete old PDF file if exists
            if ($material->pdf_file_path) {
                Storage::disk('public')->delete($material->pdf_file_path);
            }
            
            $file = $request->file('pdf_file');
            $material->pdf_file_path = $file->store('courses/materials', 'public');
            $material->pdf_file_size = $file->getSize();
            $material->pdf_file_mime_type = $file->getMimeType();
        }
        
        // Handle text content
        if ($hasText) {
            $material->text_content = $validated['text_content'];
        }
        
        // Handle image file
        if ($hasImage) {
            // Delete old image file if exists
            if ($material->image_file_path) {
                Storage::disk('public')->delete($material->image_file_path);
            }
            
            $file = $request->file('image_file');
            $material->image_file_path = $file->store('courses/materials', 'public');
            $material->image_file_size = $file->getSize();
            $material->image_file_mime_type = $file->getMimeType();
        }

        // Update title if provided
        if (!empty($validated['title'])) {
            $material->title = $validated['title'];
        }

        $material->save();

        return redirect()->route('instructor.courses.show', $material->chapter->course_id)
            ->with('success', 'Materi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? ChapterMaterial::query()
            : ChapterMaterial::whereHas('chapter.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        
        $material = $query->findOrFail($id);

        // Delete file if exists
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $courseId = $material->chapter->course_id;
        $material->delete();

        return redirect()->route('instructor.courses.show', $courseId)
            ->with('success', 'Materi berhasil dihapus!');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'materials' => 'required|array',
            'materials.*.id' => 'required|exists:chapter_materials,id',
            'materials.*.order' => 'required|integer',
        ]);

        $userId = Auth::id();
        $user = Auth::user();
        
        foreach ($request->materials as $materialData) {
            $query = $user->isAdmin()
                ? ChapterMaterial::query()
                : ChapterMaterial::whereHas('chapter.course', function($q) use ($userId) {
                    $q->where('instructor_id', $userId);
                });
            
            $material = $query->findOrFail($materialData['id']);

            $material->update(['order' => $materialData['order']]);
        }

        return response()->json(['success' => true]);
    }
}
