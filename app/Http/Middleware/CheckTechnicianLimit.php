<?php

namespace App\Http\Middleware;

use App\Services\PlanService;
use Closure;
use Illuminate\Http\Request;
use Modules\TitanSolo\Services\SoloModeService;
use Symfony\Component\HttpFoundation\Response;

class CheckTechnicianLimit
{
    public function __construct(
        private readonly PlanService $planService,
        private readonly SoloModeService $soloModeService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->organization_id) {
            return $next($request);
        }

        $org = $user->organization;

        // Solo mode: hard cap of 1 technician — prompt to switch to Team mode instead
        if ($this->soloModeService->isSolo($org->id)) {
            $techCount = $this->planService->technicianCount($org);

            if ($techCount >= 1) {
                return back()->withErrors([
                    'technician_limit' => 'Solo mode allows only 1 technician (yourself). Switch to Team mode in Settings → Operation Mode to invite more team members.',
                ]);
            }

            return $next($request);
        }

        if ($this->planService->atTechnicianLimit($org)) {
            $limit = $this->planService->technicianLimit($org);
            $plan  = $this->planService->activePlan($org);

            return back()->withErrors([
                'technician_limit' => "Your {$plan} plan allows up to {$limit} technicians. Upgrade your plan to add more.",
            ]);
        }

        return $next($request);
    }
}
