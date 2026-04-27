<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class Bill extends Model
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_bills';

    protected $guarded = ['id'];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function lines()
    {
        return $this->hasMany(BillLine::class, 'bill_id');
    }

    public function payments()
    {
        return $this->hasMany(BillPayment::class, 'bill_id');
    }

    public function getPaidTotalAttribute(): float
    {
        return (float) ($this->payments()->sum('amount') ?? 0);
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, (float)$this->total - (float)$this->paid_total);
    }
}
