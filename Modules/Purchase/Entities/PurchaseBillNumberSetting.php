<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;

class PurchaseBillNumberSetting extends BaseModel
{

    use HasCompany;

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Purchase\Database\factories\PurchaseBillNumberSettingFactory::new();
    }

}
