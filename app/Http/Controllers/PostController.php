<?php

namespace App\Http\Controllers;

use App\Events\NewPost;
use App\Models\Post;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Hashtag;
use App\Services\AnonymousIdentityService;
use App\Services\FeedService;
use App\Services\GamificationService;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct(
        private AnonymousIdentityService $identity,
        private FeedService              $feedService,
        private GamificationService      $gamification,
        private ModerationService        $moderation,
    ) {}

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($this->moderation->isBanned($user)) {
            return back()->withErrors(['error' => 'Your account is currently restricted.']);
        }

        if (!$this->moderation->canPost($user)) {
            return back()->withErrors(['error' => 'You\'re posting too fast. Slow down.']);
        }

        $request->validate([
            'type'         => 'required|in:text,image,meme,poll,spotted,question,hot_take,challenge,event_shout',
            'body'         => 'nullable|string|max:1000',
            'media.*'      => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'visibility'   => 'in:public,campus,community,friends',
            'is_anonymous' => 'boolean',
            'lat'          => 'nullable|numeric',
            'lng'          => 'nullable|numeric',
            'campus_id'    => 'nullable|exists:campuses,id',
            'community_id' => 'nullable|exists:communities,id',
            // Poll fields
            'poll_question'    => 'required_if:type,poll|string|max:255',
            'poll_options'     => 'required_if:type,poll|array|min:2|max:6',
            'poll_options.*'   => 'string|max:100',
            'poll_ends_hours'  => 'nullable|integer|min:1|max:72',
        ]);

        if ($request->body && $this->moderation->isSpam($request->body, $user)) {
            return back()->withErrors(['error' => 'Duplicate content detected.']);
        }

        if ($request->body && !$this->moderation->passesContentFilter($request->body)) {
            return back()->withErrors(['error' => 'Content violates community guidelines.']);
        }

        DB::beginTransaction();
        try {
            $mediaData = [];
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('posts/' . date('Y/m'), 'public');
                    $mediaData[] = [
                        'url'  => Storage::url($path),
                        'type' => $file->getMimeType(),
                    ];
                }
            }

            $isAnon  = $request->boolean('is_anonymous');
            $context = 'campus:' . ($request->campus_id ?? $user->campus_id ?? 'global');

            $post = Post::create([
                'user_id'               => $user->id,
                'campus_id'             => $request->campus_id ?? $user->campus_id,
                'community_id'          => $request->community_id,
                'type'                  => $request->type,
                'body'                  => $request->body,
                'media'                 => $mediaData ?: null,
                'visibility'            => $request->visibility ?? 'public',
                'is_anonymous'          => $isAnon,
                'anonymous_alias'       => $isAnon ? $this->identity->generateAlias($user->id, $context) : null,
                'anonymous_avatar_seed' => $isAnon ? $this->identity->generateAvatarSeed($user->id, $context) : null,
                'lat'                   => $request->lat,
                'lng'                   => $request->lng,
            ]);

            // Create poll if needed
            if ($request->type === 'poll') {
                $poll = Poll::create([
                    'post_id'    => $post->id,
                    'user_id'    => $user->id,
                    'campus_id'  => $post->campus_id,
                    'question'   => $request->poll_question,
                    'is_anonymous' => $isAnon,
                    'ends_at'    => $request->poll_ends_hours
                        ? now()->addHours($request->poll_ends_hours)
                        : now()->addHours(24),
                ]);

                foreach ($request->poll_options as $label) {
                    PollOption::create(['poll_id' => $poll->id, 'label' => $label]);
                }
            }

            // Extract and attach hashtags
            if ($request->body) {
                $this->attachHashtags($post, $request->body);
            }

            DB::commit();

            // Update user location
            if ($request->lat && $request->lng) {
                $user->update([
                    'last_lat'             => $request->lat,
                    'last_lng'             => $request->lng,
                    'location_updated_at'  => now(),
                ]);
            }

            $this->moderation->recordPost($user);
            $this->gamification->awardPoints($user, 'post');
            $this->gamification->updateStreak($user);
            $this->feedService->recalculateBoostScore($post);

            broadcast(new NewPost($post->load('user')))->toOthers();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'post_id' => $post->id]);
            }

            return back()->with('success', 'Posted!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong. Try again.']);
        }
    }

    public function destroy(Post $post)
    {
        $user = Auth::user();

        if ($post->user_id !== $user->id) {
            abort(403);
        }

        $post->update(['is_removed' => true, 'removed_reason' => 'user_deleted']);

        return back()->with('success', 'Post removed.');
    }

    private function attachHashtags(Post $post, string $body): void
    {
        preg_match_all('/#([a-zA-Z0-9_]+)/', $body, $matches);

        foreach (array_unique($matches[1]) as $tag) {
            $hashtag = Hashtag::firstOrCreate(
                ['name' => strtolower($tag)],
                ['campus_id' => $post->campus_id]
            );
            $hashtag->increment('usage_count');
            $hashtag->increment('today_count');
            $post->hashtags()->syncWithoutDetaching([$hashtag->id]);
        }
    }
}
