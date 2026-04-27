<?php

namespace Modules\HRCore\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\HRCore\app\Models\Shift;

/**
 * @OA\Tag(
 *     name="Organization",
 *     description="Organization structure endpoints"
 * )
 */
class ShiftController extends Controller
{
    /**
     * @OA\Get(
     *     path="/essapp/v1/organization/shifts",
     *     summary="Get all shifts",
     *     tags={"Organization"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Shifts retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shifts retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Day Shift"),
     *                     @OA\Property(property="code", type="string", example="DS"),
     *                     @OA\Property(property="start_time", type="string", example="09:00"),
     *                     @OA\Property(property="end_time", type="string", example="18:00"),
     *                     @OA\Property(property="break_duration", type="integer", example=60),
     *                     @OA\Property(property="working_hours", type="number", example=8),
     *                     @OA\Property(property="is_night_shift", type="boolean", example=false),
     *                     @OA\Property(property="is_flexible", type="boolean", example=false),
     *                     @OA\Property(property="is_default", type="boolean", example=true),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="week_offs", type="array", @OA\Items(type="string"), example={"Saturday", "Sunday"}),
     *                     @OA\Property(property="color", type="string", example="#4CAF50"),
     *                     @OA\Property(property="description", type="string", example="Regular day shift")
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
        // Cache shifts for better performance
        $shifts = Cache::remember('mobile_shifts_list', 3600, function () {
            return Shift::where('status', \App\Enums\Status::ACTIVE)
                ->get()
                ->map(function ($shift) {
                    return [
                        'id' => $shift->id,
                        'name' => $shift->name,
                        'code' => $shift->code,
                        'start_time' => $shift->start_time ? Carbon::parse($shift->start_time)->format('H:i') : null,
                        'end_time' => $shift->end_time ? Carbon::parse($shift->end_time)->format('H:i') : null,
                        'break_duration' => $shift->break_duration, // in minutes
                        'working_hours' => $shift->working_hours,
                        'is_night_shift' => $shift->is_night_shift ?? false,
                        'is_flexible' => $shift->is_flexible ?? false,
                        'is_default' => $shift->is_default ?? false,
                        'is_active' => true,
                        'week_offs' => $shift->week_offs ?? [],
                        'color' => $shift->color ?? '#4CAF50',
                        'description' => $shift->description,
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'message' => 'Shifts retrieved successfully',
            'data' => $shifts,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/essapp/v1/organization/shifts/my-schedule",
     *     summary="Get my shift schedule",
     *     tags={"Organization"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         description="Month (1-12)",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Year",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Shift schedule retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shift schedule retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_shift", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Day Shift"),
     *                     @OA\Property(property="start_time", type="string", example="09:00"),
     *                     @OA\Property(property="end_time", type="string", example="18:00")
     *                 ),
     *                 @OA\Property(property="schedule", type="array",
     *
     *                     @OA\Items(
     *
     *                         @OA\Property(property="date", type="string", example="2024-01-15"),
     *                         @OA\Property(property="shift_id", type="integer", example=1),
     *                         @OA\Property(property="shift_name", type="string", example="Day Shift"),
     *                         @OA\Property(property="start_time", type="string", example="09:00"),
     *                         @OA\Property(property="end_time", type="string", example="18:00"),
     *                         @OA\Property(property="is_week_off", type="boolean", example=false),
     *                         @OA\Property(property="is_holiday", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="summary", type="object",
     *                     @OA\Property(property="total_working_days", type="integer", example=22),
     *                     @OA\Property(property="total_week_offs", type="integer", example=8),
     *                     @OA\Property(property="total_holidays", type="integer", example=1)
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
    public function mySchedule(Request $request): JsonResponse
    {
        $user = auth()->user();
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Get default shift (first active shift)
        $defaultShift = Shift::where('status', \App\Enums\Status::ACTIVE)
            ->first();

        // Generate schedule for the month
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $schedule = [];
        $workingDays = 0;
        $weekOffs = 0;
        $holidays = 0;

        if ($defaultShift) {
            // Check which days are working days based on shift columns
            $workingDaysOfWeek = [];
            if ($defaultShift->sunday) {
                $workingDaysOfWeek[] = 'Sunday';
            }
            if ($defaultShift->monday) {
                $workingDaysOfWeek[] = 'Monday';
            }
            if ($defaultShift->tuesday) {
                $workingDaysOfWeek[] = 'Tuesday';
            }
            if ($defaultShift->wednesday) {
                $workingDaysOfWeek[] = 'Wednesday';
            }
            if ($defaultShift->thursday) {
                $workingDaysOfWeek[] = 'Thursday';
            }
            if ($defaultShift->friday) {
                $workingDaysOfWeek[] = 'Friday';
            }
            if ($defaultShift->saturday) {
                $workingDaysOfWeek[] = 'Saturday';
            }

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dayName = $date->format('l');
                $isWeekOff = ! in_array($dayName, $workingDaysOfWeek);
                $isHoliday = false; // TODO: Check against holiday calendar

                $schedule[] = [
                    'date' => $date->format('Y-m-d'),
                    'shift_id' => $defaultShift->id,
                    'shift_name' => $defaultShift->name,
                    'start_time' => $defaultShift->start_time ? Carbon::parse($defaultShift->start_time)->format('H:i') : null,
                    'end_time' => $defaultShift->end_time ? Carbon::parse($defaultShift->end_time)->format('H:i') : null,
                    'is_week_off' => $isWeekOff,
                    'is_holiday' => $isHoliday,
                ];

                if (! $isWeekOff && ! $isHoliday) {
                    $workingDays++;
                } elseif ($isWeekOff) {
                    $weekOffs++;
                } elseif ($isHoliday) {
                    $holidays++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Shift schedule retrieved successfully',
            'data' => [
                'current_shift' => $defaultShift ? [
                    'id' => $defaultShift->id,
                    'name' => $defaultShift->name,
                    'start_time' => $defaultShift->start_time ? Carbon::parse($defaultShift->start_time)->format('H:i') : null,
                    'end_time' => $defaultShift->end_time ? Carbon::parse($defaultShift->end_time)->format('H:i') : null,
                ] : null,
                'schedule' => $schedule,
                'summary' => [
                    'total_working_days' => $workingDays,
                    'total_week_offs' => $weekOffs,
                    'total_holidays' => $holidays,
                ],
            ],
        ]);
    }
}
