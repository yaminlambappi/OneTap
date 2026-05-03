<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\LiveRoom;
use App\Models\LiveRoomParticipant;
use App\Models\Message;
use App\Services\AnonymousIdentityService;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveRoomController extends Controller
{
    public function __construct(
        private AnonymousIdentityService $identity,
        private ModerationService        $moderation,
    ) {}
    public function stats(Request $request)
    {
        $user = Auth::user();
        $campusId = $user?->campus_id;
        
        // Count recent presence records (last 5 mins) or rely on Reverb presence channels on frontend
        // We'll calculate Chaos score based on recent posts velocity
        $recentPostsCount = \App\Models\Post::where('created_at', '>=', now()->subMinutes(30))
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->count();
            
        // Calculate chaos percentage: max out at 100 posts per 30 min (just an arbitrary metric for realism)
        $chaosPct = min(100, (int) (($recentPostsCount / 100) * 100));
        
        // If it's too low, let's keep it minimally active for UI purposes, or just raw
        $chaosPct = max(5, $chaosPct); 
        
        $trending = \App\Models\TrendingTopic::when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->orderByDesc('score')
            ->limit(5)
            ->get(['topic', 'post_count']);
        
        return response()->json([
            'chaos_pct' => $chaosPct,
            'trending'  => $trending
        ]);
    }

    public function index()
    {
        $user  = Auth::user();
        $rooms = LiveRoom::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->when($user?->campus_id, fn($q) => $q->where(function ($q2) use ($user) {
                $q2->where('campus_id', $user->campus_id)->orWhere('type', 'open');
            }))
            ->orderByDesc('participant_count')
            ->limit(20)
            ->get();

        return view('live.index', compact('rooms'));
    }

    public function show(LiveRoom $room)
    {
        abort_if(!$room->is_active, 404);

        $user = Auth::user();
        $messages = $room->messages()
            ->with('user')
            ->orderBy('created_at')
            ->limit(50)
            ->get();

        // Join room
        LiveRoomParticipant::updateOrCreate(
            ['live_room_id' => $room->id, 'user_id' => $user->id],
            ['joined_at' => now(), 'left_at' => null]
        );
        $room->update([
            'participant_count' => LiveRoomParticipant::where('live_room_id', $room->id)
                ->whereNull('left_at')
                ->count(),
        ]);

        return view('live.show', compact('room', 'messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic'            => 'required|string|max:100',
            'type'             => 'in:open,campus,private',
            'is_anonymous'     => 'boolean',
            'max_participants' => 'nullable|integer|min:2|max:100',
        ]);

        $user = Auth::user();
        $room = LiveRoom::create([
            'created_by'       => $user->id,
            'campus_id'        => $user->campus_id,
            'topic'            => $request->topic,
            'type'             => $request->type ?? 'open',
            'is_anonymous'     => $request->boolean('is_anonymous'),
            'max_participants' => $request->max_participants ?? 50,
            'expires_at'       => now()->addHours(3),
        ]);

        return redirect()->route('live.show', $room);
    }

    public function sendMessage(Request $request, LiveRoom $room)
    {
        $request->validate(['body' => 'required|string|max:500']);
        $user = Auth::user();

        if (!$this->moderation->passesContentFilter($request->body)) {
            return response()->json(['error' => 'Message violates guidelines.'], 422);
        }

        $isAnon  = $request->boolean('is_anonymous', $room->is_anonymous);
        $context = "room:{$room->id}";

        $message = Message::create([
            'live_room_id'          => $room->id,
            'user_id'               => $user->id,
            'body'                  => $request->body,
            'type'                  => 'text',
            'is_anonymous'          => $isAnon,
            'anonymous_alias'       => $isAnon ? $this->identity->generateAlias($user->id, $context) : null,
            'anonymous_avatar_seed' => $isAnon ? $this->identity->generateAvatarSeed($user->id, $context) : null,
        ]);

        broadcast(new NewMessage($message->load('user')));

        return response()->json(['success' => true]);
    }
}
