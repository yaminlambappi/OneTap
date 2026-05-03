<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id', 'commentable_type', 'commentable_id', 'parent_id',
        'body', 'is_anonymous', 'anonymous_alias', 'anonymous_avatar_seed',
        'reaction_count', 'reply_count', 'is_removed',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'is_removed'   => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return $this->anonymous_alias ?? 'Anonymous';
        }
        return $this->user?->display_name ?? 'Unknown';
    }
}
