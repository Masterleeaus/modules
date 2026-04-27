<?php

namespace Modules\Purchase\Entities;

use App\Models\User;
use App\Models\BaseModel;
use App\Traits\HasCompany;
use App\Models\BankAccount;
use Modules\Purchase\Entities\PurchaseVendor;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PurchaseVendorPayment extends BaseModel
{

    use HasCompany;

    protected $fillable = [];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    protected $with = [];

    /**
     * Get all of the comments for the PurchaseVendorPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendor(): HasOne
    {
        return $this->HasOne(PurchaseVendor::class, 'id', 'purchase_vendor_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

}
