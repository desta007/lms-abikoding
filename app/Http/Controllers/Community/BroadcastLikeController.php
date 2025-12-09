<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\BroadcastLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastLikeController extends Controller
{
    public function toggle(Request $request, $broadcastId)
    {
        $like = BroadcastLike::where('broadcast_id', $broadcastId)
            ->where('user_id', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            BroadcastLike::create([
                'broadcast_id' => $broadcastId,
                'user_id' => Auth::id(),
            ]);
            $liked = true;
        }

        $likesCount = BroadcastLike::where('broadcast_id', $broadcastId)->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }
}
