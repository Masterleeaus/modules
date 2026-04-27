<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class Expense extends Model
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_expenses';

    protected $guarded = ['id'];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class, 'tax_code_id');
    }

    public function serviceLine()
    {
        return $this->belongsTo(ServiceLine::class, 'service_line_id');
    }
}
