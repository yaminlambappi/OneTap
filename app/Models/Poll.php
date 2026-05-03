<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'post_id', 'user_id', 'campus_id', 'question',
        'is_anonymous', 'allow_multiple', 'total_votes', 'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous'   => 'boolean',
            'allow_multiple' => 'boolean',
            'ends_at'        => 'datetime',
        ];
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}
