<?php

namespace Modules\Purchase\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Purchase\Events\VendorCreditPaymentMade as EventsVendorCreditPaymentMade;
use Modules\Purchase\Notifications\VendorCreditPaymentMade as NotificationsVendorCreditPaymentMade;

class VendorCreditPaymentMade
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(EventsVendorCreditPaymentMade $event)
    {
        $vendor = $event->creditNote->vendors;
        $vendor->notify(new NotificationsVendorCreditPaymentMade($event->amount, $event->remainingAmount));
    }
}
