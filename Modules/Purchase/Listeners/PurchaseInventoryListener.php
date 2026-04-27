<?php

namespace Modules\Purchase\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Purchase\Entities\PurchaseInventory;
use Modules\Purchase\Events\PurchaseInventoryEvent;
use Modules\Purchase\Notifications\NewPurchaseInventory;

class PurchaseInventoryListener
{

    /**
     * Handle the event.
     *
     * @param PurchaseInventoryEvent $event
     * @return void
     */
    public function handle(PurchaseInventoryEvent $event)
    {
        $notifyUser = User::allAdmins($event->company->id);

        Notification::send($notifyUser, new NewPurchaseInventory($event->products, $event->purchaseInventory));

    }

}
