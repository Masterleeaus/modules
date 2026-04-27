<?php

namespace Modules\GroundZero\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\GroundZero\Services\DispatchBroadcaster;
use Modules\GroundZero\Services\DispatchService;
use Modules\GroundZero\Services\ETACalculatorService;
use Modules\GroundZero\Services\GeofenceService;
use Modules\GroundZero\Services\RouteOptimiserService;

class GroundZeroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ground_zero');

        // Titan Zero capabilities registry integration.
        if (class_exists(\Modules\TitanZero\Services\CapabilityRegistry::class)) {
            \Modules\TitanZero\Services\CapabilityRegistry::registerModuleFromConfig('GroundZero');
        }
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(FilamentServiceProvider::class);

        $this->app->singleton(RouteOptimiserService::class, function () {
            return new RouteOptimiserService(
                config('services.google_maps.key', env('GOOGLE_MAPS_API_KEY', '')),
            );
        });

        $this->app->singleton(ETACalculatorService::class, function () {
            return new ETACalculatorService(
                config('services.google_maps.key', env('GOOGLE_MAPS_API_KEY', '')),
            );
        });

        $this->app->singleton(GeofenceService::class);
        $this->app->singleton(DispatchService::class);
        $this->app->singleton(DispatchBroadcaster::class);
    }
}
