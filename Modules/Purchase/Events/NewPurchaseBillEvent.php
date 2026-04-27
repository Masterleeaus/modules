<?php

namespace Modules\Purchase\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Purchase\Entities\PurchaseBill;

class NewPurchaseBillEvent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

     public $purchaseBill;

    public function __construct(PurchaseBill $purchaseBill)
    {
        $this->purchaseBill = $purchaseBill;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
    
}
