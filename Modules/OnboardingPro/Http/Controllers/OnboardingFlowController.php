<?php

namespace Modules\OnboardingPro\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\OnboardingPro\Entities\OnboardingFlow;
use Modules\OnboardingPro\Entities\OnboardingFlowStep;
use Modules\OnboardingPro\Services\OnboardingFlowService;

class OnboardingFlowController extends AccountBaseController
{
    public function __construct(
        protected OnboardingFlowService $flowService
    ) {
        parent::__construct();
    }

    /**
     * Show active flows and current step for the authenticated user.
     */
    public function index(Request $request)
    {
        $user  = auth()->user();
        $type  = $request->input('type', 'staff');
        $flows = $this->flowService->getFlowsForUser($user, $type);

        $flowData = $flows->map(function (OnboardingFlow $flow) use ($user) {
            $incompleteSteps = $this->flowService->getIncompleteSteps($user, $flow);
            $totalSteps      = $flow->steps->count();
            $completedCount  = $totalSteps - $incompleteSteps->count();
            $progressPct     = $totalSteps > 0 ? (int) round(($completedCount / $totalSteps) * 100) : 100;

            return [
                'flow'             => $flow,
                'steps'            => $flow->steps,
                'incomplete_steps' => $incompleteSteps,
                'progress_pct'     => $progressPct,
                'is_complete'      => $this->flowService->isFlowComplete($user, $flow),
                'current_step'     => $incompleteSteps->first(),
            ];
        });

        return view('onboardingpro::flow.index', compact('flowData', 'type'));
    }

    /**
     * Mark a specific step as complete for the authenticated user.
     */
    public function complete(OnboardingFlowStep $step, Request $request)
    {
        $user = auth()->user();

        $this->flowService->completeStep($user, $step);

        $isFlowComplete = $this->flowService->isFlowComplete($user, $step->flow);

        return Reply::dataOnly([
            'step_id'         => $step->id,
            'flow_id'         => $step->flow_id,
            'is_flow_complete' => $isFlowComplete,
        ]);
    }

    /**
     * Admin: list all onboarding flows with completion stats.
     */
    public function adminIndex()
    {
        $this->pageTitle = 'Onboarding Flows';

        $flows = OnboardingFlow::withCount('completions')
            ->orderBy('sort_order')
            ->get();

        return view('onboardingpro::admin.flows.index', compact('flows'));
    }

    /**
     * Admin: create a new flow with its steps.
     */
    public function adminStore(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:staff,client',
            'job_type'    => 'nullable|string|max:50',
            'is_active'   => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
            'company_id'  => 'required|integer',
            'steps'       => 'nullable|array',
            'steps.*.step_type'    => 'required_with:steps|in:policy_accept,form,checklist,video,booking_wizard',
            'steps.*.title'        => 'required_with:steps|string|max:255',
            'steps.*.description'  => 'nullable|string',
            'steps.*.content'      => 'nullable|string',
            'steps.*.is_required'  => 'boolean',
            'steps.*.sort_order'   => 'nullable|integer|min:0',
        ]);

        $flow = OnboardingFlow::create([
            'company_id' => $data['company_id'],
            'name'       => $data['name'],
            'type'       => $data['type'],
            'job_type'   => $data['job_type'] ?? null,
            'is_active'  => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
            'created_by' => auth()->id(),
        ]);

        foreach ($data['steps'] ?? [] as $stepData) {
            $flow->steps()->create([
                'step_type'   => $stepData['step_type'],
                'title'       => $stepData['title'],
                'description' => $stepData['description'] ?? null,
                'content'     => $stepData['content'] ?? null,
                'is_required' => $stepData['is_required'] ?? true,
                'sort_order'  => $stepData['sort_order'] ?? 0,
            ]);
        }

        return Reply::dataOnly(['flow_id' => $flow->id]);
    }
}
