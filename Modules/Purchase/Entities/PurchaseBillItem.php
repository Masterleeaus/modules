<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseBillItem extends BaseModel
{
    use HasCompany;

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Purchase\Database\factories\PurchaseBillItemFactory::new();
    }

    public function bill() :BelongsTo
    {
        return $this->belongsTo(PurchaseBill::class);
    }

}
