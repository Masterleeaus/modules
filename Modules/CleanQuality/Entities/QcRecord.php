<?php

namespace Modules\CleanQuality\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CleanQuality\Traits\CompanyScoped;

class QcRecord extends Model
{
    use CompanyScoped;

    protected $table = 'qc_records';

    protected $fillable = [
        'company_id',
        'booking_id',
        'cleaner_id',
        'template_id',
        'schedule_id',
        'overall_score',
        'status',
        'reclean_triggered',
        'reclean_triggered_at',
        'reclean_job_id',
        'complaint_id',
        'notes',
        'inspected_by',
        'inspected_at',
    ];

    protected $casts = [
        'reclean_triggered'    => 'boolean',
        'reclean_triggered_at' => 'datetime',
        'inspected_at'         => 'datetime',
        'overall_score'        => 'integer',
    ];

    /**
     * Statuses available for a QC record.
     */
    public const STATUSES = ['pending', 'pass', 'fail', 'reclean_required', 'reclean_done'];

    public function items(): HasMany
    {
        return $this->hasMany(QcRecordItem::class, 'record_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InspectionTemplate::class, 'template_id');
    }

    public function cleaner(): BelongsTo
    {
        // User model lives in the core app namespace.
        return $this->belongsTo(\App\Models\User::class, 'cleaner_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'inspected_by');
    }

    /**
     * Whether the record's score is below the template's pass threshold (default 70).
     */
    public function isBelowThreshold(): bool
    {
        $threshold = $this->template?->pass_threshold ?? 70;

        return $this->overall_score < $threshold;
    }

    /**
     * Compute overall_score from weighted item scores and persist.
     */
    public function recalculateScore(): void
    {
        $items = $this->items;

        if ($items->isEmpty()) {
            return;
        }

        $totalWeight = $items->sum('weight');

        if ($totalWeight > 0) {
            $weighted = $items->sum(fn ($i) => $i->score * $i->weight);
            $this->overall_score = (int) round($weighted / $totalWeight);
        } else {
            $this->overall_score = (int) round($items->avg('score'));
        }

        $threshold          = $this->template?->pass_threshold ?? 70;
        $this->status       = $this->overall_score >= $threshold ? 'pass' : 'fail';
        $this->save();
    }
}
