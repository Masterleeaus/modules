<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class BankTransaction extends BaseModel
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_bank_transactions';
    protected $guarded = ['id'];

    protected $casts = [
        'txn_date' => 'date',
        'matched_at' => 'datetime',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }


    public function matches()
    {
        return $this->hasMany(BankTransactionMatch::class, 'bank_transaction_id');
    }
}
