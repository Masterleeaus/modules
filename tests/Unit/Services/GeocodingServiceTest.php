<?php

use App\Services\GeocodingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class);

// ── geocode() — successful responses ─────────────────────────────────────────

test('geocode returns lat and lng on successful response', function () {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'status'  => 'OK',
            'results' => [
                [
                    'geometry' => [
                        'location' => ['lat' => -33.8688, 'lng' => 151.2093],
                    ],
                ],
            ],
        ], 200),
    ]);

    $service = new GeocodingService('fake-api-key');
    $result  = $service->geocode('123 Main St, Sydney NSW');

    expect($result)->not->toBeNull();
    expect($result[0])->toBe(-33.8688);
    expect($result[1])->toBe(151.2093);
});

test('geocode returns null when api key is empty', function () {
    $service = new GeocodingService('');
    $result  = $service->geocode('123 Main St');

    expect($result)->toBeNull();
});

// ── geocode() — error responses ───────────────────────────────────────────────

test('geocode returns null when the HTTP request fails', function () {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([], 500),
    ]);
    Log::shouldReceive('warning')->once();

    $service = new GeocodingService('fake-api-key');
    $result  = $service->geocode('Bad Address');

    expect($result)->toBeNull();
});

test('geocode returns null when the api status is not OK', function () {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'status'  => 'ZERO_RESULTS',
            'results' => [],
        ], 200),
    ]);
    Log::shouldReceive('warning')->once();

    $service = new GeocodingService('fake-api-key');
    $result  = $service->geocode('Nowhere Land');

    expect($result)->toBeNull();
});

test('geocode returns null when results array is empty', function () {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'status'  => 'OK',
            'results' => [],
        ], 200),
    ]);
    Log::shouldReceive('warning')->once();

    $service = new GeocodingService('fake-api-key');
    $result  = $service->geocode('Empty Result');

    expect($result)->toBeNull();
});

test('geocode returns null when api returns REQUEST_DENIED', function () {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'status'  => 'REQUEST_DENIED',
            'results' => [],
        ], 200),
    ]);
    Log::shouldReceive('warning')->once();

    $service = new GeocodingService('invalid-key');
    $result  = $service->geocode('Any Address');

    expect($result)->toBeNull();
});
