<?php

namespace Modules\Purchase\Entities;

use App\Models\UnitType;
use App\Models\Tax;
use App\Models\Product;
use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseItem extends BaseModel
{

    use HasCompany;


    protected $fillable = [];

    protected $appends = ['total_amount'];
    protected $with = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'purchase_item_taxes');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseItemImage(): HasOne
    {
        return $this->hasOne(PurchaseItemImage::class, 'purchase_item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_id');
    }

    public function getTotalAmountAttribute()
    {

        if (!is_null($this->price) && !is_null($this->tax)) {
            return (int)$this->price + ((int)$this->price * ((int)$this->tax->rate_percent / 100));
        }

        return '';
    }

    public function itemTaxes(): HasMany
    {
        return $this->hasMany(PurchaseItemTax::class, 'purchase_item_id');
    }

    public function getTaxListAttribute()
    {

        $purchaseItem = $this;

        $taxes = '';

        if ($purchaseItem && $purchaseItem->taxes) {
            $numItems = count(json_decode($purchaseItem->taxes));

            if (!is_null($purchaseItem->taxes)) {
                foreach (json_decode($purchaseItem->taxes) as $index => $tax) {
                    $taxes .= $tax->tax_name . ': ' . $tax->rate_percent . '%';

                    $taxes = ($index + 1 != $numItems) ? $taxes . ', ' : $taxes;
                }
            }
        }

        return $taxes;
    }

}
