<?php

namespace Modules\TitanSolo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\TitanSolo\Services\SoloModeService;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnforceSoloMode — gates team-only routes when the organisation is in solo mode.
 *
 * In solo mode the following are blocked:
 *   - Team management (inviting additional technicians)
 *   - Dispatch board
 *
 * Apply this middleware to routes that should be unavailable in solo mode.
 */
class EnforceSoloMode
{
    public function __construct(private readonly SoloModeService $soloModeService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->organization_id && $this->soloModeService->isSolo($user->organization_id)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This feature is not available in Solo mode. Upgrade to Team mode to unlock it.',
                ], 403);
            }

            return redirect()->route('owner.dashboard')
                ->with('error', 'This feature is not available in Solo mode. Switch to Team mode in Settings to unlock it.');
        }

        return $next($request);
    }
}
