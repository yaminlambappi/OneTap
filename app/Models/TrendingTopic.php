<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendingTopic extends Model
{
    protected $fillable = [
        'topic', 'type', 'campus_id', 'score',
        'post_count', 'reaction_count', 'trending_date',
    ];

    protected function casts(): array
    {
        return ['trending_date' => 'date'];
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}
