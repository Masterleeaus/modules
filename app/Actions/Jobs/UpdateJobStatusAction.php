<?php

namespace App\Actions\Jobs;

use App\Events\JobStatusChanged;
use App\Models\Job;

class UpdateJobStatusAction
{
    /**
     * Transition a job to a new status, stamping the appropriate timestamp,
     * and dispatch the JobStatusChanged event.
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

        JobStatusChanged::dispatch($job->fresh(), $oldStatus, $newStatus);

        return $job->fresh();
    }
}
