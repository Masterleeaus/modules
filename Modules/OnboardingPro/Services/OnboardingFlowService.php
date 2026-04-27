<?php

namespace Modules\OnboardingPro\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\OnboardingPro\Entities\NpsTrigger;
use Modules\OnboardingPro\Entities\OnboardingFlow;
use Modules\OnboardingPro\Entities\OnboardingFlowCompletion;
use Modules\OnboardingPro\Entities\OnboardingFlowStep;

class OnboardingFlowService
{
    /**
     * Get active flows for the given user type, filtered by job type when set.
     */
    public function getFlowsForUser(User $user, string $type): Collection
    {
        return OnboardingFlow::active()
            ->forType($type)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Return steps in the flow that the user has not yet completed.
     */
    public function getIncompleteSteps(User $user, OnboardingFlow $flow): Collection
    {
        $completedStepIds = OnboardingFlowCompletion::where('flow_id', $flow->id)
            ->where('user_id', $user->id)
            ->whereNotNull('step_id')
            ->pluck('step_id');

        return $flow->steps()
            ->whereNotIn('id', $completedStepIds)
            ->get();
    }

    /**
     * Record a step completion for the user.
     */
    public function completeStep(User $user, OnboardingFlowStep $step): void
    {
        OnboardingFlowCompletion::firstOrCreate(
            [
                'flow_id' => $step->flow_id,
                'user_id' => $user->id,
                'step_id' => $step->id,
            ],
            ['completed_at' => now()]
        );

        // If all required steps are now done, record whole-flow completion
        if ($this->isFlowComplete($user, $step->flow)) {
            OnboardingFlowCompletion::firstOrCreate(
                [
                    'flow_id' => $step->flow_id,
                    'user_id' => $user->id,
                    'step_id' => null,
                ],
                ['completed_at' => now()]
            );
        }
    }

    /**
     * Return true when all required steps in the flow are completed by the user.
     */
    public function isFlowComplete(User $user, OnboardingFlow $flow): bool
    {
        $requiredStepIds = $flow->steps()
            ->where('is_required', true)
            ->pluck('id');

        if ($requiredStepIds->isEmpty()) {
            return true;
        }

        $completedStepIds = OnboardingFlowCompletion::where('flow_id', $flow->id)
            ->where('user_id', $user->id)
            ->whereNotNull('step_id')
            ->pluck('step_id');

        return $requiredStepIds->diff($completedStepIds)->isEmpty();
    }

    /**
     * Find active NPS triggers for the company and dispatch a delayed survey notification.
     */
    public function triggerPostJobSurvey(string $jobRef, int $userId, int $companyId): void
    {
        $triggers = NpsTrigger::active()
            ->forCompany($companyId)
            ->where('trigger_event', 'job_completed')
            ->with('survey')
            ->get();

        foreach ($triggers as $trigger) {
            if (! $trigger->survey || ! $trigger->survey->active) {
                continue;
            }

            $surveyId    = $trigger->survey_id;
            $delayHours  = $trigger->delay_hours;

            dispatch(function () use ($userId, $surveyId, $jobRef) {
                $user = \App\Models\User::find($userId);

                if (! $user) {
                    return;
                }

                \Illuminate\Support\Facades\Log::info('OnboardingPro: NPS survey triggered', [
                    'user_id'   => $userId,
                    'survey_id' => $surveyId,
                    'job_ref'   => $jobRef,
                ]);
            })->delay(now()->addHours($delayHours));
        }
    }
}
