<?php

namespace Modules\GroundZero\Events;

use App\Models\Job;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobDispatched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Job $job,
        public readonly User $technician,
        public readonly ?int $previousTechnicianId = null,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('dispatch.' . $this->job->organization_id);
    }

    public function broadcastAs(): string
    {
        return 'job.dispatched';
    }

    public function broadcastWith(): array
    {
        return [
            'job_id'                 => $this->job->id,
            'job_title'              => $this->job->title,
            'technician_id'          => $this->technician->id,
            'technician_name'        => $this->technician->name,
            'previous_technician_id' => $this->previousTechnicianId,
            'scheduled_at'           => $this->job->scheduled_at?->toISOString(),
        ];
    }
}
