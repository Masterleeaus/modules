<?php

namespace Modules\GroundZero\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ETACalculatorService
{
    /**
     * ETA warning threshold: SMS is sent to customers when tech is this many
     * seconds away from the job site.
     */
    public const ETA_SMS_THRESHOLD_SECONDS = 900; // 15 minutes

    public function __construct(private readonly string $apiKey) {}

    /**
     * Calculate the driving duration in seconds from a technician's current
     * GPS position to a destination address.
     *
     * Uses the Google Maps Directions API with `departure_time=now` to include
     * live traffic data.
     *
     * @param  array{0: float, 1: float}  $origin       [lat, lng]
     * @param  string                     $destination  Address or "lat,lng"
     * @return int|null  Duration in seconds, or null on failure.
     */
    public function calculateEtaSeconds(array $origin, string $destination): ?int
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
            'origin'         => implode(',', $origin),
            'destination'    => $destination,
            'departure_time' => 'now',
            'key'            => $this->apiKey,
        ]);

        if (! $response->successful()) {
            Log::warning('GroundZero: Directions API request failed', ['status' => $response->status()]);
            return null;
        }

        $data = $response->json();

        if (($data['status'] ?? '') !== 'OK') {
            Log::warning('GroundZero: Directions API returned non-OK', ['status' => $data['status'] ?? 'unknown']);
            return null;
        }

        // duration_in_traffic is preferred (live data); fall back to duration.
        $leg = $data['routes'][0]['legs'][0] ?? null;

        if (! $leg) {
            return null;
        }

        return ($leg['duration_in_traffic']['value'] ?? $leg['duration']['value'] ?? null);
    }

    /**
     * Return whether the technician is within the SMS-notification window.
     */
    public function isWithinSmsWindow(array $origin, string $destination): bool
    {
        $etaSeconds = $this->calculateEtaSeconds($origin, $destination);

        if ($etaSeconds === null) {
            return false;
        }

        return $etaSeconds <= self::ETA_SMS_THRESHOLD_SECONDS;
    }
}
