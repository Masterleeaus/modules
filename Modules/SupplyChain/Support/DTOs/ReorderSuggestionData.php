<?php

namespace Modules\SupplyChain\Support\DTOs;

class ReorderSuggestionData
{
    public function __construct(
        public readonly int $stockLevelId,
        public readonly int $itemId,
        public readonly int $warehouseId,
        public readonly string $itemName,
        public readonly string $warehouseName,
        public readonly float $qtyAvailable,
        public readonly float $minQty,
        public readonly float $recommendedOrderQty,
    ) {
    }
}
