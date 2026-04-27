<?php

namespace App\Providers;

use App\Events\EstimateSent;
use App\Events\InvoiceSent;
use App\Events\JobCreated;
use App\Events\JobStatusChanged;
use App\Listeners\HandleJobCreatedSendEmailConfirmation;
use App\Listeners\HandleJobCreatedSendSmsConfirmation;
use App\Listeners\HandleJobStatusChangedSendNotifications;
use App\Listeners\SendEstimateNotification;
use App\Services\GeocodingService;
use App\Services\MessageDispatcher;
use App\Services\SmsService;
use App\Services\TemplateRenderer;
use App\Services\TwilioSmsService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsService::class, TwilioSmsService::class);

        $this->app->singleton(GeocodingService::class, fn () =>
            new GeocodingService(config('services.google.maps_api_key', ''))
        );

        $this->app->singleton(MessageDispatcher::class, fn ($app) =>
            new MessageDispatcher($app->make(SmsService::class))
        );

        $this->app->singleton(TemplateRenderer::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Event::listen(EstimateSent::class, SendEstimateNotification::class);
        Event::listen(InvoiceSent::class, SendInvoiceNotification::class);
        Event::listen(JobCreated::class, HandleJobCreatedSendEmailConfirmation::class);
        Event::listen(JobCreated::class, HandleJobCreatedSendSmsConfirmation::class);
        Event::listen(JobStatusChanged::class, HandleJobStatusChangedSendNotifications::class);

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
}
