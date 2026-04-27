<?php

namespace App\Actions\Jobs;

use App\Events\JobCreated;
use App\Models\Job;

class CreateJobAction
{
    /**
     * Create a new job and dispatch the JobCreated event.
     *
     * @param  array<string, mixed>  $data  Validated job attributes (must include organization_id)
     */
    public function execute(array $data): Job
    {
        $job = Job::create([
            ...$data,
            'status' => Job::STATUS_SCHEDULED,
        ]);

        JobCreated::dispatch($job);

        return $job;
    }
}
