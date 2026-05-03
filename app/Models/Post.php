<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'campus_id', 'community_id', 'location_id',
        'type', 'body', 'media', 'visibility',
        'is_anonymous', 'anonymous_alias', 'anonymous_avatar_seed',
        'lat', 'lng', 'view_count', 'reaction_count', 'comment_count',
        'share_count', 'boost_score', 'velocity_score',
        'is_trending', 'is_pinned', 'is_nsfw', 'is_removed',
        'removed_reason', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'media'        => 'array',
            'is_anonymous' => 'boolean',
            'is_trending'  => 'boolean',
            'is_pinned'    => 'boolean',
            'is_nsfw'      => 'boolean',
            'is_removed'   => 'boolean',
            'lat'          => 'float',
            'lng'          => 'float',
            'expires_at'   => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function poll()
    {
        return $this->hasOne(Poll::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function hashtags()
    {
        return $this->morphToMany(Hashtag::class, 'hashtaggable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return $this->anonymous_alias ?? 'Anonymous';
        }
        return $this->user?->display_name ?? 'Unknown';
    }

    public function getAuthorAvatarAttribute(): string
    {
        if ($this->is_anonymous) {
            $seed = $this->anonymous_avatar_seed ?? 'anon';
            return "https://api.dicebear.com/7.x/bottts/svg?seed={$seed}";
        }
        return $this->user?->avatar_url ?? '';
    }
}
