<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Accountings\Traits\CompanyScoped;

class RecurringExpense extends Model
{
    use CompanyScoped;
    protected $table = 'acc_recurring_expenses';

    protected $fillable = [
        'name','coa_id','amount','frequency','day_of_month','day_of_week','is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}