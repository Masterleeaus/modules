<?php

namespace Modules\SupplyChain\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\SupplyChain\Support\Enums\StockMovementType;
use Modules\SupplyChain\Support\DTOs\ReorderSuggestionData;

class SupplyChainModuleStructureTest extends TestCase
{
    public function test_required_domain_artifacts_exist(): void
    {
        $base = dirname(__DIR__, 2);

        $this->assertFileExists($base . '/Actions/PlacePurchaseOrder.php');
        $this->assertFileExists($base . '/Actions/ReceiveStock.php');
        $this->assertFileExists($base . '/Actions/TransferStock.php');
        $this->assertFileExists($base . '/Actions/RateSupplier.php');
        $this->assertFileExists($base . '/Services/SupplierService.php');
        $this->assertFileExists($base . '/Services/InventoryService.php');
        $this->assertFileExists($base . '/Services/ReorderService.php');
        $this->assertFileExists($base . '/Events/StockLevelLow.php');
        $this->assertFileExists($base . '/Events/PurchaseOrderPlaced.php');
        $this->assertFileExists($base . '/Events/StockReceived.php');
        $this->assertFileExists($base . '/Events/SupplierRated.php');
    }

    public function test_filament_resources_exist(): void
    {
        $base = dirname(__DIR__, 2);

        $this->assertFileExists($base . '/Filament/Resources/SupplierResource.php');
        $this->assertFileExists($base . '/Filament/Resources/StockResource.php');
        $this->assertFileExists($base . '/Filament/Resources/PurchaseOrderResource.php');
        $this->assertFileExists($base . '/Filament/Plugin/SupplyChainPlugin.php');
        $this->assertFileExists($base . '/Filament/Widgets/StockLevelsWidget.php');
        $this->assertFileExists($base . '/Filament/Widgets/LowStockAlertsWidget.php');
    }

    public function test_filament_resource_pages_exist(): void
    {
        $base = dirname(__DIR__, 2);

        $this->assertFileExists($base . '/Filament/Resources/SupplierResource/Pages/ListSuppliers.php');
        $this->assertFileExists($base . '/Filament/Resources/SupplierResource/Pages/CreateSupplier.php');
        $this->assertFileExists($base . '/Filament/Resources/SupplierResource/Pages/EditSupplier.php');
        $this->assertFileExists($base . '/Filament/Resources/SupplierResource/Pages/ViewSupplier.php');
        $this->assertFileExists($base . '/Filament/Resources/StockResource/Pages/ListStockLevels.php');
        $this->assertFileExists($base . '/Filament/Resources/PurchaseOrderResource/Pages/ListPurchaseOrders.php');
        $this->assertFileExists($base . '/Filament/Resources/PurchaseOrderResource/Pages/CreatePurchaseOrder.php');
        $this->assertFileExists($base . '/Filament/Resources/PurchaseOrderResource/Pages/ViewPurchaseOrder.php');
    }

    public function test_jobs_and_listeners_exist(): void
    {
        $base = dirname(__DIR__, 2);

        $this->assertFileExists($base . '/Jobs/CheckStockLevelsJob.php');
        $this->assertFileExists($base . '/Jobs/SendReorderAlertJob.php');
        $this->assertFileExists($base . '/Jobs/GeneratePurchaseOrderJob.php');
        $this->assertFileExists($base . '/Listeners/CompanyCreatedListener.php');
        $this->assertFileExists($base . '/Listeners/QueueReorderAlertListener.php');
        $this->assertFileExists($base . '/Console/Commands/CheckStockLevelsCommand.php');
    }

    public function test_stock_movement_type_enum_cases(): void
    {
        $this->assertSame('in', StockMovementType::In->value);
        $this->assertSame('out', StockMovementType::Out->value);
        $this->assertSame('adjust', StockMovementType::Adjust->value);

        $cases = StockMovementType::cases();
        $this->assertCount(3, $cases);
    }

    public function test_reorder_suggestion_dto_can_be_created(): void
    {
        $dto = new ReorderSuggestionData(
            stockLevelId: 1,
            itemId: 42,
            warehouseId: 7,
            itemName: 'Sodium Hypochlorite',
            warehouseName: 'Depot A',
            qtyAvailable: 2.0,
            minQty: 10.0,
            recommendedOrderQty: 18.0,
        );

        $this->assertSame(1, $dto->stockLevelId);
        $this->assertSame('Sodium Hypochlorite', $dto->itemName);
        $this->assertSame(18.0, $dto->recommendedOrderQty);
    }

    public function test_policies_exist(): void
    {
        $base = dirname(__DIR__, 2);

        $this->assertFileExists($base . '/Policies/SupplierPolicy.php');
        $this->assertFileExists($base . '/Policies/StockItemPolicy.php');
        $this->assertFileExists($base . '/Policies/PurchaseOrderPolicy.php');
    }
}
