<?php

namespace App\Http\Controllers;

use App\Events\NewReaction;
use App\Models\Reaction;
use App\Services\FeedService;
use App\Services\GamificationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function __construct(
        private FeedService          $feedService,
        private GamificationService  $gamification,
        private NotificationService  $notifications,
    ) {}

    public function toggle(Request $request)
    {
        $request->validate([
            'reactable_type' => 'required|in:post,confession,comment',
            'reactable_id'   => 'required|integer',
            'type'           => 'required|in:fire,heart,skull,laugh,shock,vibe',
        ]);

        $user          = Auth::user();
        $modelClass    = $this->resolveModel($request->reactable_type);
        $reactable     = $modelClass::findOrFail($request->reactable_id);
        $isAnon        = $request->boolean('is_anonymous', false);

        $existing = Reaction::where([
            'user_id'        => $user->id,
            'reactable_type' => $modelClass,
            'reactable_id'   => $reactable->id,
            'type'           => $request->type,
        ])->first();

        if ($existing) {
            $existing->delete();
            $reactable->decrement('reaction_count');
            $action = 'removed';
        } else {
            Reaction::create([
                'user_id'        => $user->id,
                'reactable_type' => $modelClass,
                'reactable_id'   => $reactable->id,
                'type'           => $request->type,
                'is_anonymous'   => $isAnon,
            ]);
            $reactable->increment('reaction_count');
            $action = 'added';

            // Award points and notify
            $this->gamification->awardPoints($user, 'reaction');

            // Notify content owner (if not self)
            $ownerId = $reactable->user_id ?? null;
            if ($ownerId && $ownerId !== $user->id) {
                $owner = \App\Models\User::find($ownerId);
                if ($owner) {
                    $this->notifications->send($owner, 'new_reaction', [
                        'actor' => $isAnon ? 'Someone' : $user->display_name,
                        'emoji' => Reaction::emojis()[$request->type],
                    ]);
                }
            }

            // Recalculate feed score if it's a post
            if ($request->reactable_type === 'post') {
                $this->feedService->recalculateBoostScore($reactable);
            }
        }

        // Get updated counts
        $counts = Reaction::where('reactable_type', $modelClass)
            ->where('reactable_id', $reactable->id)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        broadcast(new NewReaction($modelClass, $reactable->id, $request->type, $counts));

        return response()->json([
            'action' => $action,
            'counts' => $counts,
            'total'  => array_sum($counts),
        ]);
    }

    private function resolveModel(string $type): string
    {
        return match ($type) {
            'post'       => \App\Models\Post::class,
            'confession' => \App\Models\Confession::class,
            'comment'    => \App\Models\Comment::class,
            default      => abort(422),
        };
    }
}
