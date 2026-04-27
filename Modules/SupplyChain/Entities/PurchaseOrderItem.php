<?php

namespace Modules\SupplyChain\Entities;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id','item_id','qty_ordered','unit_cost'];
    public function order(){ return $this->belongsTo(PurchaseOrder::class,'purchase_order_id'); }
    public function item(){ return $this->belongsTo(Item::class); }
}
