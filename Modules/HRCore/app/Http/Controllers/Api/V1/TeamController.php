<?php

namespace Modules\HRCore\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\HRCore\app\Models\Department;

/**
 * @OA\Tag(
 *     name="Organization",
 *     description="Organization structure endpoints"
 * )
 */
class TeamController extends Controller
{
    /**
     * @OA\Get(
     *     path="/essapp/v1/organization/teams",
     *     summary="Get teams list",
     *     tags={"Organization"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Teams retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Teams retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="my_team", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Engineering Team"),
     *                     @OA\Property(property="department_id", type="integer", example=1),
     *                     @OA\Property(property="department_name", type="string", example="Engineering"),
     *                     @OA\Property(property="manager_id", type="integer", example=5),
     *                     @OA\Property(property="manager_name", type="string", example="John Doe"),
     *                     @OA\Property(property="member_count", type="integer", example=8)
     *                 ),
     *                 @OA\Property(property="other_teams", type="array",
     *
     *                     @OA\Items(
     *
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Marketing Team"),
     *                         @OA\Property(property="department_name", type="string", example="Marketing"),
     *                         @OA\Property(property="manager_name", type="string", example="Jane Smith"),
     *                         @OA\Property(property="member_count", type="integer", example=5)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        // Since users don't have department_id, return departments as teams
        $teams = Cache::remember('mobile_teams_list', 3600, function () {
            return Department::query()
                ->where('status', \App\Enums\Status::ACTIVE)
                ->get()
                ->map(function ($department) {
                    // Simplified - just return department info as team
                    return [
                        'id' => $department->id,
                        'name' => $department->name.' Team',
                        'code' => $department->code,
                        'department_name' => $department->name,
                        'manager_id' => null,
                        'manager_name' => null,
                        'member_count' => 0, // No department_id in users table
                        'is_member' => false,
                        'is_lead' => false,
                    ];
                })->values();
        });

        return response()->json([
            'success' => true,
            'message' => 'Teams retrieved successfully',
            'data' => $teams,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/essapp/v1/organization/teams/{id}/members",
     *     summary="Get team members",
     *     tags={"Organization"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Team/Department ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Team members retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Team members retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="team", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Engineering Team"),
     *                     @OA\Property(property="department_name", type="string", example="Engineering")
     *                 ),
     *                 @OA\Property(property="members", type="array",
     *
     *                     @OA\Items(
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="employee_code", type="string", example="EMP001"),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="email", type="string", example="john@example.com"),
     *                         @OA\Property(property="phone", type="string", example="+1234567890"),
     *                         @OA\Property(property="designation", type="string", example="Software Engineer"),
     *                         @OA\Property(property="avatar", type="string", nullable=true),
     *                         @OA\Property(property="is_manager", type="boolean", example=false),
     *                         @OA\Property(property="status", type="string", example="active")
     *                     )
     *                 ),
     *                 @OA\Property(property="summary", type="object",
     *                     @OA\Property(property="total_members", type="integer", example=8),
     *                     @OA\Property(property="active_members", type="integer", example=7),
     *                     @OA\Property(property="on_leave", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Team not found"
     *     )
     * )
     */
    public function members($id): JsonResponse
    {
        $department = Department::find($id);

        if (! $department) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found',
            ], 404);
        }

        // Since users don't have department_id, return empty team for now
        // In a real app, you would have a proper team/department relationship
        $members = [];

        return response()->json([
            'success' => true,
            'message' => 'Team members retrieved successfully',
            'data' => [
                'team' => [
                    'id' => $department->id,
                    'name' => $department->name.' Team',
                    'department_name' => $department->name,
                ],
                'members' => $members,
                'summary' => [
                    'total_members' => 0,
                    'active_members' => 0,
                    'on_leave' => 0,
                ],
            ],
        ]);
    }

    /**
     * Get user's current status (active, on_leave, etc.)
     */
    private function getUserStatus($user): string
    {
        // Check if user is on leave today
        $onLeave = DB::table('leave_requests')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('from_date', '<=', now())
            ->whereDate('to_date', '>=', now())
            ->exists();

        if ($onLeave) {
            return 'on_leave';
        }

        return 'active';
    }
}
