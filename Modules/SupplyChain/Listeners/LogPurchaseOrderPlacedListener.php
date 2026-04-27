<?php

namespace Modules\SupplyChain\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\SupplyChain\Events\PurchaseOrderPlaced;

class LogPurchaseOrderPlacedListener
{
    public function handle(PurchaseOrderPlaced $event): void
    {
        Log::info('SupplyChain purchase order placed', [
            'purchase_order_id' => $event->purchaseOrder->id,
            'supplier_id'       => $event->purchaseOrder->supplier_id,
            'company_id'        => $event->purchaseOrder->company_id,
            'total'             => $event->purchaseOrder->total,
            'reference'         => $event->purchaseOrder->reference,
        ]);
    }
}
