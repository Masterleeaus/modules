<?php

namespace Modules\SupplyChain\Services;

use Illuminate\Support\Facades\DB;
use Modules\SupplyChain\Entities\GoodsReceipt;
use Modules\SupplyChain\Entities\GoodsReceiptItem;
use Modules\SupplyChain\Entities\Movement;
use Modules\SupplyChain\Entities\PurchaseOrder;
use Modules\SupplyChain\Entities\PurchaseOrderItem;
use Modules\SupplyChain\Entities\StockLevel;

class InventoryService
{
    public function receiveStock(PurchaseOrder $purchaseOrder, int $warehouseId, array $items): GoodsReceipt
    {
        return DB::transaction(function () use ($purchaseOrder, $warehouseId, $items) {
            $receipt = GoodsReceipt::create([
                'company_id' => $purchaseOrder->company_id,
                'purchase_order_id' => $purchaseOrder->id,
                'warehouse_id' => $warehouseId,
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);

            foreach ($items as $item) {
                $orderItem = PurchaseOrderItem::findOrFail($item['po_item_id']);

                GoodsReceiptItem::create([
                    'goods_receipt_id' => $receipt->id,
                    'purchase_order_item_id' => $orderItem->id,
                    'item_id' => $orderItem->item_id,
                    'qty_received' => $item['qty_received'],
                    'unit_cost' => $item['unit_cost'] ?? $orderItem->unit_cost,
                ]);

                $this->adjustStock(
                    companyId: (int) ($purchaseOrder->company_id ?? 0),
                    itemId: (int) $orderItem->item_id,
                    warehouseId: $warehouseId,
                    quantityDelta: (float) $item['qty_received'],
                    type: 'in',
                    note: 'GRN #' . $receipt->id,
                    reference: 'PO-' . $purchaseOrder->id,
                );
            }

            $purchaseOrder->update(['status' => 'received']);

            return $receipt;
        });
    }

    public function transferStock(int $companyId, int $itemId, int $fromWarehouseId, int $toWarehouseId, float $quantity, ?string $note = null): void
    {
        DB::transaction(function () use ($companyId, $itemId, $fromWarehouseId, $toWarehouseId, $quantity, $note) {
            $this->adjustStock($companyId, $itemId, $fromWarehouseId, -$quantity, 'out', $note, 'transfer-out');
            $this->adjustStock($companyId, $itemId, $toWarehouseId, $quantity, 'in', $note, 'transfer-in');
        });
    }

    public function adjustStock(int $companyId, int $itemId, int $warehouseId, float $quantityDelta, string $type, ?string $note = null, ?string $reference = null): StockLevel
    {
        $stock = StockLevel::firstOrCreate(
            ['item_id' => $itemId, 'warehouse_id' => $warehouseId],
            ['company_id' => $companyId, 'on_hand' => 0, 'qty_reserved' => 0, 'qty_available' => 0, 'min_qty' => 0, 'max_qty' => 0],
        );

        $stock->on_hand = max(0, $stock->on_hand + $quantityDelta);
        $stock->qty_available = max(0, $stock->on_hand - $stock->qty_reserved);
        $stock->save();

        Movement::create([
            'company_id' => $companyId,
            'user_id' => auth()->id(),
            'item_id' => $itemId,
            'warehouse_id' => $warehouseId,
            'quantity' => abs($quantityDelta),
            'type' => $type,
            'note' => $note,
            'reference' => $reference,
        ]);

        return $stock;
    }
}
