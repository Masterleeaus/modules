<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class JobCost extends Model
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_job_costs';

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
