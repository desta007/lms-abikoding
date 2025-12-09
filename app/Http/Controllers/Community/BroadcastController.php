<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Models\BroadcastView;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BroadcastController extends Controller
{
    public function index()
    {
        $liveBroadcasts = Broadcast::with(['user.profile', 'likes', 'comments'])
            ->live()
            ->recent()
            ->get();

        $upcomingBroadcasts = Broadcast::with(['user.profile'])
            ->upcoming()
            ->recent()
            ->get();

        $recentBroadcasts = Broadcast::with(['user.profile', 'likes', 'comments'])
            ->where('is_live', false)
            ->whereNotNull('ended_at')
            ->recent()
            ->paginate(12);

        return view('community.broadcasts.index', compact('liveBroadcasts', 'upcomingBroadcasts', 'recentBroadcasts'));
    }

    public function create()
    {
        return view('community.broadcasts.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_url' => 'nullable|url',
                'thumbnail' => 'nullable|image|max:2048',
                'scheduled_at' => 'nullable|date',
                'is_zoom_meeting' => 'nullable',
            ]);

            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('broadcasts/thumbnails', 'public');
            }

            // Handle checkbox - if not checked, it won't be sent in request
            $isZoomMeeting = $request->has('is_zoom_meeting') && $request->input('is_zoom_meeting') == '1';

            $broadcastData = [
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'video_url' => $validated['video_url'] ?? null,
                'thumbnail' => $thumbnailPath,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'is_zoom_meeting' => $isZoomMeeting,
            ];

            // Create Zoom meeting if requested
            if ($isZoomMeeting) {
                try {
                    $zoomService = new ZoomService();
                    $meetingData = [
                        'topic' => $validated['title'],
                        'start_time' => $validated['scheduled_at'] ?? now()->addHour()->toIso8601String(),
                        'duration' => 60, // Default 60 minutes
                    ];
                    
                    $zoomMeeting = $zoomService->createMeeting($meetingData);
                    
                    $broadcastData['zoom_meeting_id'] = $zoomMeeting['id'] ?? null;
                    $broadcastData['zoom_meeting_password'] = $zoomMeeting['password'] ?? null;
                    $broadcastData['zoom_join_url'] = $zoomMeeting['join_url'] ?? null;
                    $broadcastData['zoom_start_url'] = $zoomMeeting['start_url'] ?? null;
                } catch (\Exception $e) {
                    \Log::error('Failed to create Zoom meeting: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal membuat Zoom meeting: ' . $e->getMessage());
                }
            }

            $broadcast = Broadcast::create($broadcastData);

            return redirect()->route('community.broadcasts.show', $broadcast->id)
                ->with('success', 'Broadcast berhasil dibuat!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to create broadcast: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat broadcast: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $broadcast = Broadcast::with(['user.profile', 'likes.user', 'comments.user.profile'])
            ->findOrFail($id);

        // Track view
        if (Auth::check()) {
            BroadcastView::create([
                'broadcast_id' => $broadcast->id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
            ]);
        }

        return view('community.broadcasts.show', compact('broadcast'));
    }

    public function start($id)
    {
        $broadcast = Broadcast::where('user_id', Auth::id())->findOrFail($id);
        
        $broadcast->update([
            'is_live' => true,
            'started_at' => now(),
        ]);

        // If Zoom meeting, ensure start URL is available
        if ($broadcast->is_zoom_meeting && !$broadcast->zoom_start_url) {
            try {
                $zoomService = new ZoomService();
                $meeting = $zoomService->getMeeting($broadcast->zoom_meeting_id);
                if ($meeting) {
                    $broadcast->update([
                        'zoom_start_url' => $meeting['start_url'] ?? null,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to get Zoom start URL: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Broadcast dimulai!');
    }

    public function createZoomMeeting($id)
    {
        $broadcast = Broadcast::where('user_id', Auth::id())->findOrFail($id);
        
        try {
            $zoomService = new ZoomService();
            $meetingData = [
                'topic' => $broadcast->title,
                'start_time' => $broadcast->scheduled_at ? $broadcast->scheduled_at->toIso8601String() : now()->addHour()->toIso8601String(),
                'duration' => 60,
            ];
            
            $zoomMeeting = $zoomService->createMeeting($meetingData);
            
            $broadcast->update([
                'is_zoom_meeting' => true,
                'zoom_meeting_id' => $zoomMeeting['id'],
                'zoom_meeting_password' => $zoomMeeting['password'] ?? null,
                'zoom_join_url' => $zoomMeeting['join_url'] ?? null,
                'zoom_start_url' => $zoomMeeting['start_url'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Zoom meeting berhasil dibuat!');
        } catch (\Exception $e) {
            \Log::error('Failed to create Zoom meeting: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat Zoom meeting: ' . $e->getMessage());
        }
    }

    public function end($id)
    {
        $broadcast = Broadcast::where('user_id', Auth::id())->findOrFail($id);
        
        $broadcast->update([
            'is_live' => false,
            'ended_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Broadcast diakhiri!');
    }
}
