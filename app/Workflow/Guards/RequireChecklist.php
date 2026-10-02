<?php

namespace App\Workflow\Guards;

use App\Models\Job;
use Illuminate\Database\Eloquent\Model;

/**
 * Blocks the 'complete' transition if the job has required checklist items
 * that have not yet been marked complete.
 */
class RequireChecklist implements GuardInterface
{
    public function check(Model $entity, string $transition, array $context = []): bool
    {
        /** @var Job $entity */
        return ! $entity->checklistItems()
            ->where('is_required', true)
            ->whereNull('completed_at')
            ->exists();
    }

    public function message(): string
    {
        return 'All required checklist items must be completed before closing the job.';
    }
}
