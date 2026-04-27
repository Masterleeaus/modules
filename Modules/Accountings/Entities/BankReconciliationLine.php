<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class BankReconciliationLine extends BaseModel
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_bank_reconciliation_lines';
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    public function reconciliation()
    {
        return $this->belongsTo(BankReconciliation::class, 'reconciliation_id');
    }

    public function bankTransaction()
    {
        return $this->belongsTo(BankTransaction::class, 'bank_transaction_id');
    }
}
