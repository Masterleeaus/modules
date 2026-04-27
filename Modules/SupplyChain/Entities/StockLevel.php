<?php

namespace Modules\SupplyChain\Entities;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    use HasCompany;

    public $timestamps = true;

    protected $table = 'stock_levels';

    protected $fillable = [
        'company_id',
        'item_id',
        'warehouse_id',
        'on_hand',
        'qty_reserved',
        'qty_available',
        'min_qty',
        'max_qty',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** Recalculate qty_available and persist. */
    public function recalculate(): void
    {
        $this->qty_available = max(0, $this->on_hand - $this->qty_reserved);
        $this->save();
    }
}
