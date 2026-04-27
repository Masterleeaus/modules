<?php

namespace Modules\Purchase\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\Purchase\Notifications\VendorPayment;
use Modules\Purchase\Events\NewVendorPaymentEvent;
use Modules\Purchase\Notifications\AdminNewVendorPayment;

class NewVendorPaymentListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NewVendorPaymentEvent $event)
    {
        Notification::send(User::allAdmins($event->payment->vendor->company->id), new AdminNewVendorPayment($event->payment));

        if ($event->payment->vendor->email != null && $event->payment->notify_vendor != 0) {
            Notification::send($event->payment->vendor, new VendorPayment($event->payment));
        }
    }

}
