<?php

namespace Modules\Purchase\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Modules\Purchase\Entities\PurchaseVendorPayment;

class NewVendorPaymentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $payment;

    public function __construct(PurchaseVendorPayment $payment)
    {
        $this->payment = $payment;
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
