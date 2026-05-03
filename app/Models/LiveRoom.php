<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LiveRoom extends Model
{
    protected $fillable = [
        'code', 'created_by', 'campus_id', 'topic', 'type',
        'participant_count', 'max_participants', 'is_anonymous',
        'is_active', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'is_active'    => 'boolean',
            'expires_at'   => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($room) {
            $room->code = strtoupper(Str::random(6));
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function participants()
    {
        return $this->hasMany(LiveRoomParticipant::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
