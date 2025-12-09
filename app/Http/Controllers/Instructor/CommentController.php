<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $instructorId = Auth::id();
        
        $query = Comment::with(['user', 'chapter.course', 'chapterMaterial'])
            ->whereHas('chapter.course', function($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            });

        // Filter by course
        if ($request->has('course') && $request->course) {
            $query->whereHas('chapter', function($q) use ($request) {
                $q->where('course_id', $request->course);
            });
        }

        // Filter by chapter
        if ($request->has('chapter') && $request->chapter) {
            $query->where('chapter_id', $request->chapter);
        }

        // Filter by approval status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_approved', $request->status === 'approved');
        }

        $comments = $query->latest()->paginate(20);
        $courses = Course::where('instructor_id', $instructorId)->get();

        return view('instructor.comments.index', compact('comments', 'courses'));
    }

    public function show($id)
    {
        $comment = Comment::with(['user', 'chapter.course', 'chapterMaterial', 'replies.user'])
            ->whereHas('chapter.course', function($q) {
                $q->where('instructor_id', Auth::id());
            })
            ->findOrFail($id);

        return view('instructor.comments.show', compact('comment'));
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::whereHas('chapter.course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($id);

        $comment->update([
            'is_approved' => $request->has('is_approved') ? $request->is_approved : $comment->is_approved,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil diperbarui');
    }

    public function destroy($id)
    {
        $comment = Comment::whereHas('chapter.course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($id);

        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus');
    }

    public function reply(Request $request, $id)
    {
        $parentComment = Comment::whereHas('chapter.course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($id);

        $request->validate([
            'content' => 'required|string',
        ]);

        Comment::create([
            'chapter_id' => $parentComment->chapter_id,
            'chapter_material_id' => $parentComment->chapter_material_id,
            'user_id' => Auth::id(),
            'parent_id' => $parentComment->id,
            'content' => $request->content,
            'is_approved' => true,
        ]);

        return redirect()->back()->with('success', 'Balasan berhasil ditambahkan');
    }
}
