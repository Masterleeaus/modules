<?php

namespace Modules\Purchase\Entities;

use App\Models\Tax;
use App\Models\UnitType;
use App\Models\BaseModel;
use App\Models\ProposalItemImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseVendorItem extends BaseModel
{

    use HasFactory;

    protected $fillable = [];

    protected $guarded = ['id'];

    protected $with = [];

    public function purchaseVendorCreditItemImage(): HasOne
    {
        return $this->hasOne(PurchaseVendorCreditItemImage::class, 'vendor_item_id');
    }

    public static function taxbyid($id)
    {
        return Tax::where('id', $id)->withTrashed();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_id');
    }

    public function getTaxListAttribute()
    {
        $PurchaseVendorItem = $this;

        $taxes = '';

        if ($PurchaseVendorItem && $PurchaseVendorItem->taxes) {
            $numItems = count(json_decode($PurchaseVendorItem->taxes));

            if (!is_null($PurchaseVendorItem->taxes)) {
                foreach (json_decode($PurchaseVendorItem->taxes) as $index => $tax) {
                    $tax = $this->taxbyid($tax)->first();
                    $taxes .= $tax->tax_name . ': ' . $tax->rate_percent . '%';

                    $taxes = ($index + 1 != $numItems) ? $taxes . ', ' : $taxes;
                }
            }
        }

        return $taxes;
    }

}
