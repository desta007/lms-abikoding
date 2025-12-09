<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function store(Request $request, $courseId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Ensure course belongs to instructor
        $course = Course::where('instructor_id', Auth::id())
            ->findOrFail($courseId);

        // Get next order number
        $maxOrder = Chapter::where('course_id', $course->id)->max('order') ?? 0;

        $chapter = Chapter::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $maxOrder + 1,
            'is_published' => false,
        ]);

        return redirect()->route('instructor.courses.show', $course->id)
            ->with('success', 'Bab berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $chapter = Chapter::whereHas('course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ]);

        $chapter->update($validated);

        return redirect()->back()
            ->with('success', 'Bab berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $chapter = Chapter::whereHas('course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($id);

        $courseId = $chapter->course_id;
        $chapter->delete();

        return redirect()->route('instructor.courses.show', $courseId)
            ->with('success', 'Bab berhasil dihapus!');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'chapters' => 'required|array',
            'chapters.*.id' => 'required|exists:chapters,id',
            'chapters.*.order' => 'required|integer',
        ]);

        foreach ($request->chapters as $chapterData) {
            $chapter = Chapter::whereHas('course', function($q) {
                $q->where('instructor_id', Auth::id());
            })->findOrFail($chapterData['id']);

            $chapter->update(['order' => $chapterData['order']]);
        }

        return response()->json(['success' => true]);
    }
}
