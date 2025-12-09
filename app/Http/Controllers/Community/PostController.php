<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user.profile', 'likes', 'comments.user'])
            ->public()
            ->recent()
            ->paginate(10);

        return view('community.index', compact('posts'));
    }

    public function store(Request $request)
    {
        // Content is required only if no media file is uploaded
        $rules = [
            'post_type' => 'required|in:text,image,video,link',
            'media' => 'nullable|file|max:102400', // Increased to 100MB for videos
            'link_url' => 'nullable|url',
        ];

        // If media is uploaded, content is optional
        if ($request->hasFile('media')) {
            $rules['content'] = 'nullable|string';
        } else {
            $rules['content'] = 'required|string';
        }

        $validated = $request->validate($rules);

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            
            // Validate file type
            $mimeType = $file->getMimeType();
            $allowedMimes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo',
                'video/x-ms-wmv', 'video/x-flv', 'video/webm', 'video/x-matroska'
            ];
            
            if (!in_array($mimeType, $allowedMimes)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['media' => 'Format file tidak didukung. Gunakan gambar (JPG, PNG, GIF) atau video (MP4, MOV, AVI).']);
            }
            
            $mediaPath = $file->store('community/posts', 'public');
            
            // Auto-detect post type if not set
            if (str_starts_with($mimeType, 'image/')) {
                $validated['post_type'] = 'image';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $validated['post_type'] = 'video';
            }
        }

        // Extract YouTube/Vimeo URL from content if present
        $linkUrl = $validated['link_url'] ?? null;
        if (!$linkUrl && !empty($validated['content'])) {
            // Check for YouTube URL
            if (preg_match('/(https?:\/\/(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11}))/', $validated['content'], $matches)) {
                $linkUrl = $matches[1]; // Full URL
                $validated['post_type'] = 'link';
            }
            // Check for Vimeo URL
            elseif (preg_match('/(https?:\/\/(?:www\.)?vimeo\.com\/(?:.*\/)?(\d+))/', $validated['content'], $vimeoMatches)) {
                $linkUrl = $vimeoMatches[1]; // Full URL
                $validated['post_type'] = 'link';
            }
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'] ?? '',
            'post_type' => $validated['post_type'],
            'media_path' => $mediaPath,
            'link_url' => $linkUrl,
        ]);

        // Award points
        PointsService::awardPoints(Auth::user(), 'create_post', 10);

        return redirect()->back()->with('success', 'Post berhasil dibuat!');
    }

    public function show($id)
    {
        $post = Post::with(['user.profile', 'likes.user', 'comments.user.profile'])
            ->findOrFail($id);

        return view('community.posts.show', compact('post'));
    }

    public function destroy($id)
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($id);
        
        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }
        
        $post->delete();

        return redirect()->route('community.index')->with('success', 'Post berhasil dihapus!');
    }
}
