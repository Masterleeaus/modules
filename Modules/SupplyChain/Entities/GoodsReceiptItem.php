<?php

namespace Modules\SupplyChain\Entities;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    protected $fillable = ['goods_receipt_id','purchase_order_item_id','item_id','qty_received','unit_cost'];

    public function receipt(){ return $this->belongsTo(GoodsReceipt::class,'goods_receipt_id'); }
    public function orderItem(){ return $this->belongsTo(PurchaseOrderItem::class,'purchase_order_item_id'); }
}
