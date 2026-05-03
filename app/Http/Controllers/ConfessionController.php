<?php

namespace App\Http\Controllers;

use App\Models\Confession;
use App\Services\AnonymousIdentityService;
use App\Services\GamificationService;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfessionController extends Controller
{
    public function __construct(
        private AnonymousIdentityService $identity,
        private GamificationService      $gamification,
        private ModerationService        $moderation,
    ) {}

    public function index(Request $request)
    {
        $user     = Auth::user();
        $campusId = $user?->campus_id;
        $category = $request->get('category');

        $confessions = Confession::where('is_removed', false)
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->when($category, fn($q) => $q->where('category', $category))
            ->orderByDesc('velocity_score')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('confessions.index', compact('confessions', 'category'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($this->moderation->isBanned($user)) {
            return back()->withErrors(['error' => 'Your account is currently restricted.']);
        }

        if (!$this->moderation->canConfess($user)) {
            return back()->withErrors(['error' => 'Too many confessions. Wait a bit.']);
        }

        $request->validate([
            'body'     => 'required|string|min:10|max:500',
            'category' => 'in:general,crush,academic,social,rant,secret',
            'mood'     => 'nullable|string|max:10',
        ]);

        if (!$this->moderation->passesContentFilter($request->body)) {
            return back()->withErrors(['error' => 'Content violates community guidelines.']);
        }

        $confession = Confession::create([
            'user_id'               => $user->id,
            'campus_id'             => $user->campus_id,
            'body'                  => $request->body,
            'category'              => $request->category ?? 'general',
            'mood'                  => $request->mood,
            'anonymous_alias'       => $this->identity->generateConfessionAlias(),
            'anonymous_avatar_seed' => $this->identity->generateConfessionAvatarSeed(),
        ]);

        $this->moderation->recordConfession($user);
        $this->gamification->awardPoints($user, 'confession');
        $this->gamification->updateStreak($user);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'id' => $confession->id]);
        }

        return back()->with('success', 'Confession dropped anonymously.');
    }
}
