<?php

namespace Modules\Purchase\Observers;

use Modules\Purchase\Entities\PurchaseStockAdjustment;

class PurchaseStockAdjustmentObserver
{

    /**
     * @param PurchaseStockAdjustment $item
     */
    public function saving(PurchaseStockAdjustment $item)
    {
        if (company()) {
            $item->company_id = company()->id;
        }
    }

}
