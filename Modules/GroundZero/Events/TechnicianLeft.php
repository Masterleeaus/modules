<?php

namespace Modules\GroundZero\Events;

use App\Models\DriverLocation;
use App\Models\Job;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TechnicianLeft implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Job $job,
        public readonly DriverLocation $location,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('dispatch.' . $this->job->organization_id);
    }

    public function broadcastAs(): string
    {
        return 'technician.left';
    }

    public function broadcastWith(): array
    {
        return [
            'job_id'        => $this->job->id,
            'technician_id' => $this->location->user_id,
            'latitude'      => (float) $this->location->latitude,
            'longitude'     => (float) $this->location->longitude,
            'recorded_at'   => $this->location->recorded_at->toISOString(),
        ];
    }
}
