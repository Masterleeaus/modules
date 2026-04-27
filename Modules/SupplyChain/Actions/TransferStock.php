<?php

namespace Modules\SupplyChain\Actions;

use Modules\SupplyChain\Services\InventoryService;

class TransferStock
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function execute(int $companyId, int $itemId, int $fromWarehouseId, int $toWarehouseId, float $quantity, ?string $note = null): void
    {
        $this->inventoryService->transferStock($companyId, $itemId, $fromWarehouseId, $toWarehouseId, $quantity, $note);
    }
}
