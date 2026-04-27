<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseInventory extends BaseModel
{

    use HasCompany, HasFactory, Notifiable;

    const FILE_PATH = 'inventory';

    protected $dates = ['date', 'created_at', 'updated_at'];

    protected $table = 'purchase_inventory_adjustment';

    protected $with = [];

    public function getImageUrlAttribute()
    {
        if (app()->environment(['development', 'demo']) && str_contains($this->default_image, 'http')) {
            return $this->default_image;
        }

        return ($this->default_image) ? asset_url_local_s3(PurchaseInventory::FILE_PATH . '/' . $this->default_image) : '';
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(PurchaseStockAdjustment::class, 'inventory_id')->orderByDesc('id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PurchaseInventoryFile::class, 'inventory_id')->orderByDesc('id');
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(PurchaseStockAdjustmentReason::class);
    }

}
