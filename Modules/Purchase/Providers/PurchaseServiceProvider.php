<?php

namespace Modules\Purchase\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Purchase\Console\ActivateModuleCommand;
use Modules\Purchase\Providers\RouteServiceProvider;

class PurchaseServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerCommands();
    
        // Titan Zero + Titan Go integration (capabilities registry)
        if (class_exists(\Modules\TitanZero\Services\CapabilityRegistry::class)) {
            \Modules\TitanZero\Services\CapabilityRegistry::registerModuleFromConfig('Purchase');
        }
}

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        // Bind PurchaseReorderService as a singleton so it can be resolved
        // from the container across the application.
        $this->app->singleton(
            \Modules\Purchase\Services\PurchaseReorderService::class
        );
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('purchase.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'purchase'
        );

        $xssConfigPath = module_path('Purchase', 'Config/xss_ignore.php');

        if (is_file($xssConfigPath)) {
            $this->mergeConfigFrom($xssConfigPath, 'purchase::xss_ignore');
        }
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/purchase');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom([$sourcePath], 'purchase');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/purchase');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'purchase');

        }
        else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'purchase');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /**
     * Register artisan commands
     */
    private function registerCommands()
    {
        $this->commands(
            [
                ActivateModuleCommand::class,
            ]
        );
    }

}
