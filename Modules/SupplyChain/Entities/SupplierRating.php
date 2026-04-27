<?php

namespace Modules\SupplyChain\Entities;

use App\Models\BaseModel;
use App\Models\User;

class SupplierRating extends BaseModel
{
    protected $table = 'supplier_ratings';
    protected $guarded = ['id'];

    protected $casts = [
        'rated_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function ratedBy()
    {
        return $this->belongsTo(User::class, 'rated_by');
    }
}
