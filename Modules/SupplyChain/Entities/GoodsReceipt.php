<?php

namespace Modules\SupplyChain\Entities;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    use HasCompany;

    protected $fillable = [
        'company_id',
        'purchase_order_id',
        'warehouse_id',
        'received_by',
        'received_at',
        'reference',
        'notes',
    ];

    protected $casts = ['received_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }
}
