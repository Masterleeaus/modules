<?php

namespace Modules\SupplyChain\Providers;

use App\Events\NewCompanyCreatedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\SupplyChain\Entities\Supplier;
use Modules\SupplyChain\Events\PurchaseOrderPlaced;
use Modules\SupplyChain\Events\StockLevelLow;
use Modules\SupplyChain\Events\StockReceived;
use Modules\SupplyChain\Events\SupplierRated;
use Modules\SupplyChain\Listeners\CompanyCreatedListener;
use Modules\SupplyChain\Listeners\LogPurchaseOrderPlacedListener;
use Modules\SupplyChain\Listeners\LogStockReceivedListener;
use Modules\SupplyChain\Listeners\QueueReorderAlertListener;
use Modules\SupplyChain\Listeners\UpdateSupplierRatingListener;
use Modules\SupplyChain\Observers\SupplierObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewCompanyCreatedEvent::class => [
            CompanyCreatedListener::class,
        ],
        StockLevelLow::class => [
            QueueReorderAlertListener::class,
        ],
        PurchaseOrderPlaced::class => [
            LogPurchaseOrderPlacedListener::class,
        ],
        StockReceived::class => [
            LogStockReceivedListener::class,
        ],
        SupplierRated::class => [
            UpdateSupplierRatingListener::class,
        ],
    ];

    protected $observers = [
        Supplier::class => [SupplierObserver::class],
    ];
}
