<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Confession extends Model
{
    protected $fillable = [
        'user_id', 'campus_id', 'community_id', 'body',
        'category', 'mood', 'anonymous_alias', 'anonymous_avatar_seed',
        'reaction_count', 'comment_count', 'view_count',
        'velocity_score', 'is_trending', 'is_removed', 'removed_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_trending' => 'boolean',
            'is_removed'  => 'boolean',
        ];
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function getAvatarUrlAttribute(): string
    {
        $seed = $this->anonymous_avatar_seed ?? 'confession';
        return "https://api.dicebear.com/7.x/bottts/svg?seed={$seed}";
    }
}
