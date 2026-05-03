<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    private array $templates = [
        'nearby_mention'       => 'Someone nearby just mentioned you. 👀',
        'confession_trending'  => 'Your confession is blowing up on campus. 🔥',
        'campus_chaos'         => 'Campus chaos level just spiked. Check what\'s happening. ⚡',
        'voted_mysterious'     => 'You were voted the most mysterious person today. 🎭',
        'nearby_poll_exploded' => 'A poll near you just exploded with votes. 📊',
        'post_trending'        => 'Your post is trending right now. 🚀',
        'new_reaction'         => '{actor} reacted {emoji} to your post.',
        'new_comment'          => '{actor} commented on your post.',
        'friend_request'       => '{actor} wants to connect with you.',
        'friend_accepted'      => '{actor} accepted your connection request.',
        'event_nearby'         => 'A new event just dropped near you. 📍',
        'live_room_invite'     => '{actor} invited you to a live room.',
        'streak_milestone'     => 'You\'re on a {days}-day streak. Keep it going! 🔥',
        'rank_up'              => 'You just ranked up to {rank}. 🏆',
        'badge_earned'         => 'You earned the {badge} badge! {emoji}',
    ];

    public function send(User $recipient, string $type, array $data = []): void
    {
        $message = $this->buildMessage($type, $data);

        DB::table('notifications')->insert([
            'id'              => \Illuminate\Support\Str::uuid(),
            'type'            => $type,
            'notifiable_type' => User::class,
            'notifiable_id'   => $recipient->id,
            'data'            => json_encode([
                'message' => $message,
                'type'    => $type,
                'meta'    => $data,
            ]),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Broadcast via WebSocket
        broadcast(new \App\Events\NotificationSent($recipient->id, [
            'message' => $message,
            'type'    => $type,
            'meta'    => $data,
        ]))->toOthers();
    }

    private function buildMessage(string $type, array $data): string
    {
        $template = $this->templates[$type] ?? 'You have a new notification.';

        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }

    public function markAllRead(User $user): void
    {
        DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function unreadCount(User $user): int
    {
        return DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}
