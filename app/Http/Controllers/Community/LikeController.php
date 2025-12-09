<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Request $request, $postId)
    {
        $like = PostLike::where('post_id', $postId)
            ->where('user_id', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            PostLike::create([
                'post_id' => $postId,
                'user_id' => Auth::id(),
            ]);
            $liked = true;
        }

        $likesCount = PostLike::where('post_id', $postId)->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }
}
