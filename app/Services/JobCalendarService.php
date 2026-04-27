<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Support\Collection;

class JobCalendarService
{
    /**
     * Return jobs as FullCalendar-compatible event objects for the given date range.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getEvents(int $organizationId, string $start, string $end): Collection
    {
        $jobs = Job::where('organization_id', $organizationId)
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$start, $end])
            ->with(['customer', 'jobType', 'assignedTechnician', 'crew.user'])
            ->get();

        return $jobs->map(fn (Job $job) => [
            'id'              => $job->id,
            'title'           => $job->customer
                ? "{$job->customer->last_name}: {$job->title}"
                : $job->title,
            'start'           => $job->scheduled_at->toIso8601String(),
            'end'             => $job->started_at && $job->completed_at
                ? $job->completed_at->toIso8601String()
                : null,
            'url'             => "/owner/jobs/{$job->id}",
            'backgroundColor' => $job->jobType?->color ?? '#6366f1',
            'borderColor'     => $job->jobType?->color ?? '#6366f1',
            'textColor'       => '#ffffff',
            'extendedProps'   => [
                'status'     => $job->status,
                'customer'   => $job->customer?->full_name,
                'technician' => $job->assignedTechnician?->name,
                'crew'       => $job->crew->map(fn ($c) => [
                    'name' => $c->user?->name,
                    'role' => $c->role,
                ]),
            ],
        ]);
    }
}
