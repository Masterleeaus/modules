<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;

class BasPeriod extends Model
{
    protected $table = 'bas_periods';

    protected $fillable = [
        'organization_id',
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
        'period_start'  => 'date',
        'period_end'    => 'date',
        'gst_collected' => 'decimal:2',
        'gst_paid'      => 'decimal:2',
        'net_gst'       => 'decimal:2',
        'lodged_at'     => 'datetime',
        'locked_at'     => 'datetime',
    ];

    public function isLocked(): bool
    {
        return ! is_null($this->locked_at);
    }

    public function isLodged(): bool
    {
        return $this->status === 'lodged';
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
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
