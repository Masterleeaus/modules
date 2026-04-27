<?php

namespace Modules\TitanGo\Services;

use App\Models\Job;
use Illuminate\Support\Collection;

class RouteSheetService
{
    /**
     * Return today's jobs for a technician ordered by scheduled time.
     *
     * When a RouteOptimiserService is available (GroundZero module), jobs are
     * ordered by the optimised route; otherwise they fall back to chronological
     * order by scheduled_at.
     *
     * @return Collection<int, Job>
     */
    public function forTechnician(int $userId): Collection
    {
        $jobs = Job::where('assigned_to', $userId)
            ->whereDate('scheduled_at', today())
            ->whereNotIn('status', [Job::STATUS_CANCELLED])
            ->with(['customer', 'property', 'jobType', 'checklistItems', 'attachments', 'lineItems'])
            ->orderBy('scheduled_at')
            ->get();

        return $this->optimise($jobs);
    }

    /**
     * Apply route optimisation if the GroundZero module provides it.
     *
     * @param  Collection<int, Job>  $jobs
     * @return Collection<int, Job>
     */
    private function optimise(Collection $jobs): Collection
    {
        if ($jobs->isEmpty()) {
            return $jobs;
        }

        if (class_exists(\Modules\GroundZero\Services\RouteOptimiserService::class)) {
            try {
                return app(\Modules\GroundZero\Services\RouteOptimiserService::class)->optimise($jobs);
            } catch (\Throwable) {
                // Fall through to default ordering on any error.
            }
        }

        return $jobs;
    }
}
