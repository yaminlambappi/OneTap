<?php

namespace App\Services;

use Illuminate\Support\Str;

class AnonymousIdentityService
{
    private array $adjectives = [
        'Shadow', 'Neon', 'Cosmic', 'Phantom', 'Mystic', 'Rogue', 'Velvet',
        'Chaos', 'Silent', 'Blazing', 'Frozen', 'Hollow', 'Crimson', 'Lunar',
        'Toxic', 'Glitch', 'Void', 'Storm', 'Feral', 'Spectral',
    ];

    private array $nouns = [
        'Panda', 'Fox', 'Wolf', 'Raven', 'Tiger', 'Viper', 'Hawk',
        'Ghost', 'Comet', 'Spark', 'Cipher', 'Echo', 'Drift', 'Pulse',
        'Wraith', 'Flare', 'Nexus', 'Shade', 'Blaze', 'Specter',
    ];

    /**
     * Generate a deterministic alias from user_id + context seed.
     * Same user gets same alias per context (campus, community, etc.)
     * but different aliases across contexts.
     */
    public function generateAlias(int $userId, string $context = 'global'): string
    {
        $seed = crc32($userId . ':' . $context);
        $adj  = $this->adjectives[abs($seed) % count($this->adjectives)];
        $noun = $this->nouns[abs($seed >> 4) % count($this->nouns)];
        $num  = (abs($seed) % 90) + 10; // 10-99

        return "{$adj}{$noun}{$num}";
    }

    /**
     * Generate avatar seed for DiceBear.
     */
    public function generateAvatarSeed(int $userId, string $context = 'global'): string
    {
        return hash('crc32', $userId . ':' . $context . ':avatar');
    }

    /**
     * Generate a one-time alias for confessions (fully random, not deterministic).
     */
    public function generateConfessionAlias(): string
    {
        $adj  = $this->adjectives[array_rand($this->adjectives)];
        $noun = $this->nouns[array_rand($this->nouns)];
        $num  = rand(10, 99);
        return "{$adj}{$noun}{$num}";
    }

    public function generateConfessionAvatarSeed(): string
    {
        return Str::random(12);
    }

    public function getAvatarUrl(string $seed, string $style = 'bottts'): string
    {
        return "https://api.dicebear.com/7.x/{$style}/svg?seed={$seed}&backgroundColor=0f172a";
    }
}
