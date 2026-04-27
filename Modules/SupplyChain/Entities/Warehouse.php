<?php

namespace Modules\SupplyChain\Entities;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes, HasCompany;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'address',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function transfersFrom()
    {
        return $this->hasMany(Transfer::class, 'from_warehouse_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(Transfer::class, 'to_warehouse_id');
    }
}
