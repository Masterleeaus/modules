<?php

namespace App\Http\Controllers\Technician;

use App\Events\TechnicianLocationUpdated;
use App\Http\Controllers\Controller;
use App\Models\DriverLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\GroundZero\Services\GeofenceService;

class LocationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude'    => ['required', 'numeric', 'between:-90,90'],
            'longitude'   => ['required', 'numeric', 'between:-180,180'],
            'heading'     => ['nullable', 'numeric', 'between:0,360'],
            'speed'       => ['nullable', 'numeric', 'min:0'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        $location = DriverLocation::create([
            'user_id'     => $request->user()->id,
            'latitude'    => $validated['latitude'],
            'longitude'   => $validated['longitude'],
            'heading'     => $validated['heading'] ?? null,
            'speed'       => $validated['speed'] ?? null,
            'recorded_at' => $validated['recorded_at'] ?? now(),
        ]);

        TechnicianLocationUpdated::dispatch($location);

        // Evaluate geofence boundaries for all active jobs assigned to this
        // technician. This fires TechnicianArrived / TechnicianLeft events
        // and auto-advances job status when the tech enters a job-site radius.
        if (app()->bound(GeofenceService::class)) {
            app(GeofenceService::class)->evaluate($location);
        }

        return response()->json(['data' => $location], 201);
    }
}
