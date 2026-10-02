<?php

namespace App\Workflow\Guards;

use App\Models\Job;
use Illuminate\Database\Eloquent\Model;

/**
 * Blocks the 'complete' transition if the job has no photo attachments.
 */
class RequireAtLeastOnePhoto implements GuardInterface
{
    public function check(Model $entity, string $transition, array $context = []): bool
    {
        /** @var Job $entity */
        return $entity->attachments()->whereIn('tag', ['before', 'after', null])->exists()
            || $entity->getMedia('job-photos')->isNotEmpty()
            || $entity->getMedia('before-photos')->isNotEmpty()
            || $entity->getMedia('after-photos')->isNotEmpty();
    }

    public function message(): string
    {
        return 'At least one job photo must be uploaded before completing the job.';
    }
}
