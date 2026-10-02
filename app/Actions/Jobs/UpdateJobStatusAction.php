<?php

namespace App\Actions\Jobs;

use App\Events\JobStatusChanged;
use App\Models\Job;
use App\Signals\SignalRouter;

class UpdateJobStatusAction
{
    /**
     * Transition a job to a new status, stamping the appropriate timestamp,
     * dispatch the JobStatusChanged event, and route a 'job.status_changed'
     * signal through the Signal Engine for intake recording and governance.
     */
    public function execute(Job $job, string $newStatus): Job
    {
        $timestamps = match ($newStatus) {
            Job::STATUS_IN_PROGRESS => ['started_at'   => now()],
            Job::STATUS_COMPLETED   => ['completed_at' => now()],
            Job::STATUS_CANCELLED   => ['cancelled_at' => now()],
            default                 => [],
        };

        $oldStatus = $job->status;

        $job->update(['status' => $newStatus, ...$timestamps]);

        // Direct event dispatch — keeps existing listeners working.
        JobStatusChanged::dispatch($job->fresh(), $oldStatus, $newStatus);

        // Signal Engine recording — validation, governance, and dispatch log.
        app(SignalRouter::class)->route(
            source: 'internal',
            type: 'job.status_changed',
            payload: [
                'job_id'     => $job->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
            organizationId: $job->organization_id,
        );

        return $job->fresh();
    }
}
