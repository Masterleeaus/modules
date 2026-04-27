<?php

namespace Modules\Purchase\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Purchase\Entities\PurchaseInventory;

class PurchaseInventoryEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $purchaseInventory;
    public $products;
    public $company;

    public function __construct($purchaseInventory, $products, $company)
    {
        $this->purchaseInventory = $purchaseInventory;
        $this->products = $products;
        $this->company = $company;
    }

}
