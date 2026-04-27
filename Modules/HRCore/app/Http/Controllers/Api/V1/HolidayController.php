<?php

namespace Modules\HRCore\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\HRCore\app\Models\Holiday;

/**
 * @OA\Tag(
 *     name="Organization",
 *     description="Organization structure endpoints"
 * )
 */
class HolidayController extends Controller
{
    /**
     * @OA\Get(
     *     path="/essapp/v1/organization/holidays",
     *     summary="Get holidays list",
     *     tags={"Organization"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Year to filter holidays",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Holidays retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Holidays retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="New Year's Day"),
     *                     @OA\Property(property="date", type="string", example="2024-01-01"),
     *                     @OA\Property(property="day", type="string", example="Monday"),
     *                     @OA\Property(property="type", type="string", example="public"),
     *                     @OA\Property(property="description", type="string", example="New Year celebration"),
     *                     @OA\Property(property="is_optional", type="boolean", example=false),
     *                     @OA\Property(property="applicable_for", type="array", @OA\Items(type="string"), example={"all"}),
     *                     @OA\Property(property="departments", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                     @OA\Property(property="locations", type="array", @OA\Items(type="string"), example={"New York", "Los Angeles"}),
     *                     @OA\Property(property="is_upcoming", type="boolean", example=true),
     *                     @OA\Property(property="days_remaining", type="integer", example=15)
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
    public function index(Request $request): JsonResponse
    {
        $year = $request->input('year', Carbon::now()->year);
        $user = auth()->user();

        // Cache holidays for the year
        $cacheKey = "mobile_holidays_{$year}_{$user->department_id}_{$user->location}";

        $holidays = Cache::remember($cacheKey, 3600, function () use ($year, $user) {
            // Get holidays for the year that are active and visible to employees
            $holidays = Holiday::active()
                ->visibleToEmployees()
                ->forYear($year)
                ->orderBy('date', 'asc')
                ->get()
                ->filter(function ($holiday) use ($user) {
                    // Use the model's method to check applicability
                    return $holiday->isApplicableFor($user);
                });

            return $holidays->map(function ($holiday) {
                $holidayDate = Carbon::parse($holiday->date);
                $today = Carbon::today();
                $isUpcoming = $holidayDate->isAfter($today);
                $daysRemaining = $isUpcoming ? $today->diffInDays($holidayDate) : 0;

                return [
                    'id' => $holiday->id,
                    'name' => $holiday->name,
                    'date' => $holiday->date->format('Y-m-d'),
                    'day' => $holiday->day,
                    'type' => $holiday->type,
                    'category' => $holiday->category,
                    'description' => $holiday->description,
                    'is_optional' => $holiday->is_optional,
                    'is_restricted' => $holiday->is_restricted,
                    'is_compensatory' => $holiday->is_compensatory,
                    'compensatory_date' => $holiday->compensatory_date?->format('Y-m-d'),
                    'is_half_day' => $holiday->is_half_day,
                    'half_day_type' => $holiday->half_day_type,
                    'half_day_start_time' => $holiday->half_day_start_time,
                    'half_day_end_time' => $holiday->half_day_end_time,
                    'color' => $holiday->color ?? '#4CAF50',
                    'image' => $holiday->image,
                    'is_upcoming' => $isUpcoming,
                    'days_remaining' => $daysRemaining,
                ];
            })->values();
        });

        return response()->json([
            'success' => true,
            'message' => 'Holidays retrieved successfully',
            'data' => $holidays,
        ]);
    }
}
