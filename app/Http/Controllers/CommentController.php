<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Confession;
use App\Services\AnonymousIdentityService;
use App\Services\GamificationService;
use App\Services\ModerationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct(
        private AnonymousIdentityService $identity,
        private GamificationService      $gamification,
        private ModerationService        $moderation,
        private NotificationService      $notifications,
    ) {}

    public function store(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|in:post,confession',
            'commentable_id'   => 'required|integer',
            'body'             => 'required|string|min:1|max:500',
            'parent_id'        => 'nullable|exists:comments,id',
            'is_anonymous'     => 'boolean',
        ]);

        $user = Auth::user();

        if (!$this->moderation->passesContentFilter($request->body)) {
            return response()->json(['error' => 'Content violates guidelines.'], 422);
        }

        $modelClass = $request->commentable_type === 'post' ? Post::class : Confession::class;
        $commentable = $modelClass::findOrFail($request->commentable_id);

        $isAnon  = $request->boolean('is_anonymous');
        $context = 'comment:' . $request->commentable_type;

        $comment = Comment::create([
            'user_id'               => $user->id,
            'commentable_type'      => $modelClass,
            'commentable_id'        => $commentable->id,
            'parent_id'             => $request->parent_id,
            'body'                  => $request->body,
            'is_anonymous'          => $isAnon,
            'anonymous_alias'       => $isAnon ? $this->identity->generateAlias($user->id, $context) : null,
            'anonymous_avatar_seed' => $isAnon ? $this->identity->generateAvatarSeed($user->id, $context) : null,
        ]);

        $commentable->increment('comment_count');

        if ($request->parent_id) {
            Comment::where('id', $request->parent_id)->increment('reply_count');
        }

        $this->gamification->awardPoints($user, 'comment');

        // Notify content owner
        $ownerId = $commentable->user_id ?? null;
        if ($ownerId && $ownerId !== $user->id) {
            $owner = \App\Models\User::find($ownerId);
            if ($owner) {
                $this->notifications->send($owner, 'new_comment', [
                    'actor' => $isAnon ? 'Someone' : $user->display_name,
                ]);
            }
        }

        return response()->json([
            'id'             => $comment->id,
            'body'           => $comment->body,
            'author_name'    => $comment->author_name,
            'author_avatar'  => $isAnon
                ? $this->identity->getAvatarUrl($comment->anonymous_avatar_seed)
                : $user->avatar_url,
            'is_anonymous'   => $isAnon,
            'created_at'     => $comment->created_at->diffForHumans(),
        ]);
    }
}
