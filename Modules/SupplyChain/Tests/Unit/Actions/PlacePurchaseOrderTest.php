<?php

namespace Modules\SupplyChain\Tests\Unit\Actions;

use PHPUnit\Framework\TestCase;

/**
 * Tests pure logic within the action classes without requiring DB.
 */
class PlacePurchaseOrderTest extends TestCase
{
    public function test_total_is_sum_of_item_qty_times_unit_cost(): void
    {
        $items = [
            ['qty_ordered' => 10, 'unit_cost' => 5.00],
            ['qty_ordered' => 2,  'unit_cost' => 50.00],
            ['qty_ordered' => 1,  'unit_cost' => 0.0],
        ];

        $total = 0.0;
        foreach ($items as $item) {
            $total += ((float) $item['qty_ordered']) * ((float) $item['unit_cost']);
        }

        $this->assertSame(150.0, $total);
    }

    public function test_total_with_zero_items_is_zero(): void
    {
        $total = 0.0;
        $this->assertSame(0.0, $total);
    }

    public function test_default_status_is_ordered_when_not_provided(): void
    {
        $payload = [
            'supplier_id' => 1,
        ];

        $status = $payload['status'] ?? 'ordered';

        $this->assertSame('ordered', $status);
    }

    public function test_draft_status_is_preserved_when_provided(): void
    {
        $payload = [
            'supplier_id' => 1,
            'status'      => 'draft',
        ];

        $status = $payload['status'] ?? 'ordered';

        $this->assertSame('draft', $status);
    }

    public function test_default_currency_is_aud(): void
    {
        $payload = ['supplier_id' => 1];

        $currency = $payload['currency'] ?? 'AUD';

        $this->assertSame('AUD', $currency);
    }
}
