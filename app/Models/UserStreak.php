<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStreak extends Model
{
    protected $fillable = [
        'user_id', 'current_streak', 'longest_streak',
        'total_posts', 'total_reactions_given', 'total_reactions_received',
        'total_comments', 'last_active_date',
    ];

    protected function casts(): array
    {
        return ['last_active_date' => 'date'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
