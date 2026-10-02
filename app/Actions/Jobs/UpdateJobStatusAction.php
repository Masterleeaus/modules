<?php

namespace App\Actions\Jobs;

use App\Events\JobStatusChanged;
use App\Models\Job;
use App\Workflow\Exceptions\InvalidTransitionException;
use App\Workflow\StateMachine;

class UpdateJobStatusAction
{
    /**
     * Transition a job to a new status via the workflow state machine,
     * stamping the appropriate timestamp, and dispatch the JobStatusChanged event.
     *
     * Accepts either a transition name (e.g. 'dispatch', 'complete') or a
     * target status string (e.g. 'en_route', 'completed').  When a status
     * string is provided the corresponding transition is resolved automatically.
     *
     * @param  array<string,mixed>  $context  Optional caller-supplied context forwarded to guards.
     *
     * @throws InvalidTransitionException  When the transition is not allowed from the current state.
     * @throws \RuntimeException           When a guard blocks the transition.
     */
    public function execute(Job $job, string $transitionOrStatus, array $context = []): Job
    {
        $machine    = new StateMachine('job');
        $transition = $this->resolveTransition($machine, $job, $transitionOrStatus);
        $oldStatus  = $job->status;

        $machine->apply($job, $transition, $context);

        $job->refresh();

        $this->stampTimestamps($job);

        JobStatusChanged::dispatch($job->fresh(), $oldStatus, $job->status);

        return $job->fresh();
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Resolve a transition name from either a transition name or a target
     * status string.
     */
    private function resolveTransition(StateMachine $machine, Job $job, string $transitionOrStatus): string
    {
        $definition = config('workflows.job.transitions', []);

        // If the caller passed a known transition name, use it directly.
        if (isset($definition[$transitionOrStatus])) {
            return $transitionOrStatus;
        }

        // Otherwise treat the value as a target status and find the matching
        // transition from the current state.
        $currentState = $machine->currentState($job);

        foreach ($definition as $name => $def) {
            $from = (array) $def['from'];
            if ($def['to'] === $transitionOrStatus && in_array($currentState, $from, true)) {
                return $name;
            }
        }

        throw new InvalidTransitionException($job->status, $transitionOrStatus);
    }

    /**
     * Stamp lifecycle timestamps after the state machine has updated the
     * status column.  These are convenience fields — the canonical record is
     * the workflow_transitions table.
     */
    private function stampTimestamps(Job $job): void
    {
        $timestamps = match ($job->status) {
            Job::STATUS_IN_PROGRESS => array_filter([
                'arrived_at' => $job->arrived_at ?? now(),
                'started_at' => $job->started_at ?? now(),
            ]),
            Job::STATUS_COMPLETED   => ['completed_at' => $job->completed_at ?? now()],
            Job::STATUS_CANCELLED   => ['cancelled_at' => $job->cancelled_at ?? now()],
            default                 => [],
        };

        if (! empty($timestamps)) {
            $job->updateQuietly($timestamps);
        }
    }
}
