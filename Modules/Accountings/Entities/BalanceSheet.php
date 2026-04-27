<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use Modules\Accountings\Entities\Accounting;
use App\Traits\HasCompany;

class BalanceSheet extends BaseModel
{
    use HasCompany;

    protected $table = 'acc_map_bs';
    protected $guarded = ['id'];


    public function acc()
    {
        return $this->hasMany(Accounting::class);
    }
}

