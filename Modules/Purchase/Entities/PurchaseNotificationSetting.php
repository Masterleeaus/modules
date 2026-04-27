<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;

class PurchaseNotificationSetting extends BaseModel
{

    use HasCompany;

    protected $guarded = ['id'];

}
