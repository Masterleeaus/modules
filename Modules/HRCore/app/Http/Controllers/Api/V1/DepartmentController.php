<?php

namespace Modules\HRCore\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\HRCore\app\Models\Department;

/**
 * @OA\Tag(
 *     name="Organization",
 *     description="Organization structure endpoints"
 * )
 */
class DepartmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/essapp/v1/organization/departments",
     *     summary="Get all departments",
     *     tags={"Organization"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Departments retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Departments retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Engineering"),
     *                     @OA\Property(property="code", type="string", example="ENG"),
     *                     @OA\Property(property="description", type="string", example="Engineering Department"),
     *                     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
     *                     @OA\Property(property="parent_name", type="string", nullable=true, example=null),
     *                     @OA\Property(property="head_id", type="integer", nullable=true, example=5),
     *                     @OA\Property(property="head_name", type="string", nullable=true, example="John Doe"),
     *                     @OA\Property(property="employee_count", type="integer", example=25),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="location", type="string", nullable=true, example="Building A"),
     *                     @OA\Property(property="email", type="string", nullable=true, example="engineering@company.com"),
     *                     @OA\Property(property="phone", type="string", nullable=true, example="+1234567890")
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
        // Cache departments for better performance
        $departments = Cache::remember('mobile_departments_list', 3600, function () {
            return Department::with(['parentDepartment:id,name'])
                ->where('status', \App\Enums\Status::ACTIVE)
                ->get()
                ->map(function ($department) {
                    return [
                        'id' => $department->id,
                        'name' => $department->name,
                        'code' => $department->code,
                        'description' => $department->description,
                        'parent_id' => $department->parent_id,
                        'parent_name' => $department->parentDepartment?->name,
                        'employee_count' => $department->employees_count ?? 0,
                        'is_active' => true,
                        'location' => $department->location,
                        'email' => $department->email,
                        'phone' => $department->phone,
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'message' => 'Departments retrieved successfully',
            'data' => $departments,
        ]);
    }
}
