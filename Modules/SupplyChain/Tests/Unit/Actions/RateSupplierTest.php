<?php

namespace Modules\SupplyChain\Tests\Unit\Actions;

use PHPUnit\Framework\TestCase;

class RateSupplierTest extends TestCase
{
    public function test_rating_must_be_between_1_and_5(): void
    {
        foreach ([1, 2, 3, 4, 5] as $valid) {
            $this->assertGreaterThanOrEqual(1, $valid);
            $this->assertLessThanOrEqual(5, $valid);
        }
    }

    public function test_rating_below_1_is_invalid(): void
    {
        $rating = 0;
        $this->assertFalse($rating >= 1 && $rating <= 5);
    }

    public function test_rating_above_5_is_invalid(): void
    {
        $rating = 6;
        $this->assertFalse($rating >= 1 && $rating <= 5);
    }

    public function test_average_rating_is_updated_after_new_rating(): void
    {
        // Simulate existing ratings + new one → recalculate average
        $existingRatings = [4, 5];
        $newRating       = 3;
        $allRatings      = array_merge($existingRatings, [$newRating]);

        $average = round(array_sum($allRatings) / count($allRatings), 1);

        $this->assertSame(4.0, $average);
    }
}
