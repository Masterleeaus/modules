<?php

namespace Modules\CleanQuality\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\CleanQuality\Filament\Plugin\CleanQualityPlugin;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the plugin as a singleton so panels can resolve it by class.
        $this->app->singleton('clean-quality.filament.plugin', fn () => CleanQualityPlugin::make());
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'clean_quality');
    }
}

