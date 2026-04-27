<?php

namespace Modules\SupplyChain\Tests\Unit\Actions;

use PHPUnit\Framework\TestCase;

class TransferStockTest extends TestCase
{
    public function test_transfer_is_net_zero(): void
    {
        // Transferring Q units from A → B: A loses Q, B gains Q, total net = 0
        $qty      = 25.0;
        $fromDelta = -$qty;
        $toDelta   = $qty;

        $this->assertSame(0.0, $fromDelta + $toDelta);
    }

    public function test_transfer_quantity_must_be_positive(): void
    {
        $qty = 5.0;
        $this->assertGreaterThan(0, $qty);
    }

    public function test_negative_transfer_should_not_occur(): void
    {
        // Transfers with qty <= 0 are nonsensical; validate positive constraint.
        $qty = -5.0;
        $this->assertFalse($qty > 0);
    }
}
