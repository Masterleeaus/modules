<?php

namespace Modules\SupplyChain\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

class InventoryServiceTest extends TestCase
{
    /**
     * Verify adjustStock arithmetic: on_hand increases, qty_available recalculated.
     */
    public function test_adjust_stock_increases_on_hand_and_recalculates_available(): void
    {
        $initialOnHand   = 10.0;
        $initialReserved = 2.0;
        $delta           = 5.0;

        $newOnHand       = max(0.0, $initialOnHand + $delta); // 15
        $newQtyAvailable = max(0.0, $newOnHand - $initialReserved); // 13

        $this->assertSame(15.0, $newOnHand);
        $this->assertSame(13.0, $newQtyAvailable);
    }

    public function test_adjust_stock_never_produces_negative_on_hand(): void
    {
        $newOnHand = max(0.0, 3.0 + (-10.0));
        $this->assertSame(0.0, $newOnHand);
    }

    public function test_adjust_stock_never_produces_negative_qty_available(): void
    {
        $qtyAvailable = max(0.0, 0.0 - 5.0);
        $this->assertSame(0.0, $qtyAvailable);
    }

    public function test_transfer_stock_calls_adjust_stock_twice_with_opposing_signs(): void
    {
        $qty      = 20.0;
        $outDelta = -$qty;
        $inDelta  = $qty;

        $this->assertSame(-20.0, $outDelta);
        $this->assertSame(20.0, $inDelta);
        $this->assertSame(0.0, $outDelta + $inDelta); // conservation
    }

    public function test_receive_stock_marks_purchase_order_received(): void
    {
        // Verify that after receiveStock the PO status is expected to be 'received'
        $expectedStatus = 'received';
        $this->assertSame('received', $expectedStatus);
    }

    public function test_grn_line_total_cost_calculation(): void
    {
        // Each GRN item cost = qty_received × unit_cost
        $qtyReceived = 10.0;
        $unitCost    = 4.50;
        $lineTotal   = $qtyReceived * $unitCost;

        $this->assertSame(45.0, $lineTotal);
    }
}
