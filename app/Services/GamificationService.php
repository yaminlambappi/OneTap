<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStreak;
use App\Models\SocialScore;

class GamificationService
{
    // Score weights
    const SCORE_POST        = 10;
    const SCORE_REACTION    = 2;
    const SCORE_COMMENT     = 5;
    const SCORE_POLL_VOTE   = 1;
    const SCORE_EVENT_JOIN  = 3;
    const SCORE_CONFESSION  = 8;
    const SCORE_TRENDING    = 50;

    private array $rankThresholds = [
        'newcomer'  => 0,
        'rising'    => 100,
        'known'     => 500,
        'legend'    => 2000,
    ];

    private array $influenceLevels = [
        'ghost'    => 0,
        'spark'    => 50,
        'flame'    => 200,
        'inferno'  => 1000,
    ];

    private array $badges = [
        'early_adopter'    => ['label' => 'Early Adopter', 'emoji' => '🌟', 'threshold' => 0],
        'first_post'       => ['label' => 'First Drop', 'emoji' => '📸', 'threshold' => 1],
        'streak_7'         => ['label' => '7-Day Streak', 'emoji' => '🔥', 'threshold' => 7],
        'streak_30'        => ['label' => 'Month Grind', 'emoji' => '💎', 'threshold' => 30],
        'chaos_king'       => ['label' => 'Chaos King', 'emoji' => '👑', 'threshold' => 500],
        'confession_lord'  => ['label' => 'Confession Lord', 'emoji' => '🎭', 'threshold' => 10],
        'poll_master'      => ['label' => 'Poll Master', 'emoji' => '📊', 'threshold' => 5],
        'trending_once'    => ['label' => 'Went Viral', 'emoji' => '🚀', 'threshold' => 1],
        'local_legend'     => ['label' => 'Local Legend', 'emoji' => '🏆', 'threshold' => 2000],
    ];

    public function awardPoints(User $user, string $action, int $multiplier = 1): void
    {
        $points = match ($action) {
            'post'         => self::SCORE_POST,
            'reaction'     => self::SCORE_REACTION,
            'comment'      => self::SCORE_COMMENT,
            'poll_vote'    => self::SCORE_POLL_VOTE,
            'event_join'   => self::SCORE_EVENT_JOIN,
            'confession'   => self::SCORE_CONFESSION,
            'trending'     => self::SCORE_TRENDING,
            default        => 0,
        } * $multiplier;

        if ($points === 0) return;

        $score = SocialScore::firstOrCreate(['user_id' => $user->id]);
        $score->increment('total_score', $points);
        $score->increment('weekly_score', $points);

        // Update user scores
        $user->increment('reputation_score', (int) ($points * 0.5));
        $user->increment('influence_score', (int) ($points * 0.3));

        if ($action === 'confession') {
            $user->increment('mystery_score', 5);
        }

        $this->updateRank($user);
        $this->checkBadges($user, $score);
    }

    public function updateStreak(User $user): void
    {
        $streak = UserStreak::firstOrCreate(['user_id' => $user->id]);
        $today  = now()->toDateString();

        if ($streak->last_active_date?->toDateString() === $today) {
            return; // Already counted today
        }

        $yesterday = now()->subDay()->toDateString();
        if ($streak->last_active_date?->toDateString() === $yesterday) {
            $streak->increment('current_streak');
        } else {
            $streak->current_streak = 1;
        }

        if ($streak->current_streak > $streak->longest_streak) {
            $streak->longest_streak = $streak->current_streak;
        }

        $streak->last_active_date = $today;
        $streak->save();

        // Bonus points for streaks
        if ($streak->current_streak % 7 === 0) {
            $this->awardPoints($user, 'trending'); // streak milestone bonus
        }
    }

    private function updateRank(User $user): void
    {
        $score = $user->reputation_score;
        $rank  = 'newcomer';

        foreach ($this->rankThresholds as $level => $threshold) {
            if ($score >= $threshold) {
                $rank = $level;
            }
        }

        $influence = 'ghost';
        foreach ($this->influenceLevels as $level => $threshold) {
            if ($user->influence_score >= $threshold) {
                $influence = $level;
            }
        }

        $user->update([
            'local_rank'      => $rank,
            'influence_level' => $influence,
        ]);
    }

    private function checkBadges(User $user, SocialScore $score): void
    {
        $badges = $score->badges ?? [];
        $streak = $user->streak;

        $checks = [
            'first_post'      => ($user->streak?->total_posts ?? 0) >= 1,
            'streak_7'        => ($streak?->current_streak ?? 0) >= 7,
            'streak_30'       => ($streak?->current_streak ?? 0) >= 30,
            'chaos_king'      => $user->chaos_score >= 500,
            'local_legend'    => $score->total_score >= 2000,
        ];

        $updated = false;
        foreach ($checks as $badge => $earned) {
            if ($earned && !in_array($badge, $badges)) {
                $badges[] = $badge;
                $updated  = true;
            }
        }

        if ($updated) {
            $score->update(['badges' => $badges]);
        }
    }

    public function getBadgeDetails(array $badgeKeys): array
    {
        return array_filter(
            $this->badges,
            fn($key) => in_array($key, $badgeKeys),
            ARRAY_FILTER_USE_KEY
        );
    }
}
