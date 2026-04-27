<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class PurchaseManagementSetting extends BaseModel
{

    protected $table = 'purchase_management_settings';

    const MODULE_NAME = 'purchase';

}
