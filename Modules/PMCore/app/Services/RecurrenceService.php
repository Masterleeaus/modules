<?php

namespace Modules\PMCore\app\Services;

use Carbon\Carbon;
use Modules\PMCore\app\Enums\RecurrenceType;
use Modules\PMCore\app\Models\Project;

class RecurrenceService
{
    /**
     * Generate a new child job from a recurring parent project.
     * The child inherits all cleaning-relevant fields and its start/end
     * dates are shifted by one recurrence period.
     */
    public function generateNextJob(Project $parent): Project
    {
        $recurrence = $parent->recurrence instanceof RecurrenceType
            ? $parent->recurrence
            : RecurrenceType::from($parent->recurrence ?? 'none');

        $baseDate = $parent->end_date ?? $parent->start_date ?? now()->toDateString();
        $nextStart = $recurrence->nextDate(Carbon::parse($baseDate));

        $duration = null;
        if ($parent->start_date && $parent->end_date) {
            $duration = $parent->start_date->diffInDays($parent->end_date);
        }

        $child = $parent->replicate([
            'code',
            'recurrence_parent_id',
            'actual_cost',
            'actual_revenue',
            'actual_price',
            'completion_percentage',
            'completed_at',
            'created_at',
            'updated_at',
        ]);

        $child->recurrence = RecurrenceType::NONE->value;
        $child->recurrence_parent_id = $parent->id;
        $child->start_date = $nextStart->toDateString();
        $child->end_date = $duration !== null
            ? $nextStart->copy()->addDays($duration)->toDateString()
            : null;
        $child->status = \Modules\PMCore\app\Enums\ProjectStatus::PLANNING;
        $child->completion_percentage = 0;
        $child->completed_at = null;
        $child->is_archived = false;
        $child->save();

        return $child;
    }

    /**
     * Process all active recurring projects that are due for a new child job.
     * Returns the number of child jobs created.
     */
    public function processRecurringJobs(): int
    {
        $count = 0;

        $recurringProjects = Project::whereNotNull('recurrence')
            ->where('recurrence', '!=', RecurrenceType::NONE->value)
            ->whereNull('recurrence_parent_id') // only top-level parents
            ->whereNull('deleted_at')
            ->get();

        foreach ($recurringProjects as $parent) {
            if ($this->isDueForNextJob($parent)) {
                $this->generateNextJob($parent);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Determine whether a recurring parent is due for a new child job today.
     * A new child is due when the latest child's end_date (or start_date) is
     * in the past and no child is currently scheduled in the future.
     */
    private function isDueForNextJob(Project $parent): bool
    {
        $recurrence = $parent->recurrence instanceof RecurrenceType
            ? $parent->recurrence
            : RecurrenceType::tryFrom($parent->recurrence ?? '');

        if (! $recurrence || $recurrence === RecurrenceType::NONE) {
            return false;
        }

        $latestChild = $parent->children()
            ->orderByDesc('start_date')
            ->first();

        $baseDate = $latestChild
            ? ($latestChild->end_date ?? $latestChild->start_date)
            : ($parent->end_date ?? $parent->start_date);

        if (! $baseDate) {
            return false;
        }

        $nextDue = $recurrence->nextDate(Carbon::parse($baseDate));

        return $nextDue->isPast() || $nextDue->isToday();
    }
}
