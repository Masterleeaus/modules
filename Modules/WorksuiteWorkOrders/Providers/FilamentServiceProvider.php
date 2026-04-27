<?php

namespace Modules\WorksuiteWorkOrders\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\WorksuiteWorkOrders\Filament\Plugin\WorksuitePlugin;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('worksuite-work-orders.filament.plugin', fn () => WorksuitePlugin::make());
    }

    public function boot(): void {}
}
