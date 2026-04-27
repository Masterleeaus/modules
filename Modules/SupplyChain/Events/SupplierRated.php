<?php

namespace Modules\SupplyChain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\SupplyChain\Entities\SupplierRating;

class SupplierRated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public SupplierRating $supplierRating)
    {
    }
}
