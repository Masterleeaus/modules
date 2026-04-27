<?php

namespace Modules\Asset\Providers;

use App\Events\NewCompanyCreatedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetHistory;
use Modules\Asset\Entities\AssetType;
use Modules\Asset\Listeners\CompanyCreatedListener;
use Modules\Asset\Observers\AssetHistoryObserver;
use Modules\Asset\Observers\AssetObserver;
use Modules\Asset\Observers\AssetTypeObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewCompanyCreatedEvent::class => [CompanyCreatedListener::class],
    ];

    protected $observers = [
        Asset::class => [AssetObserver::class],
        AssetHistory::class => [AssetHistoryObserver::class],
        AssetType::class => [AssetTypeObserver::class],
    ];
}
