<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAttendance;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function __construct(private GamificationService $gamification) {}

    public function index(Request $request)
    {
        $user     = Auth::user();
        $campusId = $user?->campus_id;

        $events = Event::where('is_cancelled', false)
            ->where('starts_at', '>=', now())
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->orderBy('starts_at')
            ->paginate(15);

        return view('events.index', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:100',
            'description'   => 'nullable|string|max:1000',
            'type'          => 'in:social,academic,party,meetup,spontaneous',
            'venue_name'    => 'nullable|string|max:100',
            'starts_at'     => 'required|date|after:now',
            'ends_at'       => 'nullable|date|after:starts_at',
            'max_attendees' => 'nullable|integer|min:2|max:500',
            'cover_image'   => 'nullable|file|mimes:jpg,jpeg,png,webp|max:8192',
            'lat'           => 'nullable|numeric',
            'lng'           => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $data = $request->except('cover_image');
        $data['user_id']   = $user->id;
        $data['campus_id'] = $user->campus_id;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = Storage::url(
                $request->file('cover_image')->store('events', 'public')
            );
        }

        $event = Event::create($data);

        // Auto-attend as creator
        EventAttendance::create([
            'event_id' => $event->id,
            'user_id'  => $user->id,
            'status'   => 'going',
        ]);
        $event->increment('attendee_count');

        $this->gamification->awardPoints($user, 'event_join');

        return redirect()->route('events.index')->with('success', 'Event created!');
    }

    public function attend(Request $request, Event $event)
    {
        $request->validate(['status' => 'required|in:going,maybe,not_going']);
        $user = Auth::user();

        $attendance = EventAttendance::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['status' => $request->status]
        );

        // Recalculate attendee count
        $event->update([
            'attendee_count' => EventAttendance::where('event_id', $event->id)
                ->where('status', 'going')
                ->count(),
        ]);

        if ($request->status === 'going') {
            $this->gamification->awardPoints($user, 'event_join');
        }

        return response()->json(['success' => true, 'attendee_count' => $event->attendee_count]);
    }
}
