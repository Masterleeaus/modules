<?php

namespace Modules\Asset\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetType extends BaseModel
{
    use HasCompany;

    protected $table = 'asset_types';

    protected $hidden = [
        'created_at',
        'company_id',
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

    public static function allAssetTypes()
    {
        if (user()->permission('view_assets_type') == 'all') {
            return AssetType::all();
        }

        return AssetType::where('added_by', user()->id)->get();

    }
}
