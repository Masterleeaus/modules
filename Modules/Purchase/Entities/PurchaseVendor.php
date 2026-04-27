<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class PurchaseVendor extends BaseModel
{

    use HasCompany, Notifiable;

    protected $fillable = [];
    protected $with = [];

    /**
     * Get the user that owns the PurchaseVendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PurchaseVendorCategory::class, 'category_id');
    }

    public function purchaseBills(): HasMany
    {
        return $this->hasMany(PurchaseBill::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'vendor_id');
    }

}
