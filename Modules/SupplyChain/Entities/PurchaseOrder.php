<?php

namespace Modules\SupplyChain\Entities;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasCompany;

    protected $fillable = [
        'company_id',
        'supplier_id',
        'ordered_by',
        'status',
        'ordered_at',
        'expected_date',
        'reference',
        'currency',
        'notes',
        'total',
    ];

    protected $casts = [
        'ordered_at'    => 'datetime',
        'expected_date' => 'date',
    ];

    /**
     * Prefer the Suppliers module supplier if active, otherwise fall back to
     * the local Inventory Supplier model.
     */
    public function supplier()
    {
        if (class_exists(\Modules\SupplyChain\Entities\Supplier::class)) {
            return $this->belongsTo(\Modules\SupplyChain\Entities\Supplier::class, 'supplier_id');
        }
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function orderedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'ordered_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function goodsReceipts()
    {
        return $this->hasMany(GoodsReceipt::class);
    }
}
