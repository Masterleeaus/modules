<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseVendorCreditItemImage extends BaseModel
{
    use IconTrait;

    const FILE_PATH = 'vendorCredit-files';

    protected $appends = ['file_url', 'icon'];

    protected $fillable = ['vendor_item_id', 'filename', 'hashname', 'size', 'external_link'];

    public function getFileUrlAttribute()
    {
        if (empty($this->external_link)) {
            return asset_url_local_s3('vendorCredit-files/' . $this->vendor_item_id . '/' . $this->hashname);
        }
        elseif (!empty($this->external_link)) {
            return $this->external_link;
        }
        else {
            return '';
        }

    }

    public function item() : BelongsTo
    {
        return $this->belongsTo(PurchaseVendorItem::class, 'vendor_item_id');
    }

}
