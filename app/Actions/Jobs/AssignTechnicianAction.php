<?php

namespace App\Actions\Jobs;

use App\Models\Job;

class AssignTechnicianAction
{
    /**
     * Assign (or unassign) a technician to a job.
     *
     * @param  int|null  $userId  The user ID to assign, or null to unassign.
     */
    public function execute(Job $job, ?int $userId): Job
    {
        $job->update(['assigned_to' => $userId]);

        return $job->fresh();
    }
}
