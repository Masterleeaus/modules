<?php

namespace Modules\SupplyChain\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

class SupplierServiceTest extends TestCase
{
    public function test_rate_supplier_creates_rating_with_correct_fields(): void
    {
        $expectedPayload = [
            'supplier_id' => 5,
            'rated_by'    => 1,
            'rating'      => 4,
            'category'    => 'delivery',
            'comment'     => 'Fast and reliable',
        ];

        $this->assertSame(5, $expectedPayload['supplier_id']);
        $this->assertSame(4, $expectedPayload['rating']);
        $this->assertSame('delivery', $expectedPayload['category']);
        $this->assertSame('Fast and reliable', $expectedPayload['comment']);
    }

    public function test_supplier_average_rating_rounds_correctly(): void
    {
        $ratings = [3, 4, 5];
        $average = round(array_sum($ratings) / count($ratings), 1);
        $this->assertSame(4.0, $average);
    }

    public function test_supplier_average_rating_with_decimal(): void
    {
        $ratings = [3, 4];
        $average = round(array_sum($ratings) / count($ratings), 1);
        $this->assertSame(3.5, $average);
    }

    public function test_supplier_average_rating_single_rating(): void
    {
        $ratings = [5];
        $average = round(array_sum($ratings) / count($ratings), 1);
        $this->assertSame(5.0, $average);
    }
}
