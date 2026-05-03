<?php

namespace App\Http\Controllers;

use App\Events\PollVoteUpdated;
use App\Models\Poll;
use App\Models\PollVote;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    public function __construct(private GamificationService $gamification) {}

    public function vote(Request $request, Poll $poll)
    {
        $request->validate([
            'option_id' => 'required|exists:poll_options,id',
        ]);

        $user = Auth::user();

        if ($poll->isExpired()) {
            return response()->json(['error' => 'This poll has ended.'], 422);
        }

        // Check if already voted
        $alreadyVoted = PollVote::where('poll_id', $poll->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyVoted && !$poll->allow_multiple) {
            return response()->json(['error' => 'Already voted.'], 422);
        }

        PollVote::create([
            'poll_id'        => $poll->id,
            'poll_option_id' => $request->option_id,
            'user_id'        => $user->id,
        ]);

        $poll->increment('total_votes');
        $poll->options()->where('id', $request->option_id)->increment('vote_count');

        $this->gamification->awardPoints($user, 'poll_vote');

        $poll->load('options');
        broadcast(new PollVoteUpdated($poll));

        return response()->json([
            'total_votes' => $poll->total_votes,
            'options'     => $poll->options->map(fn($o) => [
                'id'         => $o->id,
                'vote_count' => $o->vote_count,
                'percentage' => $poll->total_votes > 0
                    ? round(($o->vote_count / $poll->total_votes) * 100)
                    : 0,
            ]),
        ]);
    }
}
