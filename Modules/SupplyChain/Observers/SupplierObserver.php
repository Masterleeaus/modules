<?php

namespace Modules\SupplyChain\Observers;

use Modules\SupplyChain\Entities\Supplier;

class SupplierObserver
{

    public function saving(Supplier $unit)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $unit->company_id = company()->id;
        }
    }

}

