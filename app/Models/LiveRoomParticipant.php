<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveRoomParticipant extends Model
{
    public $timestamps = false;
    protected $fillable = ['live_room_id', 'user_id', 'is_anonymous', 'joined_at', 'left_at'];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'joined_at'    => 'datetime',
            'left_at'      => 'datetime',
        ];
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
