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
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = Comment::with(['user', 'chapter.course', 'chapterMaterial']);
        
        // Admin can see all comments, instructor only sees their own courses
        if (!$user->isAdmin()) {
            $query->whereHas('chapter.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }

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
        $courses = $user->isAdmin()
            ? Course::all()
            : Course::where('instructor_id', $userId)->get();

        return view('instructor.comments.index', compact('comments', 'courses'));
    }

    public function show($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = Comment::with(['user', 'chapter.course', 'chapterMaterial', 'replies.user']);
        
        if (!$user->isAdmin()) {
            $query->whereHas('chapter.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $comment = $query->findOrFail($id);

        return view('instructor.comments.show', compact('comment'));
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = Comment::query();
        
        if (!$user->isAdmin()) {
            $query->whereHas('chapter.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $comment = $query->findOrFail($id);

        $comment->update([
            'is_approved' => $request->has('is_approved') ? $request->is_approved : $comment->is_approved,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil diperbarui');
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = Comment::query();
        
        if (!$user->isAdmin()) {
            $query->whereHas('chapter.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $comment = $query->findOrFail($id);

        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus');
    }

    public function reply(Request $request, $id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = Comment::query();
        
        if (!$user->isAdmin()) {
            $query->whereHas('chapter.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $parentComment = $query->findOrFail($id);

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
