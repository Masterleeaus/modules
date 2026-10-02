<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Signal extends Model
{
    use HasUuids;

    protected $table = 'signals';

    public $timestamps = false;

    const STATUS_PENDING    = 'pending';
    const STATUS_APPROVED   = 'approved';
    const STATUS_REJECTED   = 'rejected';
    const STATUS_DISPATCHED = 'dispatched';
    const STATUS_FAILED     = 'failed';

    const SOURCE_INTERNAL = 'internal';
    const SOURCE_WEBHOOK  = 'webhook';
    const SOURCE_AI       = 'ai';
    const SOURCE_DEVICE   = 'device';

    protected $fillable = [
        'id',
        'organization_id',
        'source',
        'type',
        'payload',
        'status',
        'created_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload'      => 'array',
            'created_at'   => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function dispatchLog(): HasMany
    {
        return $this->hasMany(SignalDispatchLog::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isDispatched(): bool
    {
        return $this->status === self::STATUS_DISPATCHED;
    }

    public function hasFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
