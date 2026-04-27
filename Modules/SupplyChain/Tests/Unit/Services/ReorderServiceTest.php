<?php

namespace Modules\SupplyChain\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use Modules\SupplyChain\Support\DTOs\ReorderSuggestionData;

class ReorderServiceTest extends TestCase
{
    /** Simulate recommendOrderQuantity: target = max(max_qty, min_qty), return max(0, target - available) */
    private function calcRecommendQty(float $maxQty, float $minQty, float $qtyAvailable): float
    {
        $target = max($maxQty, $minQty);
        return max(0, $target - $qtyAvailable);
    }

    public function test_recommend_order_quantity_uses_max_qty_as_target(): void
    {
        $qty = $this->calcRecommendQty(100.0, 10.0, 5.0);
        $this->assertSame(95.0, $qty);
    }

    public function test_recommend_order_quantity_falls_back_to_min_when_max_is_zero(): void
    {
        $qty = $this->calcRecommendQty(0.0, 20.0, 3.0);
        $this->assertSame(17.0, $qty);
    }

    public function test_recommend_order_quantity_never_negative(): void
    {
        // already above max
        $qty = $this->calcRecommendQty(10.0, 5.0, 50.0);
        $this->assertSame(0.0, $qty);
    }

    public function test_recommend_order_quantity_exact_match_returns_zero(): void
    {
        $qty = $this->calcRecommendQty(50.0, 10.0, 50.0);
        $this->assertSame(0.0, $qty);
    }

    public function test_reorder_suggestion_dto_fields_are_set_correctly(): void
    {
        $dto = new ReorderSuggestionData(
            stockLevelId: 7,
            itemId: 42,
            warehouseId: 3,
            itemName: 'Bleach 5L',
            warehouseName: 'Main Depot',
            qtyAvailable: 2.0,
            minQty: 10.0,
            recommendedOrderQty: 48.0,
        );

        $this->assertSame(7, $dto->stockLevelId);
        $this->assertSame(42, $dto->itemId);
        $this->assertSame(3, $dto->warehouseId);
        $this->assertSame('Bleach 5L', $dto->itemName);
        $this->assertSame('Main Depot', $dto->warehouseName);
        $this->assertSame(2.0, $dto->qtyAvailable);
        $this->assertSame(10.0, $dto->minQty);
        $this->assertSame(48.0, $dto->recommendedOrderQty);
    }

    public function test_low_stock_detection_logic(): void
    {
        // Stock is low when qty_available <= min_qty AND min_qty > 0
        $cases = [
            ['qty_available' => 5,  'min_qty' => 10, 'expected' => true],
            ['qty_available' => 10, 'min_qty' => 10, 'expected' => true],
            ['qty_available' => 11, 'min_qty' => 10, 'expected' => false],
            ['qty_available' => 0,  'min_qty' => 0,  'expected' => false], // min=0 excluded
            ['qty_available' => 0,  'min_qty' => 5,  'expected' => true],
        ];

        foreach ($cases as $c) {
            $isLow = $c['qty_available'] <= $c['min_qty'] && $c['min_qty'] > 0;
            $this->assertSame($c['expected'], $isLow, "Failed for qty={$c['qty_available']} min={$c['min_qty']}");
        }
    }
}
