<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Models\InvoiceItems;
use App\Models\Tax;
use App\Models\Lead;
use App\Models\UnitType;
use App\Models\OrderItems;
use App\Traits\HasCompany;
use App\Models\ProductFiles;
use App\Models\ProductCategory;
use App\Traits\CustomFieldsTrait;
use App\Models\ProductSubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PurchaseProduct extends BaseModel
{

    use HasCompany, HasFactory;

    protected $fillable = [];

    protected $dates = ['created_at'];

    protected static function newFactory()
    {
        return \Modules\Purchase\Database\factories\PurchaseProductFactory::new();
    }

    protected $table = 'products';
    const FILE_PATH = 'products';

    protected $appends = ['total_amount', 'image_url', 'download_file_url'];

    protected $with = [];

    const CUSTOM_FIELD_MODEL = 'Modules\Purchase\Entities\PurchaseProduct';

    public function getImageUrlAttribute()
    {
        if (app()->environment(['development', 'demo']) && str_contains($this->default_image, 'http')) {
            return $this->default_image;
        }

        return ($this->default_image) ? asset_url_local_s3(PurchaseProduct::FILE_PATH . '/' . $this->default_image) : '';
    }

    public function getDownloadFileUrlAttribute()
    {
        return ($this->downloadable_file) ? asset_url_local_s3(PurchaseProduct::FILE_PATH . '/' . $this->downloadable_file) : null;
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }

    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(Lead::class, 'lead_products');
    }

    public static function taxbyid($id)
    {
        return Tax::where('id', $id)->withTrashed();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(ProductSubCategory::class, 'sub_category_id');
    }

    public function getTotalAmountAttribute()
    {

        if (!is_null($this->price) && !is_null($this->tax)) {
            return (int)$this->price + ((int)$this->price * ((int)$this->tax->rate_percent / 100));
        }

        return '';
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProductFiles::class, 'product_id')->orderByDesc('id');
    }

    public function getTaxListAttribute()
    {
        $productItem = PurchaseProduct::findOrFail($this->id);
        $taxes = '';

        if ($productItem && $productItem->taxes) {
            $numItems = count(json_decode($productItem->taxes));

            if (!is_null($productItem->taxes)) {
                foreach (json_decode($productItem->taxes) as $index => $tax) {
                    $tax = $this->taxbyid($tax)->first();
                    $taxes .= $tax->tax_name . ': ' . $tax->rate_percent . '%';

                    $taxes = ($index + 1 != $numItems) ? $taxes . ', ' : $taxes;
                }
            }
        }

        return $taxes;
    }

    public function orderItem(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'product_id');

    }

    public function invoiceItem(): HasMany
    {
        return $this->hasMany(InvoiceItems::class, 'product_id');
    }

    public function inventory()
    {
        return $this->hasMany(PurchaseStockAdjustment::class, 'product_id');
    }

    public function quantityInventory()
    {
        return $this->hasOne(PurchaseStockAdjustment::class, 'product_id');
    }

}
