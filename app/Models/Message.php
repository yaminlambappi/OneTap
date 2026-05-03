<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'live_room_id', 'user_id', 'body', 'type',
        'is_anonymous', 'anonymous_alias', 'anonymous_avatar_seed',
    ];

    protected function casts(): array
    {
        return ['is_anonymous' => 'boolean'];
    }

    public function room()
    {
        return $this->belongsTo(LiveRoom::class, 'live_room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
