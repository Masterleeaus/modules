<?php

namespace TitanZero\FilamentChatbot\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use TitanZero\FilamentChatbot\Models\AssistantRun;

class AssistantRunCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly AssistantRun $run,
    ) {}

    /**
     * Broadcast on a private channel scoped to the authenticated user.
     *
     * The user_identifier is stored as "user:{id}". For guest/session threads
     * there is no private channel, so no broadcast is made.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $identifier = $this->run->thread->user_identifier ?? '';

        if (! str_starts_with($identifier, 'user:')) {
            return [];
        }

        $userId = substr($identifier, strlen('user:'));

        return [new PrivateChannel("assistant.user.{$userId}")];
    }

    /**
     * Payload sent to the browser via the broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'run_id' => $this->run->id,
            'status' => $this->run->status,
            'output' => $this->run->output,
            'error'  => $this->run->error,
        ];
    }

    /**
     * The event name as seen by the JavaScript Echo listener.
     */
    public function broadcastAs(): string
    {
        return 'AssistantRunCompleted';
    }
}
