<?php

namespace Modules\SupplyChain\Tests\Feature;

use PHPUnit\Framework\TestCase;

class SupplyChainManifestsTest extends TestCase
{
    public function test_required_manifests_exist(): void
    {
        $base = dirname(__DIR__, 2) . '/manifests';

        $this->assertFileExists($base . '/ai_tools.json');
        $this->assertFileExists($base . '/signals_manifest.json');
        $this->assertFileExists($base . '/lifecycle_manifest.json');
        $this->assertFileExists($base . '/api_manifest.json');
    }

    public function test_manifests_are_valid_json(): void
    {
        $base = dirname(__DIR__, 2) . '/manifests';

        foreach (['ai_tools', 'signals_manifest', 'lifecycle_manifest', 'api_manifest'] as $name) {
            $path = "{$base}/{$name}.json";
            $decoded = json_decode(file_get_contents($path), true);
            $this->assertNotNull($decoded, "Manifest {$name}.json is invalid JSON");
        }
    }

    public function test_signals_manifest_declares_supplychain_events(): void
    {
        $path = dirname(__DIR__, 2) . '/manifests/signals_manifest.json';
        $data = json_decode(file_get_contents($path), true);

        $this->assertArrayHasKey('signals', $data);
        $signals = array_column($data['signals'], 'event');

        $expected = [
            'Modules\\SupplyChain\\Events\\StockLevelLow',
            'Modules\\SupplyChain\\Events\\PurchaseOrderPlaced',
            'Modules\\SupplyChain\\Events\\StockReceived',
            'Modules\\SupplyChain\\Events\\SupplierRated',
        ];

        foreach ($expected as $event) {
            $this->assertContains($event, $signals, "signals_manifest missing event: {$event}");
        }
    }

    public function test_lifecycle_manifest_declares_supplychain_hooks(): void
    {
        $path = dirname(__DIR__, 2) . '/manifests/lifecycle_manifest.json';
        $data = json_decode(file_get_contents($path), true);

        $this->assertArrayHasKey('hooks', $data);
        $this->assertNotEmpty($data['hooks']);
    }

    public function test_module_json_is_valid(): void
    {
        $path = dirname(__DIR__, 2) . '/module.json';
        $this->assertFileExists($path);

        $data = json_decode(file_get_contents($path), true);
        $this->assertNotNull($data, 'module.json is invalid JSON');
        $this->assertSame('SupplyChain', $data['name']);
        $this->assertSame('supplychain', $data['alias']);
        $this->assertNotEmpty($data['providers']);
    }
}
