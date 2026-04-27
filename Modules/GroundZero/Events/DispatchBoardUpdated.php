<?php

namespace Modules\GroundZero\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DispatchBoardUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $organizationId,
        public readonly string $action,
        public readonly array $payload = [],
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('dispatch.' . $this->organizationId);
    }

    public function broadcastAs(): string
    {
        return 'board.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'action'  => $this->action,
            'payload' => $this->payload,
        ];
    }
}
