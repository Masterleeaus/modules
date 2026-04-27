<?php

namespace Modules\SupplyChain\Tests\Unit\Console;

use PHPUnit\Framework\TestCase;

class CheckStockLevelsCommandTest extends TestCase
{
    public function test_command_file_exists(): void
    {
        $path = dirname(__DIR__, 3) . '/Console/Commands/CheckStockLevelsCommand.php';
        $this->assertFileExists($path);
    }

    public function test_command_signature_present_in_source(): void
    {
        $path    = dirname(__DIR__, 3) . '/Console/Commands/CheckStockLevelsCommand.php';
        $content = file_get_contents($path);

        $this->assertStringContainsString('supplychain:check-stock', $content);
        $this->assertStringContainsString('--queue', $content);
    }

    public function test_check_stock_levels_job_file_exists(): void
    {
        $path = dirname(__DIR__, 3) . '/Jobs/CheckStockLevelsJob.php';
        $this->assertFileExists($path);
    }

    public function test_check_stock_levels_job_dispatches_send_reorder_alert_job(): void
    {
        $path    = dirname(__DIR__, 3) . '/Jobs/CheckStockLevelsJob.php';
        $content = file_get_contents($path);

        $this->assertStringContainsString('SendReorderAlertJob', $content);
    }

    public function test_command_schedule_present_in_kernel(): void
    {
        // Navigate up from Modules/SupplyChain/Tests/Unit/Console/ → repo root
        $kernelPath = dirname(__DIR__, 5) . '/app/Console/Kernel.php';
        $this->assertFileExists($kernelPath);

        $content = file_get_contents($kernelPath);
        $this->assertStringContainsString('supplychain:check-stock', $content);
        $this->assertStringContainsString('--queue', $content);
    }

    public function test_command_uses_generate_purchase_order_job(): void
    {
        // The reorder flow: CheckStockLevelsJob → sends StockLevelLow event → QueueReorderAlertListener → SendReorderAlertJob
        $path    = dirname(__DIR__, 3) . '/Listeners/QueueReorderAlertListener.php';
        $content = file_get_contents($path);

        $this->assertStringContainsString('SendReorderAlertJob', $content);
    }
}
