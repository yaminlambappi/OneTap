<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPost implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Post $post) {}

    public function broadcastOn(): array
    {
        $channels = [new Channel('feed.global')];

        if ($this->post->campus_id) {
            $channels[] = new Channel("feed.campus.{$this->post->campus_id}");
        }

        if ($this->post->community_id) {
            $channels[] = new Channel("feed.community.{$this->post->community_id}");
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'id'           => $this->post->id,
            'type'         => $this->post->type,
            'body'         => $this->post->body,
            'author_name'  => $this->post->author_name,
            'author_avatar'=> $this->post->author_avatar,
            'is_anonymous' => $this->post->is_anonymous,
            'campus_id'    => $this->post->campus_id,
            'created_at'   => $this->post->created_at->toISOString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.post';
    }
}
