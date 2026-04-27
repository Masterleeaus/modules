<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class AccountingSetting extends BaseModel
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_accounting_settings';
    protected $guarded = ['id'];

    protected $casts = [
        'period_lock_date' => 'date',
    ];
}
