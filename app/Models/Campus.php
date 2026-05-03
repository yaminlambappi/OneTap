<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'name', 'slug', 'short_name', 'city', 'country',
        'lat', 'lng', 'radius_meters', 'cover_image',
        'color_primary', 'color_secondary', 'member_count', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function communities()
    {
        return $this->hasMany(Community::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function confessions()
    {
        return $this->hasMany(Confession::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function trendingTopics()
    {
        return $this->hasMany(TrendingTopic::class);
    }
}
