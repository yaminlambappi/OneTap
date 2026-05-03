<?php

namespace App\Events;

use App\Models\Poll;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PollVoteUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Poll $poll) {}

    public function broadcastOn(): array
    {
        return [new Channel("poll.{$this->poll->id}")];
    }

    public function broadcastWith(): array
    {
        return [
            'poll_id'     => $this->poll->id,
            'total_votes' => $this->poll->total_votes,
            'options'     => $this->poll->options->map(fn($o) => [
                'id'         => $o->id,
                'label'      => $o->label,
                'vote_count' => $o->vote_count,
                'percentage' => $this->poll->total_votes > 0
                    ? round(($o->vote_count / $this->poll->total_votes) * 100)
                    : 0,
            ]),
        ];
    }

    public function broadcastAs(): string
    {
        return 'poll.updated';
    }
}
