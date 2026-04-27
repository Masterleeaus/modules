<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseStockAdjustment extends BaseModel
{

    use HasFactory, HasCompany;

    protected $table = 'purchase_stock_adjustments';

    protected $fillable = [];

    public function reason(): BelongsTo
    {
        return $this->belongsTo(PurchaseStockAdjustmentReason::class, 'reason_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PurchaseProduct::class, 'product_id');
    }

}
