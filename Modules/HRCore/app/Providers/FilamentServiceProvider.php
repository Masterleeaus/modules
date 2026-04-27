<?php

namespace Modules\HRCore\app\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\HRCore\Filament\Plugin\HRCorePlugin;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('hr-core.filament.plugin', fn () => HRCorePlugin::make());
    }

    public function boot(): void {}
}
