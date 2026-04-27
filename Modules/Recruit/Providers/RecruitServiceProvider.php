<?php

namespace Modules\Recruit\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Modules\Recruit\Console\ActivateModuleCommand;
use Modules\Recruit\Console\CandidateFollowupReminderCommand;
use Modules\Recruit\Console\JobEndDateCheck;
use Modules\Recruit\Console\OfferLetterReminder;
use Modules\Recruit\Console\SendInterviewReminder;

class RecruitServiceProvider extends ServiceProvider
{

    /**
     * @var string
     */
    protected $moduleName = 'Recruit';

    /**
     * @var string
     */
    protected $moduleNameLower = 'recruit';

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
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerCommands();
        $this->app->booted(function () {
            $this->scheduleCommands();
        });
    
        // Titan Zero + Titan Go integration (capabilities registry)
        if (class_exists(\Modules\TitanZero\Services\CapabilityRegistry::class)) {
            \Modules\TitanZero\Services\CapabilityRegistry::registerModuleFromConfig('Recruit');
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
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/xss_ignore.php'),
            $this->moduleNameLower . '::xss_ignore'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        }
        else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];

        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }

        return $paths;
    }

    /**
     * Register artisan commands
     */
    private function registerCommands()
    {
        $this->commands(
            [
                SendInterviewReminder::class,
                JobEndDateCheck::class,
                OfferLetterReminder::class,
                ActivateModuleCommand::class,
                CandidateFollowupReminderCommand::class,
            ]
        );
    }

    public function scheduleCommands()
    {
        // Do not run for development and demo
        if (app()->environment(['development', 'demo'])) {
            return true;
        }

        $schedule = $this->app->make(Schedule::class);

        $schedule->command('send-interview-reminder')->everyMinute();
        $schedule->command('candidate-followup-reminder')->everyMinute();

        $schedule->command('job-end-date-check')->daily();
        $schedule->command('offer-letter-reminder')->daily();
    }

}
