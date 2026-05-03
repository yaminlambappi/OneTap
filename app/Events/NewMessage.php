<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel("live-room.{$this->message->live_room_id}")];
    }

    public function broadcastWith(): array
    {
        return [
            'id'             => $this->message->id,
            'body'           => $this->message->body,
            'type'           => $this->message->type,
            'is_anonymous'   => $this->message->is_anonymous,
            'author_name'    => $this->message->is_anonymous
                ? ($this->message->anonymous_alias ?? 'Anonymous')
                : ($this->message->user?->display_name ?? 'Unknown'),
            'author_avatar'  => $this->message->is_anonymous
                ? "https://api.dicebear.com/7.x/bottts/svg?seed={$this->message->anonymous_avatar_seed}"
                : ($this->message->user?->avatar_url ?? ''),
            'created_at'     => $this->message->created_at->toISOString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.message';
    }
}
