<?php

namespace Modules\SupplyChain\Tests\Unit\Listeners;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Structural wiring test: verifies every event class has the correct listener
 * registered in EventServiceProvider, without booting the Laravel app.
 */
class EventListenerWiringTest extends TestCase
{
    /** @return array<class-string, class-string[]> */
    private function getListenMap(): array
    {
        $path = dirname(__DIR__, 3) . '/Providers/EventServiceProvider.php';
        $this->assertFileExists($path, 'EventServiceProvider.php not found');

        $content = file_get_contents($path);
        return ['_raw' => $content];
    }

    public function test_stock_level_low_listener_is_wired(): void
    {
        ['_raw' => $src] = $this->getListenMap();
        $this->assertStringContainsString('StockLevelLow::class', $src);
        $this->assertStringContainsString('QueueReorderAlertListener::class', $src);
    }

    public function test_purchase_order_placed_listener_is_wired(): void
    {
        ['_raw' => $src] = $this->getListenMap();
        $this->assertStringContainsString('PurchaseOrderPlaced::class', $src);
        $this->assertStringContainsString('LogPurchaseOrderPlacedListener::class', $src);
    }

    public function test_stock_received_listener_is_wired(): void
    {
        ['_raw' => $src] = $this->getListenMap();
        $this->assertStringContainsString('StockReceived::class', $src);
        $this->assertStringContainsString('LogStockReceivedListener::class', $src);
    }

    public function test_supplier_rated_listener_is_wired(): void
    {
        ['_raw' => $src] = $this->getListenMap();
        $this->assertStringContainsString('SupplierRated::class', $src);
        $this->assertStringContainsString('UpdateSupplierRatingListener::class', $src);
    }

    public function test_all_listener_files_exist(): void
    {
        $base = dirname(__DIR__, 3) . '/Listeners/';

        $this->assertFileExists($base . 'QueueReorderAlertListener.php');
        $this->assertFileExists($base . 'LogPurchaseOrderPlacedListener.php');
        $this->assertFileExists($base . 'LogStockReceivedListener.php');
        $this->assertFileExists($base . 'UpdateSupplierRatingListener.php');
        $this->assertFileExists($base . 'CompanyCreatedListener.php');
    }

    public function test_all_event_files_exist(): void
    {
        $base = dirname(__DIR__, 3) . '/Events/';

        $this->assertFileExists($base . 'StockLevelLow.php');
        $this->assertFileExists($base . 'PurchaseOrderPlaced.php');
        $this->assertFileExists($base . 'StockReceived.php');
        $this->assertFileExists($base . 'SupplierRated.php');
    }
}
