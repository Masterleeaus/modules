<?php

namespace Modules\GroundZero\Services;

use App\Models\Job;
use App\Models\User;
use Modules\GroundZero\Events\DispatchBoardUpdated;

class DispatchBroadcaster
{
    /**
     * Broadcast a board-update event on the organisation's private channel.
     *
     * @param  array<string, mixed>  $payload  Extra context to include
     */
    public function broadcastBoardUpdate(int $organizationId, string $action, array $payload = []): void
    {
        DispatchBoardUpdated::dispatch($organizationId, $action, $payload);
    }

    /**
     * Broadcast a job-reassigned update after drag-drop on the board.
     */
    public function broadcastJobReassigned(Job $job, ?User $previousTechnician, User $newTechnician): void
    {
        $this->broadcastBoardUpdate(
            $job->organization_id,
            'job_reassigned',
            [
                'job_id'                  => $job->id,
                'job_title'               => $job->title,
                'previous_technician_id'  => $previousTechnician?->id,
                'new_technician_id'        => $newTechnician->id,
                'new_technician_name'      => $newTechnician->name,
            ],
        );
    }
}
