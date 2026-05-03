<?php

namespace App\Http\Controllers;

use App\Models\TrendingTopic;
use App\Models\Event;
use App\Models\UserPresence;
use App\Services\FeedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function __construct(private FeedService $feedService) {}

    public function index(Request $request)
    {
        $user      = Auth::user();
        $campusId  = $user?->campus_id;
        $tab       = $request->get('tab', 'nearby');

        $lat = $request->get('lat', $user?->last_lat ?? 23.8103);
        $lng = $request->get('lng', $user?->last_lng ?? 90.4125);

        $feeds = match ($tab) {
            'campus'    => $campusId ? $this->feedService->campusFeed($campusId) : collect(),
            'trending'  => $this->feedService->trendingFeed($campusId),
            'anonymous' => $this->feedService->anonymousFeed($campusId),
            'friends'   => $user ? $this->feedService->friendsFeed($user) : collect(),
            'chaos'     => $this->feedService->chaosFeed($campusId),
            default     => $this->feedService->nearbyFeed($user ?? new \App\Models\User(), $lat, $lng),
        };

        $trending = TrendingTopic::when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->orderByDesc('score')
            ->limit(8)
            ->get();

        $nearbyEvents = Event::where('is_cancelled', false)
            ->where('starts_at', '>=', now())
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        $onlineCount = UserPresence::where('is_online', true)
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->count();

        return view('feed.index', compact(
            'feeds', 'tab', 'trending', 'nearbyEvents', 'onlineCount', 'campusId'
        ));
    }

    /**
     * AJAX: load more posts for infinite scroll.
     */
    public function loadMore(Request $request)
    {
        $user     = Auth::user();
        $tab      = $request->get('tab', 'nearby');
        $page     = (int) $request->get('page', 1);
        $campusId = $user?->campus_id;

        $posts = match ($tab) {
            'campus'    => $campusId ? $this->feedService->campusFeed($campusId, 20) : collect(),
            'trending'  => $this->feedService->trendingFeed($campusId, 20),
            'anonymous' => $this->feedService->anonymousFeed($campusId, 20),
            'chaos'     => $this->feedService->chaosFeed($campusId, 20),
            default     => collect(),
        };

        return response()->json([
            'posts' => $posts->map(fn($p) => $this->formatPost($p)),
            'has_more' => $posts->count() === 20,
        ]);
    }

    private function formatPost($post): array
    {
        return [
            'id'            => $post->id,
            'type'          => $post->type,
            'body'          => $post->body,
            'author_name'   => $post->author_name,
            'author_avatar' => $post->author_avatar,
            'is_anonymous'  => $post->is_anonymous,
            'reaction_count'=> $post->reaction_count,
            'comment_count' => $post->comment_count,
            'is_trending'   => $post->is_trending,
            'created_at'    => $post->created_at->diffForHumans(),
        ];
    }
}
