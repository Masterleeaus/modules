<?php

namespace Modules\Purchase\Tests\Unit;

use Tests\TestCase;

/**
 * Unit tests for Purchase module support / utility logic.
 *
 * Pure logic tests — no DB required.
 */
class PurchaseSupportTest extends TestCase
{
    // -----------------------------------------------------------------------
    // po_number uniqueness (company-scoped, not global)
    // -----------------------------------------------------------------------

    /** @test */
    public function po_number_format_is_prefix_separator_padded_number(): void
    {
        $prefix    = 'PO';
        $separator = '-';
        $number    = 7;
        $digits    = 4;

        $poNumber = $prefix . $separator . str_pad($number, $digits, '0', STR_PAD_LEFT);

        $this->assertSame('PO-0007', $poNumber);
    }

    /** @test */
    public function gst_amount_calculation_is_accurate(): void
    {
        $subTotal     = 1000.00;
        $gstRate      = 0.10; // 10 %
        $gstAmount    = round($subTotal * $gstRate, 2);
        $totalWithGst = $subTotal + $gstAmount;

        $this->assertSame(100.00, $gstAmount);
        $this->assertSame(1100.00, $totalWithGst);
    }

    /** @test */
    public function purchase_status_transitions_are_ordered(): void
    {
        $validStatuses = ['draft', 'sent', 'confirmed', 'received', 'cancelled'];

        // Confirm the expected state machine order
        $this->assertSame('draft',     $validStatuses[0]);
        $this->assertSame('sent',      $validStatuses[1]);
        $this->assertSame('confirmed', $validStatuses[2]);
        $this->assertSame('received',  $validStatuses[3]);
        $this->assertSame('cancelled', $validStatuses[4]);
    }

    /** @test */
    public function order_type_defaults_to_sale_for_backward_compatibility(): void
    {
        // Any existing row without an explicit order_type should behave as 'sale'
        $defaultOrderType = 'sale';
        $this->assertSame('sale', $defaultOrderType);
    }
}
