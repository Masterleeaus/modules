<?php

namespace Modules\ZeroPay\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ZeroPay\Filament\Plugin\ZeroPayPlugin;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('zero-pay.filament.plugin', fn () => ZeroPayPlugin::make());
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'zero_pay');
    }
}
