<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInventoryFile extends BaseModel
{
    use HasCompany, HasFactory, IconTrait;

    const FILE_PATH = 'inventory';

    protected $guarded = ['id'];
    protected $table = 'purchase_inventory_files';

    protected $appends = ['file_url', 'icon'];

    public $timestamps = false;
    protected static function newFactory()
    {
        return \Modules\Purchase\Database\factories\PurchaseInventoryFileFactory::new();
    }

    public function getFileUrlAttribute()
    {
        return asset_url_local_s3(PurchaseInventory::FILE_PATH . '/' . $this->hashname);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PurchaseInventory::class);
    }

}
