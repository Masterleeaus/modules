<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class Receipt extends Model
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_receipts';

    protected $guarded = ['id'];
}
