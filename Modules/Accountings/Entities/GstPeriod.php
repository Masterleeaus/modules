<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class GstPeriod extends BaseModel
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_gst_periods';

    protected $fillable = [
        'company_id',
        'user_id',
        'period_type',
        'period_start',
        'period_end',
        'gst_collected',
        'gst_paid',
        'net_gst',
        'status',
        'lodged_at',
        'locked_at',
    ];

    protected $casts = [
        'period_start'   => 'date',
        'period_end'     => 'date',
        'gst_collected'  => 'decimal:2',
        'gst_paid'       => 'decimal:2',
        'net_gst'        => 'decimal:2',
        'lodged_at'      => 'datetime',
        'locked_at'      => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function isLocked(): bool
    {
        return ! is_null($this->locked_at);
    }

    public function isLodged(): bool
    {
        return $this->status === 'lodged';
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeLodged($query)
    {
        return $query->where('status', 'lodged');
    }
}
