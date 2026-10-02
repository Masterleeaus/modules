<?php

namespace App\Providers;

use App\Console\Commands\Modules\InstallModuleCommand;
use App\Console\Commands\Modules\ModuleStatusCommand;
use App\Console\Commands\Modules\UpgradeModuleCommand;
use App\Console\Commands\Modules\VerifyModuleCommand;
use App\Events\EstimateSent;
use App\Events\InvoiceSent;
use App\Events\JobCreated;
use App\Events\JobStatusChanged;
use App\Listeners\HandleJobCreatedSendEmailConfirmation;
use App\Listeners\HandleJobCreatedSendSmsConfirmation;
use App\Listeners\HandleJobStatusChangedSendNotifications;
use App\Listeners\SendEstimateNotification;
use App\Listeners\SendInvoiceNotification;
use App\Modules\ModuleInstaller;
use App\Services\GeocodingService;
use App\Services\MessageDispatcher;
use App\Services\SmsService;
use App\Services\TemplateRenderer;
use App\Services\TwilioSmsService;
use App\Signals\SignalDispatcher;
use App\Signals\SignalGovernor;
use App\Signals\SignalRegistry;
use App\Signals\SignalRouter;
use App\Signals\SignalValidator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleInstaller::class);

        $this->app->bind(SmsService::class, TwilioSmsService::class);

        $this->app->singleton(GeocodingService::class, fn () =>
            new GeocodingService(config('services.google.maps_api_key', ''))
        );

        $this->app->singleton(MessageDispatcher::class, fn ($app) =>
            new MessageDispatcher($app->make(SmsService::class))
        );

        $this->app->singleton(TemplateRenderer::class);

        // Signal Engine singletons
        $this->app->singleton(SignalRegistry::class);
        $this->app->singleton(SignalValidator::class);
        $this->app->singleton(SignalGovernor::class);
        $this->app->singleton(SignalDispatcher::class);
        $this->app->singleton(SignalRouter::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallModuleCommand::class,
                UpgradeModuleCommand::class,
                VerifyModuleCommand::class,
                ModuleStatusCommand::class,
            ]);
        }

        Event::listen(EstimateSent::class, SendEstimateNotification::class);
        Event::listen(InvoiceSent::class, SendInvoiceNotification::class);

        // Listeners for core job events (dispatched directly by Action classes;
        // the Signal Engine records these as signals alongside the direct dispatch).
        Event::listen(JobCreated::class, HandleJobCreatedSendEmailConfirmation::class);
        Event::listen(JobCreated::class, HandleJobCreatedSendSmsConfirmation::class);
        Event::listen(JobStatusChanged::class, HandleJobStatusChangedSendNotifications::class);

        // Register Signal Engine dispatch handlers for external / custom signals.
        $this->registerSignalHandlers();

        if (class_exists(\TomatoPHP\FilamentCms\Facades\FilamentCMS::class)
            && class_exists(\TomatoPHP\FilamentCms\Services\Contracts\CmsType::class)) {
            \TomatoPHP\FilamentCms\Facades\FilamentCMS::types()->register([
                \TomatoPHP\FilamentCms\Services\Contracts\CmsType::make('page')
                    ->label('Pages')
                    ->icon('heroicon-o-document-text')
                    ->color('primary'),
                \TomatoPHP\FilamentCms\Services\Contracts\CmsType::make('landing')
                    ->label('Landing Pages')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success'),
                \TomatoPHP\FilamentCms\Services\Contracts\CmsType::make('block')
                    ->label('Blocks')
                    ->icon('heroicon-o-square-3-stack-3d')
                    ->color('warning'),
            ]);
        }
    }

    private function registerSignalHandlers(): void
    {
        // Internal events (job.created, job.status_changed, payment.received) are dispatched
        // directly by their Action classes — no Signal Engine handler needed for them.
        // Register handlers here for any external or custom signals that require the Signal
        // Engine to drive the dispatch (e.g. webhook signals, AI inference results, etc.).
    }
}
