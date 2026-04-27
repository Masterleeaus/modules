<?php

namespace Modules\Biometric\Entities;

use App\Models\BaseModel;
use App\Models\ModuleSetting;

class BiometricGlobalSetting extends BaseModel
{

    protected $guarded = ['id'];

    const MODULE_NAME = 'biometric';

    public static function addModuleSetting($company)
    {
        $roles = ['employee', 'admin'];
        ModuleSetting::createRoleSettingEntry(self::MODULE_NAME, $roles, $company);
    }
}
