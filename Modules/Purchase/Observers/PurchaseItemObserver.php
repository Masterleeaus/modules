<?php

namespace Modules\Purchase\Observers;

use Modules\Purchase\Entities\PurchaseItem;

class PurchaseItemObserver
{

    public function creating(PurchaseItem $item)
    {
        if (company()) {
            $item->company_id = company()->id;
        }

    }

}
