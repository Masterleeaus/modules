<?php

namespace Modules\Purchase\Listeners;

use App\Models\User;
use Modules\Purchase\Events\UpdateVendorPaymentEvent;
use Modules\Purchase\Notifications\AdminUpdateVendorPayment;
use Notification;

class UpdateVendorPaymentListener
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
    public function handle(UpdateVendorPaymentEvent $event)
    {
        Notification::send(User::allAdmins($event->payment->vendor->company->id), new AdminUpdateVendorPayment($event->payment));
    }

}
