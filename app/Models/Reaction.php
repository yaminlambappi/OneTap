<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = [
        'user_id', 'reactable_type', 'reactable_id', 'type', 'is_anonymous',
    ];

    protected function casts(): array
    {
        return ['is_anonymous' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactable()
    {
        return $this->morphTo();
    }

    public static function types(): array
    {
        return ['fire', 'heart', 'skull', 'laugh', 'shock', 'vibe'];
    }

    public static function emojis(): array
    {
        return [
            'fire'  => '🔥',
            'heart' => '❤️',
            'skull' => '💀',
            'laugh' => '😂',
            'shock' => '😱',
            'vibe'  => '✨',
        ];
    }
}
