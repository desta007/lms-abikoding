<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['user.profile', 'attendees'])
            ->public()
            ->orderBy('start_date', 'asc');

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        // Filter by date range
        if ($request->has('month')) {
            $query->whereMonth('start_date', $request->month);
        }
        if ($request->has('year')) {
            $query->whereYear('start_date', $request->year);
        }

        // Filter upcoming/past
        if ($request->has('filter')) {
            if ($request->filter === 'upcoming') {
                $query->upcoming();
            } elseif ($request->filter === 'past') {
                $query->past();
            }
        } else {
            $query->upcoming();
        }

        $events = $query->paginate(12);

        return view('community.events.index', compact('events'));
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $events = Event::public()
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->get();

        return view('community.events.calendar', compact('events', 'month', 'year'));
    }

    public function create()
    {
        return view('community.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'online_link' => 'nullable|url',
            'event_type' => 'required|in:online,offline,hybrid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'timezone' => 'nullable|string',
            'max_attendees' => 'nullable|integer|min:1',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_end_date' => 'nullable|date|after:start_date',
            'is_public' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['timezone'] = $validated['timezone'] ?? 'Asia/Jakarta';

        $event = Event::create($validated);

        return redirect()->route('community.events.show', $event->id)
            ->with('success', 'Event berhasil dibuat!');
    }

    public function show($id)
    {
        $event = Event::with(['user.profile', 'attendees.user'])
            ->findOrFail($id);

        $isRegistered = false;
        $attendee = null;
        if (Auth::check()) {
            $attendee = EventAttendee::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->first();
            $isRegistered = $attendee !== null && $attendee->status === 'registered';
        }

        return view('community.events.show', compact('event', 'isRegistered', 'attendee'));
    }

    public function register($id)
    {
        $event = Event::public()->findOrFail($id);

        if (!$event->canRegister()) {
            return redirect()->back()->with('error', 'Event sudah penuh atau sudah dimulai');
        }

        EventAttendee::updateOrCreate([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
        ], [
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Berhasil mendaftar ke event!');
    }

    public function cancel($id)
    {
        $attendee = EventAttendee::where('event_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $attendee->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Pendaftaran dibatalkan');
    }

    public function myEvents()
    {
        $myEvents = Event::where('user_id', Auth::id())->get();
        $registeredEvents = Event::whereHas('attendees', function($q) {
            $q->where('user_id', Auth::id())
              ->where('status', 'registered');
        })->get();

        return view('community.events.my-events', compact('myEvents', 'registeredEvents'));
    }
}
