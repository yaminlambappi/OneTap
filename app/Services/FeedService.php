<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Confession;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FeedService
{
    /**
     * Nearby feed — posts within radius, ranked by proximity + velocity.
     */
    public function nearbyFeed(User $user, float $lat, float $lng, int $radiusKm = 5, int $limit = 30): Collection
    {
        $cacheKey = "feed:nearby:{$user->id}:" . round($lat, 2) . ':' . round($lng, 2);

        return Cache::remember($cacheKey, 60, function () use ($lat, $lng, $radiusKm, $limit) {
            return Post::query()
                ->select('posts.*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(last_lat)) * cos(radians(last_lng) - radians(?)) + sin(radians(?)) * sin(radians(last_lat)))) AS distance_km',
                    [$lat, $lng, $lat]
                )
                ->join('users', 'users.id', '=', 'posts.user_id')
                ->where('posts.is_removed', false)
                ->whereNull('posts.expires_at')
                ->orWhere('posts.expires_at', '>', now())
                ->having('distance_km', '<=', $radiusKm)
                ->orderByRaw('(posts.velocity_score * 0.5 + (1 / (distance_km + 0.1)) * 0.5) DESC')
                ->limit($limit)
                ->with(['user', 'poll.options', 'reactions'])
                ->get();
        });
    }

    /**
     * Campus feed — posts from the same campus, ranked by boost score.
     */
    public function campusFeed(int $campusId, int $limit = 40): Collection
    {
        $cacheKey = "feed:campus:{$campusId}";

        return Cache::remember($cacheKey, 90, function () use ($campusId, $limit) {
            return Post::where('campus_id', $campusId)
                ->where('is_removed', false)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->orderByDesc('boost_score')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->with(['user', 'poll.options', 'reactions'])
                ->get();
        });
    }

    /**
     * Trending feed — highest velocity content across all campuses.
     */
    public function trendingFeed(?int $campusId = null, int $limit = 30): Collection
    {
        $cacheKey = "feed:trending:" . ($campusId ?? 'global');

        return Cache::remember($cacheKey, 120, function () use ($campusId, $limit) {
            $query = Post::where('is_removed', false)
                ->where('is_trending', true)
                ->where('created_at', '>=', now()->subHours(24));

            if ($campusId) {
                $query->where('campus_id', $campusId);
            }

            return $query->orderByDesc('velocity_score')
                ->limit($limit)
                ->with(['user', 'poll.options', 'reactions'])
                ->get();
        });
    }

    /**
     * Anonymous / confession feed.
     */
    public function anonymousFeed(?int $campusId = null, int $limit = 30): Collection
    {
        $cacheKey = "feed:anon:" . ($campusId ?? 'global');

        return Cache::remember($cacheKey, 60, function () use ($campusId, $limit) {
            $query = Confession::where('is_removed', false);

            if ($campusId) {
                $query->where('campus_id', $campusId);
            }

            return $query->orderByDesc('velocity_score')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->with(['reactions', 'comments'])
                ->get();
        });
    }

    /**
     * Friends feed — posts from connected users.
     */
    public function friendsFeed(User $user, int $limit = 30): Collection
    {
        $friendIds = DB::table('friendships')
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
            })
            ->where('status', 'accepted')
            ->get()
            ->map(fn($f) => $f->sender_id === $user->id ? $f->receiver_id : $f->sender_id)
            ->toArray();

        if (empty($friendIds)) {
            return collect();
        }

        return Post::whereIn('user_id', $friendIds)
            ->where('is_removed', false)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->with(['user', 'poll.options', 'reactions'])
            ->get();
    }

    /**
     * Chaos feed — highest engagement velocity in last 2 hours.
     */
    public function chaosFeed(?int $campusId = null, int $limit = 20): Collection
    {
        $cacheKey = "feed:chaos:" . ($campusId ?? 'global');

        return Cache::remember($cacheKey, 30, function () use ($campusId, $limit) {
            $query = Post::where('is_removed', false)
                ->where('created_at', '>=', now()->subHours(2))
                ->orderByDesc('velocity_score');

            if ($campusId) {
                $query->where('campus_id', $campusId);
            }

            return $query->limit($limit)
                ->with(['user', 'poll.options', 'reactions'])
                ->get();
        });
    }

    /**
     * Recalculate boost score for a post.
     * Score = reactions(w:0.4) + comments(w:0.3) + recency(w:0.2) + proximity_bonus(w:0.1)
     */
    public function recalculateBoostScore(Post $post): void
    {
        $ageHours = max(1, now()->diffInHours($post->created_at));
        $reactionScore = $post->reaction_count * 0.4;
        $commentScore  = $post->comment_count * 0.3;
        $recencyScore  = (1 / $ageHours) * 100 * 0.2;
        $velocity      = ($post->reaction_count + $post->comment_count) / $ageHours;

        $post->update([
            'boost_score'    => (int) ($reactionScore + $commentScore + $recencyScore),
            'velocity_score' => round($velocity, 4),
            'is_trending'    => $velocity >= 5,
        ]);

        // Bust cache
        Cache::forget("feed:trending:" . ($post->campus_id ?? 'global'));
        Cache::forget("feed:campus:{$post->campus_id}");
        Cache::forget("feed:chaos:" . ($post->campus_id ?? 'global'));
    }
}
