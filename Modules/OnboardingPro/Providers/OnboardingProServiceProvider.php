<?php
namespace Modules\OnboardingPro\Providers;

use Illuminate\Support\ServiceProvider;

class OnboardingProServiceProvider extends ServiceProvider
{
    protected $moduleName      = 'OnboardingPro';
    protected $moduleNameLower = 'onboardingpro';

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/onboarding-pro.php') => config_path('onboarding-pro.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/onboarding-pro.php'), 'onboarding-pro'
        );
    }

    public function registerViews(): void
    {
        $this->loadViewsFrom(module_path($this->moduleName, 'Resources/views'), $this->moduleNameLower);
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
    }
}
