<?php

namespace Modules\OnboardingPro\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingFlowStep extends Model
{
    protected $table = 'onboarding_flow_steps';

    protected $guarded = ['id'];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order'  => 'integer',
    ];

    // ── Relations ──────────────────────────────────────────────────────────

    public function flow(): BelongsTo
    {
        return $this->belongsTo(OnboardingFlow::class, 'flow_id');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(OnboardingFlowCompletion::class, 'step_id');
    }
}
