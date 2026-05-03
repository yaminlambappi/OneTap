<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialScore extends Model
{
    protected $fillable = [
        'user_id', 'total_score', 'weekly_score',
        'campus_rank', 'global_rank', 'badges', 'achievements',
    ];

    protected function casts(): array
    {
        return [
            'badges'       => 'array',
            'achievements' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
