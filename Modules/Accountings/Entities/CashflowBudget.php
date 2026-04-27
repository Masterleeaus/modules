<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Accountings\Traits\CompanyScoped;

class CashflowBudget extends Model
{
    use CompanyScoped;
    protected $table = 'acc_cashflow_budgets';

    protected $fillable = ['name','expected_monthly_inflow','expected_monthly_outflow','is_active'];

    protected $casts = ['is_active' => 'boolean'];
}