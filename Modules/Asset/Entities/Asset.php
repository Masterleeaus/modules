<?php

namespace Modules\Asset\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Asset\Database\factories\AssetFactory;

class Asset extends BaseModel
{

    use HasFactory, HasCompany;

    //region Properties

    protected $table = 'assets';

    CONST STATUSES = [
        'lent' => 'text-yellow',
        'available' => 'text-light-green',
        'non-functional' => 'text-red',
        'lost' => 'text-warning',
        'damaged' => 'text-pink',
        'under-maintenance' => 'text-orange',
    ];

    protected $appends = ['image_url'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'company_id',
        'location_id',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'company_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    //endregion

    public function history(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function latestHistory(): HasOne
    {
        return $this->hasOne(AssetHistory::class)->latest();
    }

    public function assetType(): BelongsTo
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id', 'id');
    }

    //endregion

    public function getImageUrlAttribute()
    {
        if (str_contains($this->image, 'http')) {
            return $this->image;
        }

        return ($this->image) ? asset_url_local_s3('assets/' . $this->image) : '';
    }

    //region Custom Functions

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return AssetFactory::new();
    }

}
