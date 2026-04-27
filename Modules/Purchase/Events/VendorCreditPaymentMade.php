<?php

namespace Modules\Purchase\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Purchase\Entities\PurchasePaymentBill;
use Modules\Purchase\Entities\PurchaseVendorCredit;

class VendorCreditPaymentMade
{
    use SerializesModels;
    public $creditNote;
    public $amount;
    public $remainingAmount;

    /**
     * Create a new event instance.
     */
    public function __construct(PurchaseVendorCredit $creditNote, $amount, $remainingAmount)
    {
        $this->creditNote = $creditNote;
        $this->amount = $amount;
        $this->remainingAmount = $remainingAmount;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
