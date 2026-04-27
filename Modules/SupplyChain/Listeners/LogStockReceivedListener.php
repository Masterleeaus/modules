<?php

namespace Modules\SupplyChain\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\SupplyChain\Events\StockReceived;

class LogStockReceivedListener
{
    public function handle(StockReceived $event): void
    {
        $receipt = $event->goodsReceipt;

        Log::info('SupplyChain goods receipt created', [
            'goods_receipt_id'   => $receipt->id,
            'purchase_order_id'  => $receipt->purchase_order_id,
            'warehouse_id'       => $receipt->warehouse_id,
            'company_id'         => $receipt->company_id,
            'received_by'        => $receipt->received_by,
        ]);
    }
}
