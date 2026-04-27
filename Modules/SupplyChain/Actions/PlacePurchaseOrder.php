<?php

namespace Modules\SupplyChain\Actions;

use Modules\SupplyChain\Entities\PurchaseOrder;
use Modules\SupplyChain\Entities\PurchaseOrderItem;
use Modules\SupplyChain\Events\PurchaseOrderPlaced;

class PlacePurchaseOrder
{
    public function execute(array $payload): PurchaseOrder
    {
        $order = PurchaseOrder::create([
            'company_id' => company()->id ?? null,
            'supplier_id' => $payload['supplier_id'],
            'ordered_by' => auth()->id(),
            'status' => $payload['status'] ?? 'ordered',
            'ordered_at' => now(),
            'expected_date' => $payload['expected_date'] ?? null,
            'reference' => $payload['reference'] ?? null,
            'currency' => $payload['currency'] ?? 'AUD',
            'notes' => $payload['notes'] ?? null,
            'total' => 0,
        ]);

        $total = 0;

        foreach ($payload['items'] as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'item_id' => $item['item_id'],
                'qty_ordered' => $item['qty_ordered'],
                'unit_cost' => $item['unit_cost'],
            ]);

            $total += ((float) $item['qty_ordered']) * ((float) $item['unit_cost']);
        }

        $order->update(['total' => $total]);

        event(new PurchaseOrderPlaced($order));

        return $order->fresh();
    }
}
