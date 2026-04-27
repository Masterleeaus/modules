<?php

namespace Modules\ProShots\Providers;

use Illuminate\Support\ServiceProvider;

class ProShotsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'ProShots';

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
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'proshots');
    }

    protected function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . strtolower($this->moduleName));

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'proshots');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'proshots');
        }
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'proshots');
    }
}
