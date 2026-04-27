<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use App\Traits\IconTrait;

class PurchaseItemImage extends BaseModel
{

    const MODULE_NAME = 'purchase_item_images';

    use IconTrait;

    const FILE_PATH = 'purchase-order-files';

    protected $appends = ['file_url', 'icon'];
    protected $fillable = ['purchase_item_id', 'filename', 'hashname', 'size', 'external_link'];

    public function getFileUrlAttribute()
    {
        if (empty($this->external_link)) {
            return asset_url_local_s3(PurchaseItemImage::FILE_PATH . '/' . $this->purchase_item_id . '/' . $this->hashname);
        }

        if (!empty($this->external_link)) {
            return $this->external_link;
        }

        return '';


    }

}
