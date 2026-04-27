<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePaymentBill extends BaseModel
{

    protected $fillable = [];

    public function bill(): BelongsTo
    {
        return $this->BelongsTo(PurchaseBill::class, 'purchase_bill_id', 'id');
    }

}
