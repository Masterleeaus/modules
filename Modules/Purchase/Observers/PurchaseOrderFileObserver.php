<?php

namespace Modules\Purchase\Observers;

use App\Helper\Files;
use Exception;
use Modules\Purchase\Entities\PurchaseItem;
use Modules\Purchase\Entities\PurchaseItemImage;
use Modules\Purchase\Entities\PurchaseItemTax;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseOrderFile;

class PurchaseOrderFileObserver
{

    public function saving(PurchaseOrderFile $file)
    {
        if (!isRunningInConsoleOrSeeding()) {

            if (user()) {
                $file->last_updated_by = user()->id;
            }
        }
    }

    public function creating(PurchaseOrderFile $file)
    {
        $file->added_by = user() ? user()->id : null;
    }

}
