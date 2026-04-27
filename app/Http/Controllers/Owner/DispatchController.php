<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DispatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class DispatchController extends Controller
{
    public function index(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $technicians = User::role('technician')
            ->where('organization_id', $orgId)
            ->get(['id', 'name']);

        return inertia('Owner/Dispatch/Map', [
            'technicians' => $technicians,
        ]);
    }

    /**
     * JSON endpoint: latest location + current job for each technician in the org.
     */
    public function technicianLocations(Request $request, DispatchService $dispatchService): JsonResponse
    {
        $result = $dispatchService->getActiveTechnicians($request->user()->organization_id);

        return response()->json($result);
    }

    /**
     * JSON endpoint: today's trail (location history) for a technician.
     */
    public function technicianTrail(Request $request, User $user, DispatchService $dispatchService): JsonResponse
    {
        abort_unless($user->organization_id === $request->user()->organization_id, 403);

        $points = $dispatchService->getTechnicianTrail($user->id);

        return response()->json(['data' => $points]);
    }
}
