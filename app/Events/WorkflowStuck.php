<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowStuck
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $entityType,
        public readonly Model  $entity,
        public readonly string $state,
        public readonly int    $hoursStuck,
    ) {}
}
