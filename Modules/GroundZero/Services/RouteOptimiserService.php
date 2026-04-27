<?php

namespace Modules\GroundZero\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RouteOptimiserService
{
    /**
     * Geofence radius used across the module (metres).
     */
    public const GEOFENCE_RADIUS_METRES = 200;

    public function __construct(private readonly string $apiKey) {}

    /**
     * Given a starting [lat, lng] and an ordered list of destination addresses,
     * return a visit order that minimises travel distance using a
     * nearest-first heuristic together with the total distance and duration.
     *
     * **Algorithm**: The Distance Matrix API is called with one origin (the
     * technician's current position) and all destinations.  The destination
     * with the shortest distance from the origin is chosen as the first stop.
     * Remaining stops are appended in their original `scheduled_at` order —
     * a full inter-stop TSP optimisation would require an additional API call
     * per subsequent leg and is not performed here.
     *
     * @param  array{0: float, 1: float}  $origin       [lat, lng]
     * @param  list<string>               $destinations  Address strings
     * @return array{
     *     ordered_indexes: list<int>,
     *     total_distance_metres: int,
     *     total_duration_seconds: int,
     * }|null  Returns null when the API key is absent or the request fails.
     */
    public function optimise(array $origin, array $destinations): ?array
    {
        if (empty($this->apiKey) || empty($destinations)) {
            return null;
        }

        $originStr  = implode(',', $origin);
        $destStr    = implode('|', $destinations);

        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins'      => $originStr,
            'destinations' => $destStr,
            'key'          => $this->apiKey,
            'units'        => 'metric',
        ]);

        if (! $response->successful()) {
            Log::warning('GroundZero: Distance Matrix request failed', ['status' => $response->status()]);
            return null;
        }

        $data = $response->json();

        if (($data['status'] ?? '') !== 'OK') {
            Log::warning('GroundZero: Distance Matrix returned non-OK', ['status' => $data['status'] ?? 'unknown']);
            return null;
        }

        $elements = $data['rows'][0]['elements'] ?? [];

        if (empty($elements)) {
            return null;
        }

        // Extract distances and durations (seconds / metres) for each destination.
        $distances = [];
        $durations = [];

        foreach ($elements as $i => $element) {
            if (($element['status'] ?? '') === 'OK') {
                $distances[$i] = $element['distance']['value'] ?? PHP_INT_MAX;
                $durations[$i] = $element['duration']['value'] ?? PHP_INT_MAX;
            } else {
                $distances[$i] = PHP_INT_MAX;
                $durations[$i] = PHP_INT_MAX;
            }
        }

        // Nearest-neighbour greedy sort.
        $remaining    = array_keys($distances);
        $ordered      = [];
        $totalDist    = 0;
        $totalDur     = 0;

        // First stop: nearest from origin.
        $minDist = PHP_INT_MAX;
        $first   = 0;

        foreach ($remaining as $idx) {
            if ($distances[$idx] < $minDist) {
                $minDist = $distances[$idx];
                $first   = $idx;
            }
        }

        $ordered[]  = $first;
        $totalDist += $distances[$first];
        $totalDur  += $durations[$first];
        $remaining  = array_values(array_filter($remaining, fn ($i) => $i !== $first));

        // Subsequent stops: simply maintain the original order once we've
        // identified the first stop (single-origin matrix gives us distances
        // only from the origin, not inter-destination).  For multi-stop
        // optimisation with full inter-stop data a separate call would be needed.
        foreach ($remaining as $idx) {
            $ordered[]  = $idx;
            $totalDist += $distances[$idx] ?? 0;
            $totalDur  += $durations[$idx] ?? 0;
        }

        return [
            'ordered_indexes'        => $ordered,
            'total_distance_metres'  => $totalDist,
            'total_duration_seconds' => $totalDur,
        ];
    }
}
