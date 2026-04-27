<?php

namespace Modules\SupplyChain\Exports;

use Modules\SupplyChain\Entities\StockLevel;

class StockLevelsExport
{
    public function toArray(): array
    {
        return StockLevel::query()
            ->get(['item_id', 'warehouse_id', 'on_hand', 'qty_available', 'min_qty', 'max_qty'])
            ->toArray();
    }
}
