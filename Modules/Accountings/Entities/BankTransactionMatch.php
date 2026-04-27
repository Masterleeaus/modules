<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class BankTransactionMatch extends BaseModel
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_bank_transaction_matches';
    protected $guarded = ['id'];

    public function transaction()
    {
        return $this->belongsTo(BankTransaction::class, 'bank_transaction_id');
    }
}
