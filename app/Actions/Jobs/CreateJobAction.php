<?php

namespace App\Actions\Jobs;

use App\Events\JobCreated;
use App\Models\Job;
use App\Signals\SignalRouter;

class CreateJobAction
{
    /**
     * Create a new job, dispatch the JobCreated event, and route a
     * 'job.created' signal through the Signal Engine for intake recording
     * and governance.
     *
     * @param  array<string, mixed>  $data  Validated job attributes (must include organization_id)
     */
    public function execute(array $data): Job
    {
        $job = Job::create([
            ...$data,
            'status' => Job::STATUS_SCHEDULED,
        ]);

        // Direct event dispatch — keeps existing listeners working.
        JobCreated::dispatch($job);

        // Signal Engine recording — validation, governance, and dispatch log.
        app(SignalRouter::class)->route(
            source: 'internal',
            type: 'job.created',
            payload: ['job_id' => $job->id],
            organizationId: $job->organization_id,
        );

        return $job;
    }
}
