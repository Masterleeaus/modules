<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Owner\SetupController;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class RequireSetupComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->organization_id) {
            return $next($request);
        }

        // Only applies to owners and admins
        if (! $user->hasRole(['owner', 'admin'])) {
            return $next($request);
        }

        if (SetupController::isComplete($user->organization_id)) {
            return $next($request);
        }

        if ($request->inertia()) {
            return Inertia::location(route('owner.setup'));
        }

        return redirect()->route('owner.setup');
    }
}
