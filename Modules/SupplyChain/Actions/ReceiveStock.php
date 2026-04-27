<?php

namespace Modules\SupplyChain\Actions;

use Modules\SupplyChain\Entities\PurchaseOrder;
use Modules\SupplyChain\Events\StockReceived;
use Modules\SupplyChain\Services\InventoryService;

class ReceiveStock
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function execute(PurchaseOrder $purchaseOrder, int $warehouseId, array $items)
    {
        $receipt = $this->inventoryService->receiveStock($purchaseOrder, $warehouseId, $items);

        event(new StockReceived($receipt));

        return $receipt;
    }
}
