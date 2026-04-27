<?php

namespace Modules\GroundZero\Services;

use App\Models\DriverLocation;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Collection;
use Modules\GroundZero\Events\JobDispatched;

class DispatchService
{
    /**
     * Assign a job to a technician and fire the JobDispatched event.
     */
    public function assignJob(Job $job, User $technician): Job
    {
        $previousTechnicianId = $job->assigned_to;

        $job->update(['assigned_to' => $technician->id]);

        JobDispatched::dispatch($job, $technician, $previousTechnicianId);

        return $job->fresh();
    }

    /**
     * Return all technicians for an organisation with their current location
     * and active job, ordered by number of scheduled jobs today (ascending)
     * so the least-loaded technician appears first.
     *
     * @return Collection<int, array{
     *     id: int,
     *     name: string,
     *     scheduled_today: int,
     *     location: array{latitude: float, longitude: float}|null,
     *     current_job: array{id: int, title: string, status: string}|null,
     * }>
     */
    public function getTechniciansWithLoad(int $organizationId): Collection
    {
        $technicians = User::role('technician')
            ->where('organization_id', $organizationId)
            ->get(['id', 'name']);

        if ($technicians->isEmpty()) {
            return collect();
        }

        $techIds = $technicians->pluck('id');

        $latestLocations = DriverLocation::whereIn('user_id', $techIds)
            ->whereIn('id', function ($sub) use ($techIds) {
                $sub->selectRaw('MAX(id)')
                    ->from('driver_locations')
                    ->whereIn('user_id', $techIds)
                    ->groupBy('user_id');
            })
            ->get(['user_id', 'latitude', 'longitude'])
            ->keyBy('user_id');

        $activeJobs = Job::whereIn('assigned_to', $techIds)
            ->whereIn('status', [Job::STATUS_EN_ROUTE, Job::STATUS_IN_PROGRESS])
            ->get(['id', 'assigned_to', 'title', 'status'])
            ->keyBy('assigned_to');

        $scheduledCounts = Job::whereIn('assigned_to', $techIds)
            ->where('status', Job::STATUS_SCHEDULED)
            ->whereDate('scheduled_at', today())
            ->selectRaw('assigned_to, COUNT(*) as cnt')
            ->groupBy('assigned_to')
            ->pluck('cnt', 'assigned_to');

        return $technicians
            ->map(function (User $tech) use ($latestLocations, $activeJobs, $scheduledCounts) {
                $location   = $latestLocations->get($tech->id);
                $activeJob  = $activeJobs->get($tech->id);
                $count      = (int) ($scheduledCounts->get($tech->id, 0));

                return [
                    'id'              => $tech->id,
                    'name'            => $tech->name,
                    'scheduled_today' => $count,
                    'location'        => $location ? [
                        'latitude'  => (float) $location->latitude,
                        'longitude' => (float) $location->longitude,
                    ] : null,
                    'current_job' => $activeJob ? [
                        'id'     => $activeJob->id,
                        'title'  => $activeJob->title,
                        'status' => $activeJob->status,
                    ] : null,
                ];
            })
            ->sortBy('scheduled_today')
            ->values();
    }

    /**
     * Return today's scheduled jobs for an organisation grouped by assigned
     * technician id, suitable for rendering the dispatch board columns.
     *
     * @return Collection<int, Collection<int, Job>>
     */
    public function getBoardJobsByTechnician(int $organizationId): Collection
    {
        return Job::where('organization_id', $organizationId)
            ->whereDate('scheduled_at', today())
            ->whereNotIn('status', [Job::STATUS_CANCELLED])
            ->with(['customer:id,first_name,last_name', 'property:id,address_line1,city'])
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy('assigned_to');
    }
}
