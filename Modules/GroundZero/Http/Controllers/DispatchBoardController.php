<?php

namespace Modules\GroundZero\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\GroundZero\Services\DispatchBroadcaster;
use Modules\GroundZero\Services\DispatchService;
use Modules\GroundZero\Services\RouteOptimiserService;

class DispatchBoardController extends Controller
{
    public function __construct(
        private readonly DispatchService $dispatchService,
        private readonly DispatchBroadcaster $broadcaster,
        private readonly RouteOptimiserService $routeOptimiser,
    ) {}

    /**
     * Return the board data: technicians with load + today's jobs grouped by
     * technician.
     */
    public function board(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $technicians = $this->dispatchService->getTechniciansWithLoad($orgId);
        $jobsByTech  = $this->dispatchService->getBoardJobsByTechnician($orgId);

        $columns = $technicians->map(function (array $tech) use ($jobsByTech) {
            $jobs = $jobsByTech->get($tech['id'], collect())->map(fn (Job $job) => [
                'id'           => $job->id,
                'title'        => $job->title,
                'status'       => $job->status,
                'scheduled_at' => $job->scheduled_at?->toISOString(),
                'customer'     => $job->customer
                    ? "{$job->customer->first_name} {$job->customer->last_name}"
                    : null,
                'address'      => $job->property
                    ? "{$job->property->address_line1}, {$job->property->city}"
                    : null,
            ])->values();

            return array_merge($tech, ['jobs' => $jobs]);
        });

        // Unassigned jobs (assigned_to is null)
        $unassigned = $jobsByTech->get(null, collect())->map(fn (Job $job) => [
            'id'           => $job->id,
            'title'        => $job->title,
            'status'       => $job->status,
            'scheduled_at' => $job->scheduled_at?->toISOString(),
            'customer'     => $job->customer
                ? "{$job->customer->first_name} {$job->customer->last_name}"
                : null,
            'address'      => $job->property
                ? "{$job->property->address_line1}, {$job->property->city}"
                : null,
        ])->values();

        return response()->json([
            'columns'    => $columns,
            'unassigned' => $unassigned,
        ]);
    }

    /**
     * Reassign a job to a different technician (drag-drop endpoint).
     */
    public function assign(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);

        $validated = $request->validate([
            'technician_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $technician = User::findOrFail($validated['technician_id']);

        abort_unless($technician->organization_id === $request->user()->organization_id, 403);

        $previousTechnicianId = $job->assigned_to;
        $previousTechnician   = $previousTechnicianId
            ? User::find($previousTechnicianId)
            : null;

        $job = $this->dispatchService->assignJob($job, $technician);

        $this->broadcaster->broadcastJobReassigned($job, $previousTechnician, $technician);

        return response()->json(['data' => ['job_id' => $job->id, 'technician_id' => $technician->id]]);
    }

    /**
     * Return the optimised route for a technician's today's jobs.
     */
    public function optimiseRoute(Request $request, User $user): JsonResponse
    {
        abort_unless($user->organization_id === $request->user()->organization_id, 403);

        $jobs = Job::where('assigned_to', $user->id)
            ->where('status', Job::STATUS_SCHEDULED)
            ->whereDate('scheduled_at', today())
            ->whereHas('property', fn ($q) => $q->whereNotNull('latitude')->whereNotNull('longitude'))
            ->with('property:id,latitude,longitude,address_line1,city')
            ->orderBy('scheduled_at')
            ->get();

        if ($jobs->isEmpty()) {
            return response()->json(['data' => ['ordered_jobs' => [], 'total_distance_metres' => 0, 'total_duration_seconds' => 0]]);
        }

        // Try to use the technician's current GPS as origin; otherwise fall
        // back to the first job's address.
        $latestLocation = \App\Models\DriverLocation::where('user_id', $user->id)
            ->orderByDesc('id')
            ->first(['latitude', 'longitude']);

        $origin = $latestLocation
            ? [(float) $latestLocation->latitude, (float) $latestLocation->longitude]
            : [(float) $jobs->first()->property->latitude, (float) $jobs->first()->property->longitude];

        $destinations = $jobs->map(fn (Job $j) => sprintf(
            '%s,%s',
            $j->property->latitude,
            $j->property->longitude,
        ))->all();

        $result = $this->routeOptimiser->optimise($origin, $destinations);

        if (! $result) {
            // Return jobs in scheduled_at order as a fallback.
            return response()->json([
                'data' => [
                    'ordered_jobs'           => $jobs->map(fn (Job $j) => $j->id)->values(),
                    'total_distance_metres'  => 0,
                    'total_duration_seconds' => 0,
                ],
            ]);
        }

        $orderedJobIds = collect($result['ordered_indexes'])->map(fn (int $i) => $jobs[$i]->id)->values();

        return response()->json([
            'data' => [
                'ordered_jobs'           => $orderedJobIds,
                'total_distance_metres'  => $result['total_distance_metres'],
                'total_duration_seconds' => $result['total_duration_seconds'],
            ],
        ]);
    }
}
