<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class ModerationService
{
    // Banned words list (extend as needed)
    private array $bannedPatterns = [
        '/\b(spam|scam)\b/i',
    ];

    /**
     * Check if content passes basic filters.
     */
    public function passesContentFilter(string $content): bool
    {
        foreach ($this->bannedPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Rate limit check for posting.
     */
    public function canPost(User $user): bool
    {
        $key = "post_rate:{$user->id}";
        return !RateLimiter::tooManyAttempts($key, 10); // 10 posts per window
    }

    public function recordPost(User $user): void
    {
        RateLimiter::hit("post_rate:{$user->id}", 3600); // 1 hour window
    }

    /**
     * Rate limit for confessions.
     */
    public function canConfess(User $user): bool
    {
        $key = "confession_rate:{$user->id}";
        return !RateLimiter::tooManyAttempts($key, 5); // 5 confessions per hour
    }

    public function recordConfession(User $user): void
    {
        RateLimiter::hit("confession_rate:{$user->id}", 3600);
    }

    /**
     * File a report against content.
     */
    public function report(User $reporter, Model $content, string $reason, ?string $details = null): Report
    {
        return Report::create([
            'reporter_id'      => $reporter->id,
            'reportable_type'  => get_class($content),
            'reportable_id'    => $content->id,
            'reason'           => $reason,
            'details'          => $details,
            'status'           => 'pending',
        ]);
    }

    /**
     * Auto-remove content that hits report threshold.
     */
    public function checkAutoRemove(Model $content): void
    {
        $reportCount = Report::where('reportable_type', get_class($content))
            ->where('reportable_id', $content->id)
            ->where('status', 'pending')
            ->count();

        if ($reportCount >= 5 && method_exists($content, 'update')) {
            $content->update([
                'is_removed'     => true,
                'removed_reason' => 'auto_moderation',
            ]);
        }
    }

    /**
     * Check if user is banned.
     */
    public function isBanned(User $user): bool
    {
        if (!$user->is_banned) return false;
        if ($user->banned_until && $user->banned_until->isPast()) {
            $user->update(['is_banned' => false, 'banned_until' => null]);
            return false;
        }
        return true;
    }

    /**
     * Detect spam patterns in content.
     */
    public function isSpam(string $content, User $user): bool
    {
        // Repeated content check
        $hash    = md5(strtolower(trim($content)));
        $cacheKey = "spam_check:{$user->id}:{$hash}";

        if (Cache::has($cacheKey)) {
            return true;
        }

        Cache::put($cacheKey, true, 300); // 5 min window
        return false;
    }
}
