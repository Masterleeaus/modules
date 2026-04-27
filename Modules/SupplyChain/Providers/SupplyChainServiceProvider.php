<?php

namespace Modules\SupplyChain\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\SupplyChain\Console\Commands\CheckStockLevelsCommand;

class SupplyChainServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'SupplyChain';

    public function register(): void
    {
        $this->mergeConfigFrom(module_path($this->moduleName, 'Config/config.php'), 'supplychain');
        $this->mergeConfigFrom(module_path($this->moduleName, 'Config/features.php'), 'supplychain.features');
        $this->mergeConfigFrom(module_path($this->moduleName, 'Config/permissions.php'), 'supplychain.permissions');

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(FilamentServiceProvider::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->loadViewsFrom(module_path($this->moduleName, 'Resources/views'), 'supplychain');
        $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), 'supplychain');

        Blade::anonymousComponentPath(module_path($this->moduleName, 'Resources/views/components'), 'supplychain');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckStockLevelsCommand::class,
            ]);
        }
    }
}
