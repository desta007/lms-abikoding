<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:post_comments,id',
        ]);

        $comment = PostComment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan',
                'comment_id' => $comment->id,
            ]);
        }

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan');
    }

    public function destroy(Request $request, $id)
    {
        $comment = PostComment::where('user_id', Auth::id())->findOrFail($id);
        $comment->delete();

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil dihapus',
            ]);
        }

        return redirect()->back()->with('success', 'Komentar berhasil dihapus');
    }
}
