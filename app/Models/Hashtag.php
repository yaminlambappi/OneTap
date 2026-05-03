<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $fillable = ['name', 'usage_count', 'today_count', 'is_trending', 'campus_id'];

    protected function casts(): array
    {
        return ['is_trending' => 'boolean'];
    }

    public function posts()
    {
        return $this->morphedByMany(Post::class, 'hashtaggable');
    }

    public function confessions()
    {
        return $this->morphedByMany(Confession::class, 'hashtaggable');
    }
}
