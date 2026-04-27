<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

/**
 * Google Maps — route optimisation, geocoding, and ETA calculations
 * for cleaning job scheduling.
 * Uses getDecryptedApiKey() as the Google API key.
 */
class GoogleMapsIntegration
{
    private const DIRECTIONS_URL = 'https://maps.googleapis.com/maps/api/directions/json';
    private const GEOCODE_URL    = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function getProvider(): string { return 'google_maps'; }

    public function testConnection(Integration $integration): array
    {
        $key = $integration->getDecryptedApiKey();
        if (!$key) {
            return ['ok' => false, 'error' => 'No API key configured'];
        }

        // Simple geocode test to validate the key.
        $response = Http::get(self::GEOCODE_URL, [
            'address' => 'Sydney, Australia',
            'key'     => $key,
        ]);

        if ($response->successful() && $response->json('status') === 'OK') {
            return ['ok' => true, 'account' => 'Google Maps'];
        }

        $status = $response->json('status', 'UNKNOWN_ERROR');
        return ['ok' => false, 'error' => "Google Maps API error: {$status}"];
    }

    /**
     * Optimise the route for a list of jobs using the Directions API.
     *
     * Each job must have an 'address' key.
     * Returns the jobs reordered by the optimised waypoint order,
     * each with an appended 'estimated_travel_minutes' value.
     *
     * @param  array  $jobs  Array of job arrays, each containing at least ['address' => string]
     * @return array         Optimised job list
     */
    public function optimiseRoute(Integration $integration, array $jobs): array
    {
        if (count($jobs) < 2) {
            return $jobs;
        }

        $key      = $integration->getDecryptedApiKey() ?? '';
        $origin   = $jobs[0]['address'];
        $dest     = end($jobs)['address'];

        // Middle jobs are waypoints.
        $waypoints = collect($jobs)->slice(1, -1)->pluck('address')->implode('|');

        $params = [
            'origin'            => $origin,
            'destination'       => $dest,
            'key'               => $key,
            'optimize_waypoints' => 'true',
        ];
        if ($waypoints) {
            $params['waypoints'] = 'optimize:true|' . $waypoints;
        }

        $response = Http::get(self::DIRECTIONS_URL, $params);

        if (!$response->successful() || $response->json('status') !== 'OK') {
            return $jobs;
        }

        $route           = $response->json('routes.0');
        $waypointOrder   = $route['waypoint_order'] ?? [];
        $legs            = $route['legs']            ?? [];

        // Rebuild sorted job list: origin → optimised waypoints → destination.
        $middle = collect($jobs)->slice(1, -1)->values();
        $sorted = collect([$jobs[0]]);

        foreach ($waypointOrder as $i => $waypointIdx) {
            $job = $middle[$waypointIdx];
            $job['estimated_travel_minutes'] = isset($legs[$i])
                ? (int) round($legs[$i]['duration']['value'] / 60)
                : null;
            $sorted->push($job);
        }

        $lastJob = end($jobs);
        $lastLeg = end($legs);
        $lastJob['estimated_travel_minutes'] = $lastLeg
            ? (int) round($lastLeg['duration']['value'] / 60)
            : null;
        $sorted->push($lastJob);

        return $sorted->values()->all();
    }

    /**
     * Get the estimated driving time in minutes between two addresses.
     * Returns null if the API call fails.
     */
    public function getJobEta(Integration $integration, string $origin, string $destination): ?int
    {
        $response = Http::get(self::DIRECTIONS_URL, [
            'origin'      => $origin,
            'destination' => $destination,
            'key'         => $integration->getDecryptedApiKey() ?? '',
        ]);

        if ($response->successful() && $response->json('status') === 'OK') {
            $seconds = $response->json('routes.0.legs.0.duration.value');
            return $seconds !== null ? (int) round($seconds / 60) : null;
        }

        return null;
    }

    /**
     * Geocode an address string to latitude/longitude.
     * Returns ['lat' => float, 'lng' => float] or null.
     */
    public function geocodeAddress(Integration $integration, string $address): ?array
    {
        $response = Http::get(self::GEOCODE_URL, [
            'address' => $address,
            'key'     => $integration->getDecryptedApiKey() ?? '',
        ]);

        if ($response->successful() && $response->json('status') === 'OK') {
            $location = $response->json('results.0.geometry.location');
            if ($location) {
                return ['lat' => $location['lat'], 'lng' => $location['lng']];
            }
        }

        return null;
    }
}
