<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;

class PurchaseVendorContact extends BaseModel
{

    use HasCompany;

    protected $fillable = ['contact_name', 'email', 'phone', 'title', 'purchase_vendor_id'];

    protected static function newFactory()
    {
        return \Modules\Purchase\Database\factories\PurchaseVendorContactFactory::new();
    }

}
