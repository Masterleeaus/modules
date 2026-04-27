<?php

namespace Modules\TitanIntegrations\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\TitanIntegrations\Services\OAuthService;
use Modules\TitanIntegrations\Services\WebhookDispatcher;
use Modules\TitanIntegrations\Services\Integrations\GoogleCalendarIntegration;
use Modules\TitanIntegrations\Services\Integrations\XeroIntegration;
use Modules\TitanIntegrations\Services\Integrations\HubSpotIntegration;
use Modules\TitanIntegrations\Services\Integrations\MailchimpIntegration;
use Modules\TitanIntegrations\Services\Integrations\SlackIntegration;

class TitanIntegrationsServiceProvider extends ServiceProvider
{
    protected string $moduleName      = 'TitanIntegrations';
    protected string $moduleNameLower = 'titanintegrations';

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerRoutes();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(module_path($this->moduleName, 'Config/config.php'), 'titanintegrations');

        $this->app->singleton(OAuthService::class);
        $this->app->singleton(WebhookDispatcher::class);

        $this->app->singleton(GoogleCalendarIntegration::class, fn($app) =>
            new GoogleCalendarIntegration($app->make(OAuthService::class))
        );
        $this->app->singleton(XeroIntegration::class, fn($app) =>
            new XeroIntegration($app->make(OAuthService::class))
        );
        $this->app->singleton(HubSpotIntegration::class);
        $this->app->singleton(MailchimpIntegration::class);
        $this->app->singleton(SlackIntegration::class);
    }

    protected function registerRoutes(): void
    {
        $web = module_path($this->moduleName, 'Routes/web.php');
        if (file_exists($web)) {
            Route::middleware('web')->group($web);
        }

        $api = module_path($this->moduleName, 'Routes/api.php');
        if (file_exists($api)) {
            Route::middleware('api')->prefix('api')->group($api);
        }
    }

    public function registerViews(): void
    {
        $sourcePath = module_path($this->moduleName, 'Resources/views');
        if (is_dir($sourcePath)) {
            $this->loadViewsFrom($sourcePath, $this->moduleNameLower);
        }
    }

    public function registerTranslations(): void
    {
        $langPath = resource_path("lang/modules/{$this->moduleNameLower}");
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $moduleLang = module_path($this->moduleName, 'Resources/lang');
            if (is_dir($moduleLang)) {
                $this->loadTranslationsFrom($moduleLang, $this->moduleNameLower);
            }
        }
    }
}
