<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchasePaymentBill;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseBill extends BaseModel
{

    use HasCompany;

    protected $fillable = [];
    protected $dates = ['bill_date'];
    protected $appends = ['bill_number', 'total_amount', 'original_bill_number'];

    public function getBillNumberAttribute()
    {
        $purchaseSettings = cache()->rememberForever('purchase_setting_' . $this->company_id, function () {
            return PurchaseSetting::first();
        });

        $zero = '';

        if (strlen($this->attributes['purchase_bill_number']) < $purchaseSettings->bill_number_digit) {

            $condition = $purchaseSettings->bill_number_digit - strlen($this->attributes['purchase_bill_number']);

            for ($i = 0; $i < $condition; $i++) {
                $zero = '0' . $zero;
            }
        }

        return $purchaseSettings->bill_prefix . $purchaseSettings->bill_number_separator . $zero . $this->attributes['purchase_bill_number'];
    }

    public function getTotalAmountAttribute()
    {

        if (!is_null($this->price) && !is_null($this->tax)) {
            return (int)$this->price + ((int)$this->price * ((int)$this->tax->rate_percent / 100));
        }

        return '';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function vendor(): BelongsTo
    {
        return $this->BelongsTo(PurchaseVendor::class, 'purchase_vendor_id', 'id');
    }

    public static function lastPurchaseBillNumber()
    {
        return (int)PurchaseBill::max('purchase_bill_number');
    }

    public function purchaseVendor(): BelongsTo
    {
        return $this->belongsTo(PurchaseVendor::class);
    }

    public function getOriginalBillNumberAttribute()
    {
        $purchaseSettings = cache()->rememberForever('purchase_setting_' . $this->company_id, function () {
            return PurchaseSetting::first();
        });

        $zero = '';

        if (strlen($this->attributes['purchase_bill_number']) < $purchaseSettings->bill_number_digit) {
            $condition = $purchaseSettings->bill_number_digit - strlen($this->attributes['purchase_bill_number']);

            for ($i = 0; $i < $condition; $i++) {
                $zero = '0' . $zero;
            }
        }

        return $zero . $this->attributes['purchase_bill_number'];
    }

    public function purchasePaymentBills(): HasMany
    {
        return $this->hasMany(PurchasePaymentBill::class, 'purchase_bill_id');
    }

    public function amountDue($vendorId)
    {
        $amountPaid = $this->purchasePaymentBills->where('purchase_vendor_id', $vendorId)->sum('total_paid');
        $total = $this->total;
        $due = $total - $amountPaid;
        return max($due, 0);
    }

    public function vendorPayment(): HasMany
    {
        return $this->hasMany(PurchaseVendorPayment::class, 'purchase_vendor_id', 'purchase_vendor_id')->orderByDesc('paid_on');
    }

    public function paymentBill()
    {
        return $this->hasMany(PurchasePaymentBill::class, 'purchase_bill_id');
    }

    public function amountPaid()
    {
        return $this->paymentBill->where('purchase_bill_id', $this->id)->sum('total_paid');
    }

    public function getPaidAmount()
    {
        return $this->vendorPayment->sum('received_payment');
    }

    public function scopePending($query)
    {
        return $query->where(function ($q) {
            $q->where('purchase_bills.status', 'open')
                ->orWhere('purchase_bills.status', 'partial_paid');
        });
    }

}
