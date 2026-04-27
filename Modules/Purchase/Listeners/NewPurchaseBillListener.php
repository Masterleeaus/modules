<?php

namespace Modules\Purchase\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Purchase\Events\NewPurchaseBillEvent;
use Modules\Purchase\Notifications\NewPurchaseBill;

class NewPurchaseBillListener
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
     * @param NewPurchaseBillEvent $event
     * @return void
     */
    public function handle(NewPurchaseBillEvent $event)
    {
        if($event->purchaseBill->vendor->email != null)
        {
            Notification::send($event->purchaseBill->vendor, new NewPurchaseBill($event->purchaseBill));
        }
    }

}
