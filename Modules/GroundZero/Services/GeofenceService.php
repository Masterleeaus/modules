<?php

namespace Modules\GroundZero\Services;

use App\Models\DriverLocation;
use App\Models\Job;
use App\Models\Property;
use Modules\GroundZero\Events\TechnicianArrived;
use Modules\GroundZero\Events\TechnicianLeft;

class GeofenceService
{
    /**
     * Earth radius in metres used for the Haversine formula.
     */
    private const EARTH_RADIUS_METRES = 6_371_000;

    /**
     * Geofence radius in metres.  A technician is considered "on site" when
     * within this distance from the property coordinates.
     */
    public const RADIUS_METRES = RouteOptimiserService::GEOFENCE_RADIUS_METRES;

    /**
     * Evaluate a newly recorded GPS fix against all active jobs assigned to
     * the technician.  Fires `TechnicianArrived` or `TechnicianLeft` as
     * appropriate and auto-advances job status on arrival.
     */
    public function evaluate(DriverLocation $location): void
    {
        $techId = $location->user_id;

        // Load all active (en_route / in_progress) jobs for the technician
        // that have a property with coordinates.
        $activeJobs = Job::where('assigned_to', $techId)
            ->whereIn('status', [Job::STATUS_EN_ROUTE, Job::STATUS_IN_PROGRESS])
            ->whereHas('property', fn ($q) => $q->whereNotNull('latitude')->whereNotNull('longitude'))
            ->with('property:id,latitude,longitude')
            ->get();

        foreach ($activeJobs as $job) {
            $property = $job->property;

            $insideNow = $this->isInsideGeofence(
                (float) $location->latitude,
                (float) $location->longitude,
                (float) $property->latitude,
                (float) $property->longitude,
            );

            if ($insideNow && $job->status === Job::STATUS_EN_ROUTE) {
                // Technician has arrived — advance status and fire event.
                $job->update([
                    'status'     => Job::STATUS_IN_PROGRESS,
                    'arrived_at' => $location->recorded_at,
                ]);

                TechnicianArrived::dispatch($job, $location);
            } elseif (! $insideNow && $job->status === Job::STATUS_IN_PROGRESS && $job->arrived_at !== null) {
                // Technician has left the geofence after having arrived.
                TechnicianLeft::dispatch($job, $location);
            }
        }
    }

    /**
     * Returns true when the given GPS coordinates are within the configured
     * radius of the target coordinates.
     *
     * Uses the Haversine formula for great-circle distance.
     */
    public function isInsideGeofence(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2,
    ): bool {
        return $this->haversineDistanceMetres($lat1, $lng1, $lat2, $lng2) <= self::RADIUS_METRES;
    }

    /**
     * Calculate the great-circle distance between two coordinates in metres.
     */
    public function haversineDistanceMetres(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2,
    ): float {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METRES * $c;
    }
}
