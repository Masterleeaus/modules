<?php

namespace Modules\Purchase\Providers;

use App\Models\Payment;
use App\Models\Currency;
use App\Events\NewCompanyCreatedEvent;
use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Entities\PurchaseItem;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Entities\PurchaseProduct;
use Modules\Purchase\Events\VendorCreditEvent;
use Modules\Purchase\Observers\PaymentObserver;
use Modules\Purchase\Entities\PurchaseInventory;
use Modules\Purchase\Entities\PurchaseOrderFile;
use Modules\Purchase\Observers\CurrencyObserver;
use Modules\Purchase\Entities\PurchaseVendorNote;
use Modules\Purchase\Events\NewPurchaseBillEvent;
use Modules\Purchase\Events\NewPurchaseOrderEvent;
use Modules\Purchase\Events\NewVendorPaymentEvent;
use Modules\Purchase\Entities\PurchaseVendorCredit;
use Modules\Purchase\Events\PurchaseInventoryEvent;
use Modules\Purchase\Entities\PurchaseInventoryFile;
use Modules\Purchase\Entities\PurchaseVendorContact;
use Modules\Purchase\Entities\PurchaseVendorPayment;
use Modules\Purchase\Listeners\VendorCreditListener;
use Modules\Purchase\Observers\PurchaseBillObserver;
use Modules\Purchase\Observers\PurchaseItemObserver;
use Modules\Purchase\Events\UpdateVendorPaymentEvent;
use Modules\Purchase\Observers\PurchaseOrderObserver;
use Modules\Purchase\Entities\PurchaseStockAdjustment;
use Modules\Purchase\Listeners\CompanyCreatedListener;
use Modules\Purchase\Observers\PurchaseVendorObserver;
use Modules\Purchase\Listeners\NewPurchaseBillListener;
use Modules\Purchase\Listeners\VendorCreditPaymentMade;
use Modules\Purchase\Events\VendorCreditPaymentMade as EventsVendorCreditPaymentMade;
use Modules\Purchase\Observers\PurchaseProductObserver;
use Modules\Purchase\Listeners\NewPurchaseOrderListener;
use Modules\Purchase\Listeners\NewVendorPaymentListener;
use Modules\Purchase\Listeners\PurchaseInventoryListener;
use Modules\Purchase\Observers\PurchaseInventoryObserver;
use Modules\Purchase\Observers\PurchaseOrderFileObserver;
use Modules\Purchase\Listeners\UpdateVendorPaymentListener;
use Modules\Purchase\Observers\PurchaseVendorNotesObserver;
use Modules\Purchase\Entities\PurchaseStockAdjustmentReason;
use Modules\Purchase\Observers\PurchaseVendorCreditObserver;
use Modules\Purchase\Observers\PurchaseInventoryFileObserver;
use Modules\Purchase\Observers\PurchaseVendorContactObserver;
use Modules\Purchase\Observers\PurchaseVendorPaymentObserver;
use Modules\Purchase\Observers\StockAdjustmentReasonObserver;
use Modules\Purchase\Observers\PurchaseStockAdjustmentObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        NewCompanyCreatedEvent::class => [CompanyCreatedListener::class],
        VendorCreditEvent::class => [VendorCreditListener::class],
        PurchaseInventoryEvent::class => [PurchaseInventoryListener::class],
        NewVendorPaymentEvent::class => [NewVendorPaymentListener::class],
        UpdateVendorPaymentEvent::class => [UpdateVendorPaymentListener::class],
        NewPurchaseBillEvent::class => [NewPurchaseBillListener::class],
        NewPurchaseOrderEvent::class => [NewPurchaseOrderListener::class],
        EventsVendorCreditPaymentMade::class => [VendorCreditPaymentMade::class],

    ];

    protected $observers = [
        PurchaseVendor::class => [PurchaseVendorObserver::class],
        PurchaseVendorPayment::class => [PurchaseVendorPaymentObserver::class],
        PurchaseProduct::class => [PurchaseProductObserver::class],
        PurchaseVendorContact::class => [PurchaseVendorContactObserver::class],
        PurchaseVendorNote::class => [PurchaseVendorNotesObserver::class],
        PurchaseVendorCredit::class => [PurchaseVendorCreditObserver::class],
        PurchaseStockAdjustmentReason::class => [StockAdjustmentReasonObserver::class],
        PurchaseStockAdjustment::class => [PurchaseStockAdjustmentObserver::class],
        PurchaseInventory::class => [PurchaseInventoryObserver::class],
        PurchaseInventoryFile::class => [PurchaseInventoryFileObserver::class],
        PurchaseOrder::class => [PurchaseOrderObserver::class],
        PurchaseItem::class => [PurchaseItemObserver::class],
        PurchaseOrderFile::class => [PurchaseOrderFileObserver::class],
        PurchaseBill::class => [PurchaseBillObserver::class],
        Payment::class => [PaymentObserver::class],
        Currency::class => [CurrencyObserver::class],
    ];

}
