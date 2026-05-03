<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'phone', 'email', 'password',
        'bio', 'avatar', 'cover_image', 'campus_id',
        'vibe_tags', 'reputation_score', 'mystery_score',
        'influence_score', 'chaos_score', 'local_rank', 'influence_level',
        'is_anonymous_mode', 'anonymous_alias', 'anonymous_avatar_seed',
        'is_verified', 'is_banned', 'banned_until',
        'last_lat', 'last_lng', 'location_updated_at', 'last_active_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'             => 'hashed',
            'vibe_tags'            => 'array',
            'is_anonymous_mode'    => 'boolean',
            'is_verified'          => 'boolean',
            'is_banned'            => 'boolean',
            'last_lat'             => 'float',
            'last_lng'             => 'float',
            'last_active_at'       => 'datetime',
            'location_updated_at'  => 'datetime',
            'banned_until'         => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function confessions()
    {
        return $this->hasMany(Confession::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function streak()
    {
        return $this->hasOne(UserStreak::class);
    }

    public function socialScore()
    {
        return $this->hasOne(SocialScore::class);
    }

    public function presence()
    {
        return $this->hasOne(UserPresence::class);
    }

    public function communities()
    {
        return $this->belongsToMany(Community::class, 'community_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function friendships()
    {
        return $this->hasMany(Friendship::class, 'sender_id')
            ->orWhere('receiver_id', $this->id);
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'sender_id', 'receiver_id')
            ->wherePivot('status', 'accepted')
            ->union(
                $this->belongsToMany(User::class, 'friendships', 'receiver_id', 'sender_id')
                    ->wherePivot('status', 'accepted')
            );
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocker_id', 'blocked_id');
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isOnline(): bool
    {
        return $this->presence?->is_online ?? false;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return $this->avatar;
        }
        // Dicebear avatars for anonymous/default
        $seed = $this->anonymous_avatar_seed ?? $this->username;
        return "https://api.dicebear.com/7.x/bottts/svg?seed={$seed}";
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? $this->username;
    }
}
