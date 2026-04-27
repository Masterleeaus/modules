<?php

namespace Modules\SupplyChain\Listeners;

use Modules\SupplyChain\Events\SupplierRated;

/**
 * Recompute the cached fsm_rating on the Supplier from all submitted ratings.
 * This keeps the denormalised column in sync whenever a new rating is submitted.
 */
class UpdateSupplierRatingListener
{
    public function handle(SupplierRated $event): void
    {
        $supplierRating = $event->supplierRating;

        if (!$supplierRating->relationLoaded('supplier')) {
            $supplierRating->load('supplier');
        }

        $supplier = $supplierRating->supplier;

        if (!$supplier) {
            return;
        }

        $average = $supplier->ratings()->avg('rating');

        $supplier->updateQuietly([
            'fsm_rating' => round((float) $average, 1),
        ]);
    }
}
