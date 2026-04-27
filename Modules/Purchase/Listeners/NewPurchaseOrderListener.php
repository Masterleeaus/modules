<?php

namespace Modules\Purchase\Listeners;

use Modules\Purchase\Events\NewPurchaseOrderEvent;
use Modules\Purchase\Notifications\NewPurchaseOrder;
use Illuminate\Support\Facades\Notification;
use Modules\Purchase\Events\NewPurchaseOrderEvent as EventsNewPurchaseOrderEvent;

class NewPurchaseOrderListener
{

    /**
     * Handle the event.
     *
     * @param NewPurchaseOrderEvent $event
     * @return void
     */

    public function handle(EventsNewPurchaseOrderEvent $event)
    {
        if ($event->notifyUser->email != null) {
            Notification::send($event->notifyUser, new NewPurchaseOrder($event->order));
        }

    }

}
