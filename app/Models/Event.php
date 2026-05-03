<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'user_id', 'campus_id', 'location_id', 'community_id',
        'title', 'description', 'type', 'cover_image',
        'lat', 'lng', 'venue_name', 'starts_at', 'ends_at',
        'max_attendees', 'attendee_count', 'is_public',
        'is_anonymous', 'is_cancelled',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'    => 'datetime',
            'ends_at'      => 'datetime',
            'is_public'    => 'boolean',
            'is_anonymous' => 'boolean',
            'is_cancelled' => 'boolean',
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

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function attendances()
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendances')
            ->withPivot('status')
            ->withTimestamps();
    }
}
