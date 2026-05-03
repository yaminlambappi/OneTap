<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'lat', 'lng', 'city', 'address',
        'campus_id', 'cover_image', 'checkin_count', 'post_count',
        'vibe_score', 'is_trending', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'lat'        => 'float',
            'lng'        => 'float',
            'is_trending' => 'boolean',
            'is_active'  => 'boolean',
        ];
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
