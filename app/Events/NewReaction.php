<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewReaction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $reactableType,
        public int    $reactableId,
        public string $reactionType,
        public array  $counts
    ) {}

    public function broadcastOn(): array
    {
        $modelClass = $this->reactableType;
        $reactable = $modelClass::find($this->reactableId);
        $campusId = $reactable->campus_id ?? null;
        
        $channels = [new Channel("reactions.global")];
        if ($campusId) {
            $channels[] = new Channel("reactions.campus.{$campusId}");
        }
        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'reactable_type' => $this->reactableType,
            'reactable_id'   => $this->reactableId,
            'type'           => $this->reactionType,
            'counts'         => $this->counts,
        ];
    }

    public function broadcastAs(): string
    {
        return 'reaction.updated';
    }
}
