<?php

namespace Modules\SupplyChain\Services;

use Modules\SupplyChain\Entities\StockLevel;
use Modules\SupplyChain\Jobs\GeneratePurchaseOrderJob;
use Modules\SupplyChain\Support\DTOs\ReorderSuggestionData;

class ReorderService
{
    public function lowStockLevels(?int $companyId = null)
    {
        $query = StockLevel::query()->with(['item', 'warehouse'])->whereColumn('qty_available', '<=', 'min_qty')->where('min_qty', '>', 0);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get();
    }

    public function recommendOrderQuantity(StockLevel $stockLevel): float
    {
        $target = max($stockLevel->max_qty, $stockLevel->min_qty);

        return max(0, $target - $stockLevel->qty_available);
    }

    /**
     * Build suggestion DTOs for all low-stock items.
     *
     * @return ReorderSuggestionData[]
     */
    public function suggestions(?int $companyId = null): array
    {
        return $this->lowStockLevels($companyId)
            ->map(fn (StockLevel $sl) => new ReorderSuggestionData(
                stockLevelId: $sl->id,
                itemId: (int) $sl->item_id,
                warehouseId: (int) $sl->warehouse_id,
                itemName: optional($sl->item)->name ?? 'Unknown',
                warehouseName: optional($sl->warehouse)->name ?? 'Unknown',
                qtyAvailable: (float) $sl->qty_available,
                minQty: (float) $sl->min_qty,
                recommendedOrderQty: $this->recommendOrderQuantity($sl),
            ))
            ->all();
    }

    /**
     * Dispatch draft purchase-order generation jobs for every low-stock item
     * that has a preferred supplier set on the item (field_item_id fallback or supplier FK).
     * Items without a resolvable supplier are skipped and reported back.
     *
     * @return array{dispatched: int, skipped: int}
     */
    public function generateDraftOrders(?int $companyId = null): array
    {
        $dispatched = 0;
        $skipped    = 0;

        foreach ($this->lowStockLevels($companyId) as $stockLevel) {
            $item = $stockLevel->item;

            // Resolve preferred supplier_id from item attributes if present
            $supplierId = null;

            if ($item && isset($item->preferred_supplier_id) && $item->preferred_supplier_id) {
                $supplierId = (int) $item->preferred_supplier_id;
            }

            if (!$supplierId) {
                $skipped++;
                continue;
            }

            $qty = $this->recommendOrderQuantity($stockLevel);

            if ($qty <= 0) {
                $skipped++;
                continue;
            }

            GeneratePurchaseOrderJob::dispatch([
                'company_id'  => $stockLevel->company_id,
                'supplier_id' => $supplierId,
                'status'      => 'draft',
                'currency'    => 'AUD',
                'notes'       => 'Auto-generated draft from reorder trigger (StockLevel #' . $stockLevel->id . ')',
                'items'       => [
                    [
                        'item_id'     => $stockLevel->item_id,
                        'qty_ordered' => $qty,
                        'unit_cost'   => 0,
                    ],
                ],
            ]);

            $dispatched++;
        }

        return ['dispatched' => $dispatched, 'skipped' => $skipped];
    }
}
