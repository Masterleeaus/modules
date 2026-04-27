<?php
return [
    'name' => 'WorksuiteWorkOrders',
    'models' => [
        'user' => App\Models\User::class,
        'client' => App\Models\Client::class,
        'project' => App\Models\Project::class,
        'task' => App\Models\Task::class,
    ],
    ,
    'permissions' => [
        'workorders.view',
        'workorders.create',
        'workorders.update',
        'workorders.delete',
        'workorders.settings',
    ],
    ,
    'api_auth' => true, // wrap API routes in 'auth' middleware if true
    ,
    'webhook_url' => env('WORKORDERS_WEBHOOK_URL', ''),
    'webhook_retries' => env('WORKORDERS_WEBHOOK_RETRIES', 3),
    'webhook_backoff_seconds' => env('WORKORDERS_WEBHOOK_BACKOFF', 5),
];
