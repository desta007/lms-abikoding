<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\BroadcastComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastCommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'broadcast_id' => 'required|exists:broadcasts,id',
            'content' => 'required|string',
        ]);

        BroadcastComment::create([
            'broadcast_id' => $request->broadcast_id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $comment = BroadcastComment::where('user_id', Auth::id())->findOrFail($id);
        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus');
    }
}
