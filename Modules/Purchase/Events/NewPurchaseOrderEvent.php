<?php

namespace Modules\Purchase\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Purchase\Entities\PurchaseOrder;

class NewPurchaseOrderEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $notifyUser;

    public function __construct(PurchaseOrder $order, $notifyUser)
    {
        $this->order = $order;
        $this->notifyUser = $notifyUser;
    }

}
