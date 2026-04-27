<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Models\CompanyAddress;
use App\Models\Currency;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseOrder extends BaseModel
{

    use HasCompany;

    protected $casts = [
        'purchase_date' => 'datetime',
        'expected_delivery_date' => 'datetime'
    ];

    public static function lastOrderNumber()
    {
        return (int)PurchaseOrder::max('purchase_order_number');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(PurchaseVendor::class, 'vendor_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_order_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CompanyAddress::class, 'address_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PurchaseOrderFile::class, 'purchase_order_id')->orderByDesc('id');
    }

    public function purchaseBill(): HasOne
    {
        return $this->hasOne(PurchaseBill::class, 'purchase_order_id');
    }

    public function getOriginalOrderNumberAttribute()
    {
        $purchaseSetting = cache()->rememberForever('purchase_setting_' . $this->company_id, function () {
            return PurchaseSetting::first();
        });

        $zero = '';

        if (strlen($this->attributes['purchase_order_number']) < $purchaseSetting->purchase_order_number_digit) {
            $condition = $purchaseSetting->purchase_order_number_digit - strlen($this->attributes['purchase_order_number']);

            for ($i = 0; $i < $condition; $i++) {
                $zero = '0' . $zero;
            }
        }
        return $zero . $this->attributes['purchase_order_number'];
    }

    public function getPurchaseOrderNumberAttribute($value)
    {
        if (is_null($value)) {
            return '';
        }

        $purchaseSetting = cache()->rememberForever('purchase_setting_' . $this->company_id, function () {
            return PurchaseSetting::first();
        });;

        $zero = '';

        if (strlen($value) < $purchaseSetting->purchase_order_number_digit) {
            $condition = $purchaseSetting->purchase_order_number_digit - strlen($value);

            for ($i = 0; $i < $condition; $i++) {
                $zero = '0' . $zero;
            }
        }

        return $purchaseSetting->purchase_order_prefix . $purchaseSetting->purchase_order_number_separator . $zero . $value;

    }

}
