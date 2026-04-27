<?php

namespace Modules\SupplyChain\Services;

use Modules\SupplyChain\Entities\Supplier;
use Modules\SupplyChain\Entities\SupplierRating;

class SupplierService
{
    public function rateSupplier(Supplier $supplier, int $rating, ?string $comment = null, ?string $category = null): SupplierRating
    {
        return SupplierRating::create([
            'supplier_id' => $supplier->id,
            'rated_by' => auth()->id(),
            'rating' => $rating,
            'category' => $category,
            'comment' => $comment,
            'rated_at' => now(),
        ]);
    }

    public function topRated(int $limit = 10)
    {
        return Supplier::query()
            ->withCount('ratings')
            ->orderByDesc('fsm_rating')
            ->limit($limit)
            ->get();
    }
}
