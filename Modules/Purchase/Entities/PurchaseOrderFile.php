<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\IconTrait;

class PurchaseOrderFile extends BaseModel
{

    use IconTrait;

    const FILE_PATH = 'purchase-order';

    protected $fillable = [];
    protected $guarded = ['id'];

    protected $appends = ['file_url', 'icon'];

    public function getFileUrlAttribute()
    {
        return asset_url_local_s3(PurchaseOrderFile::FILE_PATH . '/' . $this->hashname);
    }

}
