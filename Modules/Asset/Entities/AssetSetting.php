<?php

namespace Modules\Asset\Entities;

use App\Models\BaseModel;
use App\Models\ModuleSetting;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetSetting extends BaseModel
{
    protected $table = 'asset_settings';

    const MODULE_NAME = 'asset';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function history(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }

    public static function addModuleSetting($company)
    {
        $roles = ['employee', 'admin'];
        ModuleSetting::createRoleSettingEntry(self::MODULE_NAME, $roles, $company);
    }
}
