<?php

namespace Modules\TitanIntegrations\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\TitanIntegrations\Entities\ApiToken;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next, string $scope = null)
    {
        $bearer = $request->bearerToken();

        if (!$bearer) {
            return response()->json(['error' => 'No API token provided'], 401);
        }

        $token = ApiToken::findByToken($bearer);

        if (!$token) {
            return response()->json(['error' => 'Invalid API token'], 401);
        }

        if ($token->isExpired()) {
            return response()->json(['error' => 'API token has expired'], 401);
        }

        if ($scope && !$token->hasScope($scope)) {
            return response()->json(['error' => "Insufficient scope — requires {$scope}"], 403);
        }

        $token->touchLastUsed();

        // Inject company_id into request for controllers to use
        $request->attributes->set('api_company_id', $token->company_id);
        $request->attributes->set('api_token', $token);

        return $next($request);
    }
}
