<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Models\Tax;

class PurchaseItemTax extends BaseModel
{

    protected $table = 'purchase_item_taxes';


    public static function taxbyid($id)
    {
        return Tax::where('id', $id)->withTrashed();
    }

}
