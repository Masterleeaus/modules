<?php

namespace Modules\OnboardingPro\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingFlowCompletion extends Model
{
    protected $table = 'onboarding_flow_completions';

    protected $guarded = ['id'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function flow(): BelongsTo
    {
        return $this->belongsTo(OnboardingFlow::class, 'flow_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(OnboardingFlowStep::class, 'step_id');
    }
}
