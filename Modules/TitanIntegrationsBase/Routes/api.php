<?php

use Illuminate\Support\Facades\Route;
use Modules\TitanIntegrations\Http\Controllers\Api\V1\BookingApiController;

/*
|--------------------------------------------------------------------------
| TitanIntegrations REST API — external systems call WorkSuite
|--------------------------------------------------------------------------
| Auth: Bearer token (ApiToken model)
| Rate limit: 60 requests/minute per token
| Prefix: /api/v1
*/

Route::prefix('v1')
    ->middleware(['throttle:60,1', \Modules\TitanIntegrations\Http\Middleware\ApiTokenMiddleware::class])
    ->group(function () {

        // Bookings (tasks with task_type='booking')
        Route::apiResource('bookings', BookingApiController::class);

        // Health check (no auth)
        Route::get('ping', fn() => response()->json(['ok' => true, 'service' => 'WorkSuite API v1']))
            ->withoutMiddleware(\Modules\TitanIntegrations\Http\Middleware\ApiTokenMiddleware::class);
    });
