<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Accountings\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class BillPayment extends Model
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_bill_payments';

    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'date',
        'amount' => 'float',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
}
