# Boilerplate System

Status: Starter reference
Layer: Cross-layer boilerplate

## Platform Provider Boilerplate

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Platform\Core\PlatformManager;
use App\Platform\Core\PlatformRegistry;

class PlatformServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PlatformRegistry::class, function () {
            return new PlatformRegistry(
                basePath: base_path(),
                manifestPath: base_path('platform/platform_manifest.json'),
            );
        });

        $this->app->singleton(PlatformManager::class, function ($app) {
            return new PlatformManager(
                registry: $app->make(PlatformRegistry::class),
            );
        });
    }

    public function boot(): void
    {
        $this->app->make(PlatformManager::class)->boot();
    }
}
```

## Module Manifest Boilerplate

```json
{
  "name": "BookingManagement",
  "alias": "bookingmanagement",
  "description": "Booking domain module with engine and Filament surfaces.",
  "priority": 0,
  "providers": [
    "Modules\\BookingManagement\\Providers\\BookingManagementServiceProvider"
  ]
}
```

## Module Service Provider Boilerplate

```php
<?php

namespace Modules\BookingManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\BookingManagement\Services\BookingAvailabilityService;

class BookingManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'bookingmanagement');

        $this->app->singleton(BookingAvailabilityService::class, fn () => new BookingAvailabilityService());
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'bookingmanagement');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'bookingmanagement');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
```

## Action Boilerplate

```php
<?php

namespace Modules\BookingManagement\Actions;

use Modules\BookingManagement\Data\BookingData;
use Modules\BookingManagement\Models\Booking;
use Modules\BookingManagement\Events\BookingCreated;

class CreateBookingAction
{
    public function execute(int $companyId, BookingData $data): Booking
    {
        $booking = Booking::create([
            'company_id' => $companyId,
            'customer_id' => $data->customerId,
            'site_id' => $data->siteId,
            'scheduled_start' => $data->scheduledStart,
            'scheduled_end' => $data->scheduledEnd,
            'notes' => $data->notes,
            'status' => 'draft',
        ]);

        event(new BookingCreated($booking));

        return $booking;
    }
}
```

## Event + Listener + Job Boilerplate

```php
// Event
class BookingCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Booking $booking) {}
}

// Listener
class QueueBookingReminderListener
{
    public function handle(BookingCreated $event): void
    {
        SendBookingReminderJob::dispatch($event->booking->id);
    }
}

// Job
class SendBookingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $bookingId) {}

    public function handle(): void
    {
        // Resolve services and send reminder.
    }
}
```

## Filament Plugin Boilerplate

```php
<?php

namespace Modules\BookingManagement\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\BookingManagement\Filament\Resources\BookingResource;
use Modules\BookingManagement\Filament\Pages\DispatchBoardPage;

class BookingManagementPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'booking-management';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                BookingResource::class,
            ])
            ->pages([
                DispatchBoardPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
    }
}
```

## Platform Manifest Boilerplate

```json
{
  "platform": "TitanPlatform",
  "version": "1.0.0",
  "tenant_key": "company_id",
  "layers": [
    "core",
    "identity",
    "tenancy",
    "permissions",
    "navigation",
    "modules",
    "packages",
    "api",
    "communications",
    "automation",
    "workflows",
    "signals",
    "ai",
    "pwa",
    "cms",
    "omni",
    "sync",
    "audit",
    "observability"
  ]
}
```
