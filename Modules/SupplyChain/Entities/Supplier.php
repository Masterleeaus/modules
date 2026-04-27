<?php

namespace Modules\SupplyChain\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use App\Models\ModuleSetting;

class Supplier extends BaseModel
{
    use HasCompany;

    protected $table = 'suppliers';
    protected $guarded = ['id'];
    const MODULE_NAME = 'supplychain';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function ratings()
    {
        return $this->hasMany(SupplierRating::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function averageRating(): float
    {
        return (float) $this->ratings()->avg('rating');
    }

    public static function addModuleSetting($company)
    {
        $roles = ['admin', 'employee'];
        ModuleSetting::createRoleSettingEntry(self::MODULE_NAME, $roles, $company);
    }
}
