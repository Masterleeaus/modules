<?php

namespace Modules\SupplyChain\Actions;

use Modules\SupplyChain\Entities\Supplier;
use Modules\SupplyChain\Events\SupplierRated;
use Modules\SupplyChain\Services\SupplierService;

class RateSupplier
{
    public function __construct(private readonly SupplierService $supplierService)
    {
    }

    public function execute(Supplier $supplier, int $rating, ?string $comment = null, ?string $category = null)
    {
        $supplierRating = $this->supplierService->rateSupplier($supplier, $rating, $comment, $category);

        event(new SupplierRated($supplierRating));

        return $supplierRating;
    }
}
