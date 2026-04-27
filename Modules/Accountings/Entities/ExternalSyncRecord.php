<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;

class ExternalSyncRecord extends Model
{
    protected $table = 'external_sync_records';

    protected $fillable = [
        'organization_id',
        'syncable_type',
        'syncable_id',
        'provider',
        'external_id',
        'status',
        'last_error',
        'synced_at',
        'retry_count',
    ];

    protected $casts = [
        'synced_at'   => 'datetime',
        'retry_count' => 'integer',
    ];

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function markSynced(string $externalId): void
    {
        $this->update([
            'external_id' => $externalId,
            'status'      => 'synced',
            'last_error'  => null,
            'synced_at'   => now(),
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status'      => 'failed',
            'last_error'  => $error,
            'retry_count' => $this->retry_count + 1,
        ]);
    }
}
