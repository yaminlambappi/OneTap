<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'type', 'campus_id', 'created_by',
        'avatar', 'cover_image', 'lat', 'lng', 'radius_meters',
        'member_count', 'post_count', 'allow_anonymous', 'is_nsfw', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'allow_anonymous' => 'boolean',
            'is_nsfw'         => 'boolean',
            'is_active'       => 'boolean',
        ];
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'community_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
