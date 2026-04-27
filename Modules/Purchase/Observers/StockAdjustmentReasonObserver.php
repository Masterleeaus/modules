<?php

namespace Modules\Purchase\Observers;

use Modules\Purchase\Entities\PurchaseStockAdjustmentReason;

class StockAdjustmentReasonObserver
{

    /**
     * @param PurchaseStockAdjustmentReason $item
     */
    public function saving(PurchaseStockAdjustmentReason $item)
    {
        if (company()) {
            $item->company_id = company()->id;
        }
    }

}
