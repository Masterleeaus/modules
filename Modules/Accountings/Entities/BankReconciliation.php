<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class BankReconciliation extends BaseModel
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_bank_reconciliations';
    protected $guarded = ['id'];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function lines()
    {
        return $this->hasMany(BankReconciliationLine::class, 'reconciliation_id');
    }
}
