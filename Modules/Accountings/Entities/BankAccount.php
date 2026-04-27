<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class BankAccount extends BaseModel
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_bank_accounts';
    protected $guarded = ['id'];

    public function transactions()
    {
        return $this->hasMany(BankTransaction::class, 'bank_account_id');
    }
}
