<?php

namespace Modules\GroundZero\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\GroundZero\Filament\Plugin\GroundZeroPlugin;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('ground-zero.filament.plugin', fn () => GroundZeroPlugin::make());
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../../resources/views',
            'ground_zero',
        );
    }
}
