<?php

namespace Modules\HRCore\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\HRCore\app\Models\Designation;

/**
 * @OA\Tag(
 *     name="Organization",
 *     description="Organization structure endpoints"
 * )
 */
class DesignationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/essapp/v1/organization/designations",
     *     summary="Get all designations",
     *     tags={"Organization"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Designations retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Designations retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Software Engineer"),
     *                     @OA\Property(property="code", type="string", example="SE"),
     *                     @OA\Property(property="description", type="string", example="Software development role"),
     *                     @OA\Property(property="department_id", type="integer", example=1),
     *                     @OA\Property(property="department_name", type="string", example="Engineering"),
     *                     @OA\Property(property="level", type="string", example="L3"),
     *                     @OA\Property(property="grade", type="string", example="Senior"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="employee_count", type="integer", example=15),
     *                     @OA\Property(property="min_experience", type="integer", nullable=true, example=3),
     *                     @OA\Property(property="max_experience", type="integer", nullable=true, example=5)
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
        // Cache designations for better performance
        $designations = Cache::remember('mobile_designations_list', 3600, function () {
            return Designation::with(['department:id,name'])
                ->where('status', \App\Enums\Status::ACTIVE)
                ->get()
                ->map(function ($designation) {
                    return [
                        'id' => $designation->id,
                        'name' => $designation->name,
                        'code' => $designation->code,
                        'description' => $designation->description,
                        'department_id' => $designation->department_id,
                        'department_name' => $designation->department?->name,
                        'level' => $designation->level,
                        'grade' => $designation->grade,
                        'is_active' => true,
                        'employee_count' => $designation->employees_count ?? 0,
                        'min_experience' => $designation->min_experience,
                        'max_experience' => $designation->max_experience,
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'message' => 'Designations retrieved successfully',
            'data' => $designations,
        ]);
    }
}
