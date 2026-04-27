<?php

namespace Modules\TitanVault\Providers;

use Illuminate\Support\ServiceProvider;

class TitanVaultServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'TitanVault';

    public function register(): void
    {
        $this->registerConfig();
    }

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('titan_vault.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'titan_vault');
    }

    protected function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . strtolower($this->moduleName));

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'titan_vault');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'titan_vault');
        }
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'titan_vault');

        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/modules/' . strtolower($this->moduleName)),
        ], 'views');
    }
}
