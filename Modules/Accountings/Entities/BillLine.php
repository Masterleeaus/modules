<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class BillLine extends Model
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_bill_lines';

    protected $guarded = ['id'];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'line_subtotal' => 'decimal:2',
        'line_tax' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
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
