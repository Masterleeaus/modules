<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use Modules\Accountings\Entities\Accounting;
use App\Traits\HasCompany;

class Pnl extends BaseModel
{
    use HasCompany;

    protected $table = 'acc_map_pnl';
    protected $guarded = ['id'];


    public function acc()
    {
        return $this->hasMany(Accounting::class);
    }
}

