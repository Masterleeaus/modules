<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use App\Models\ModuleSetting;
use Modules\Units\Entities\Floor;
use Modules\Units\Entities\Tower;
use Modules\Units\Entities\TypeUnit;
use Modules\Accountings\Entities\Pnl;
use Modules\Accountings\Entities\BalanceSheet;

class Accounting extends BaseModel
{
    use HasCompany;

    protected $table = 'acc_coa';
    protected $guarded = ['id'];
    const MODULE_NAME = 'accountings';

    public static function addModuleSetting($company)
    {
        // create admin, employee and client module settings
        $roles = ['admin', 'employee'];
        ModuleSetting::createRoleSettingEntry(self::MODULE_NAME, $roles, $company);
    }

    public function bs()
    {
        return $this->belongsTo(BalanceSheet::class, 'bs_id');
    }

    public function pnl()
    {
        return $this->belongsTo(Pnl::class, 'pnl_id');
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function tower()
    {
        return $this->belongsTo(Tower::class, 'tower_id');
    }

    public function typeunit()
    {
        return $this->belongsTo(TypeUnit::class, 'typeunit_id');
    }
}
