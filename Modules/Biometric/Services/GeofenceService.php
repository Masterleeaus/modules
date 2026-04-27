<?php

namespace Modules\Biometric\Services;

use Modules\Biometric\Entities\BiometricSetting;

/**
 * GeofenceService
 *
 * Validates whether a set of GPS coordinates falls within the configured
 * geofence radius for a given company.  The radius is stored in the
 * `biometric_settings` table (column `geofence_radius`, in metres) so it
 * is fully company-configurable and never hard-coded.
 */
class GeofenceService
{
    /**
     * Earth's mean radius in metres.
     */
    private const EARTH_RADIUS_M = 6_371_000;

    /**
     * Determine whether ($lat, $lng) is within the geofence defined by
     * ($centreLat, $centreLng) and the radius stored in the company's
     * biometric settings.
     *
     * @param  int        $companyId
     * @param  float      $lat         Device/GPS latitude
     * @param  float      $lng         Device/GPS longitude
     * @param  float      $centreLat   Job-site / office latitude
     * @param  float      $centreLng   Job-site / office longitude
     * @return bool
     */
    public function passes(
        int $companyId,
        float $lat,
        float $lng,
        float $centreLat,
        float $centreLng
    ): bool {
        $settings = BiometricSetting::where('company_id', $companyId)->first();

        if (! $settings || ! $settings->geofence_enabled) {
            return true; // Geofence not enabled — always passes
        }

        $radiusMetres = (int) ($settings->geofence_radius ?? 200);

        $distance = $this->haversineDistanceMetres(
            $lat,
            $lng,
            $centreLat,
            $centreLng
        );

        return $distance <= $radiusMetres;
    }

    /**
     * Calculate the Haversine distance in metres between two GPS coordinates.
     *
     * @param  float $lat1
     * @param  float $lng1
     * @param  float $lat2
     * @param  float $lng2
     * @return float  Distance in metres
     */
    public function haversineDistanceMetres(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_M * $c;
    }
}
