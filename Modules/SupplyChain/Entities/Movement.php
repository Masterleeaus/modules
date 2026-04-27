<?php

namespace Modules\SupplyChain\Entities;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movement extends Model
{
    use SoftDeletes, HasCompany;

    protected $fillable = [
        'company_id',
        'user_id',
        'item_id',
        'warehouse_id',
        'quantity',
        'type',
        'note',
        'reference',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
