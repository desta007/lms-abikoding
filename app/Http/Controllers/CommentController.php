<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'chapter_material_id' => 'nullable|exists:chapter_materials,id',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            'chapter_id' => $request->chapter_id,
            'chapter_material_id' => $request->chapter_material_id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_approved' => true,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil diperbarui');
    }

    public function destroy($id)
    {
        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);
        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus');
    }
}
