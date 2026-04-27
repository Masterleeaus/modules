<?php

namespace Modules\ProShots\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobBatch extends Model
{
    protected $table = 'proshots_job_batches';

    protected $fillable = [
        'company_id',
        'job_ref',
        'title',
        'status',
        'total_photos',
        'completed_photos',
        'vault_proof_pack_id',
        'created_by',
    ];

    protected $casts = [
        'total_photos'     => 'integer',
        'completed_photos' => 'integer',
    ];

    // ── Relations ──────────────────────────────────────────────────────────

    public function photos(): HasMany
    {
        return $this->hasMany(UserPebblely::class, 'job_ref', 'job_ref');
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getProgressPercentAttribute(): int
    {
        if ($this->total_photos <= 0) {
            return 0;
        }

        return (int) round(($this->completed_photos / $this->total_photos) * 100);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }
}
