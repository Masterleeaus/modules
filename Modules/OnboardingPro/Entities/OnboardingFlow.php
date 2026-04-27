<?php

namespace Modules\OnboardingPro\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingFlow extends Model
{
    protected $table = 'onboarding_flows';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Relations ──────────────────────────────────────────────────────────

    public function steps(): HasMany
    {
        return $this->hasMany(OnboardingFlowStep::class, 'flow_id')->orderBy('sort_order');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(OnboardingFlowCompletion::class, 'flow_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForJobType($query, ?string $jobType)
    {
        return $query->where(function ($q) use ($jobType) {
            $q->whereNull('job_type')->orWhere('job_type', $jobType);
        });
    }
}
