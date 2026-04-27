<?php

namespace Modules\CleanQuality\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\CleanQuality\Console\Commands\AutoCreateRecurringSchedules;
use Modules\CleanQuality\Console\Commands\ActivateQualityControlModuleCommand;
use Modules\CleanQuality\Entities\Inspection;
use Modules\CleanQuality\Entities\QcRecord;
use Modules\CleanQuality\Listeners\JobCompletedListener;
use Modules\CleanQuality\Policies\InspectionPolicy;
use Modules\CleanQuality\Policies\QcRecordPolicy;

class CleanQualityServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'CleanQuality';

    public function register(): void
    {
        $this->registerConfig();
        $this->registerCommands();
    }

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerSidebarHints();
        $this->registerPolicies();
        $this->scheduleCommands();

        // Optional: auto-create Quality Checks when a job is completed.
        if (config('clean_quality.auto_create_on_job_complete', false)) {
            $listener = app(JobCompletedListener::class);

            // String events (loose coupling) — other modules can dispatch these.
            Event::listen('job.completed', [$listener, 'handle']);
            Event::listen('cleaning.job.completed', [$listener, 'handle']);
            Event::listen('jobs.completed', [$listener, 'handle']);
        }
    
        // Titan Zero + Titan Go integration (capabilities registry)
        if (class_exists(\Modules\TitanZero\Services\CapabilityRegistry::class)) {
            \Modules\TitanZero\Services\CapabilityRegistry::registerModuleFromConfig('CleanQuality');
        }
    }

    protected function registerConfig(): void
    {
        // module config.php (if present)
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('clean_quality.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'clean_quality');

        // integrations (Titan link-outs only)
        $this->publishes([
            __DIR__ . '/../Config/integrations.php' => config_path('clean_quality_integrations.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../Config/integrations.php', 'clean_quality_integrations');

        $this->publishes([
            __DIR__ . '/../Config/module_settings.php' => config_path('clean_quality_module_settings.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../Config/module_settings.php', 'clean_quality_module_settings');

        $this->publishes([
            __DIR__ . '/../Config/features.php' => config_path('clean_quality_features.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../Config/features.php', 'clean_quality_features');

        $this->publishes([
            __DIR__ . '/../Config/permissions.php' => config_path('clean_quality_permissions.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../Config/permissions.php', 'clean_quality_permissions');

        $this->publishes([
            __DIR__ . '/../Config/titanzero.php' => config_path('clean_quality_titanzero.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../Config/titanzero.php', 'clean_quality_titanzero');
    }

    protected function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . strtolower($this->moduleName));

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'quality_control');
            $this->loadTranslationsFrom($langPath, 'inspection');
            $this->loadTranslationsFrom($langPath, 'clean_quality');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'quality_control');
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'inspection');
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'clean_quality');
        }
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'quality_control');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'inspection');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'clean_quality');

        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/modules/' . strtolower($this->moduleName)),
        ], 'views');
    }

    protected function registerCommands(): void
    {
        $this->commands([
            AutoCreateRecurringSchedules::class,
            ActivateQualityControlModuleCommand::class,
        ]);
    }

    protected function scheduleCommands(): void
    {
        // Worksuite commonly defines app.cron_timezone; fall back to app.timezone.
        $timezone = config('app.cron_timezone') ?: config('app.timezone', 'UTC');

        /** @var Schedule $schedule */
        $schedule = $this->app->make(Schedule::class);

        $schedule->command('recurring-schedule-create')
            ->daily()
            ->timezone($timezone);
    }

protected function registerSidebarHints(): void
{
        View::share('qualityControlSidebarView', 'clean_quality::sections.sidebar');
        View::share('inspectionSidebarView', 'clean_quality::sections.sidebar');
        View::share('qualityControlModuleAlias', config('clean_quality_module_settings.module_alias', 'clean_quality'));
        View::share('inspectionModuleAlias', 'clean_quality');
}

    protected function registerPolicies(): void
    {
        Gate::policy(Inspection::class, InspectionPolicy::class);
        Gate::policy(QcRecord::class, QcRecordPolicy::class);
    }

}
