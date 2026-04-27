<?php

namespace Modules\WorksuiteWorkOrders\Providers;

use Illuminate\Support\ServiceProvider;

class WorkOrdersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'workorders');
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\WorksuiteWorkOrders\Console\WorkOrdersImportCsvCommand::class,
                \Modules\WorksuiteWorkOrders\Console\WorkOrdersExportCsvCommand::class,
                \Modules\WorksuiteWorkOrders\Console\WorkOrdersUninstallCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        if (file_exists(__DIR__.'/../Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        }
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'workorders');
        \Illuminate\Support\Facades\Blade::componentNamespace('Modules\\WorksuiteWorkOrders\\Resources\\views\\components', 'workorders');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'workorders');
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('workorders.php'),
        ], 'workorders-config');
    }
}

// TODO: Register policies via Gate::policy(WorkOrder::class, WorkOrderPolicy::class) when enforcing permissions.

