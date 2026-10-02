<?php

use App\Models\Job;

return [

    /*
    |--------------------------------------------------------------------------
    | Workflow Definitions
    |--------------------------------------------------------------------------
    |
    | Each key is the entity type name (matches the entity_type column in
    | workflow_transitions). Each definition describes states, the allowed
    | transitions between them, per-transition guards, and the stuck-state
    | thresholds used by `workflow:detect-stuck`.
    |
    */

    'job' => [
        'initial' => Job::STATUS_SCHEDULED,

        'states' => [
            Job::STATUS_SCHEDULED,
            Job::STATUS_EN_ROUTE,
            Job::STATUS_IN_PROGRESS,
            Job::STATUS_ON_HOLD,
            Job::STATUS_COMPLETED,
            Job::STATUS_CANCELLED,
        ],

        'transitions' => [
            'dispatch' => [
                'from' => Job::STATUS_SCHEDULED,
                'to'   => Job::STATUS_EN_ROUTE,
            ],
            'arrive' => [
                'from' => Job::STATUS_EN_ROUTE,
                'to'   => Job::STATUS_IN_PROGRESS,
            ],
            'hold' => [
                'from' => Job::STATUS_IN_PROGRESS,
                'to'   => Job::STATUS_ON_HOLD,
            ],
            'resume' => [
                'from' => Job::STATUS_ON_HOLD,
                'to'   => Job::STATUS_IN_PROGRESS,
            ],
            'complete' => [
                'from' => Job::STATUS_IN_PROGRESS,
                'to'   => Job::STATUS_COMPLETED,
            ],
            'cancel' => [
                'from' => [
                    Job::STATUS_SCHEDULED,
                    Job::STATUS_EN_ROUTE,
                    Job::STATUS_ON_HOLD,
                ],
                'to' => Job::STATUS_CANCELLED,
            ],
        ],

        /*
        | Guards are executed before a transition is applied.  Each key is a
        | transition name; the value is an array of guard class names that must
        | all pass.  Guard classes must implement
        | App\Workflow\Guards\GuardInterface.
        */
        'guards' => [],

        /*
        | Stuck-state thresholds (in hours).  If an entity has been in the
        | given state for longer than the threshold, `workflow:detect-stuck`
        | will fire a WorkflowStuck event.
        */
        'stuck_thresholds' => [
            Job::STATUS_IN_PROGRESS => 24,
            Job::STATUS_EN_ROUTE    => 4,
            Job::STATUS_ON_HOLD     => 48,
        ],
    ],

];
