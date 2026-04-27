<?php

namespace Modules\TitanSolo\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\TitanSolo\Services\SoloModeService;

class TitanSoloServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SoloModeService::class);
    }

    public function boot(): void
    {
        //
    }
}
