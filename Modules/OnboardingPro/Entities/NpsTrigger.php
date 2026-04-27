<?php

namespace Modules\OnboardingPro\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NpsTrigger extends Model
{
    protected $table = 'onboarding_nps_triggers';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active'   => 'boolean',
        'delay_hours' => 'integer',
    ];

    // ── Relations ──────────────────────────────────────────────────────────

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
