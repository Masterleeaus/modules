<?php

namespace Modules\SupplyChain\Entities;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasCompany;

    protected $fillable = [
        'company_id',
        'item_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'quantity',
        'note',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function from()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function to()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
}
