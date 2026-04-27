<?php

namespace Modules\HRCore\app\Http\Controllers\Api\V1;

use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\HRCore\app\Http\Controllers\Api\BaseApiController;
use Modules\HRCore\app\Models\Attendance;
use Modules\HRCore\app\Models\AttendanceLog;

class AttendanceController extends BaseApiController
{
    public function checkIn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        // Check if multiple check-ins are allowed
        $setting = SystemSetting::where('key', 'allow_multiple_checkin')->first();
        $allowMultipleCheckin = $setting ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN) : false;

        DB::beginTransaction();
        try {
            // Check for existing attendance today
            $existingAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if ($existingAttendance) {
                // Check if already checked in and not checked out
                if ($existingAttendance->check_in_time && ! $existingAttendance->check_out_time) {
                    if (! $allowMultipleCheckin) {
                        DB::rollback();

                        return $this->errorResponse('You are already checked in. Please check out first.');
                    }
                }

                // If multiple check-ins allowed or if previously checked out, update the record
                $existingAttendance->check_in_time = $now;
                $existingAttendance->check_out_time = null;
                $existingAttendance->status = 'checked_in';
                $existingAttendance->notes = $request->notes;
                $existingAttendance->save();

                $attendance = $existingAttendance;
            } else {
                // Create new attendance record
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today,
                    'check_in_time' => $now,
                    'status' => 'checked_in',
                    'shift_id' => $user->shift_id,
                    'department_id' => $user->department_id,
                    'notes' => $request->notes,
                ]);
            }

            // Create attendance log
            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'date' => $today,
                'time' => $now->format('H:i:s'),
                'logged_at' => $now,
                'type' => 'check_in',
                'action_type' => 'api',
                'shift_id' => $user->shift_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->location_address,
                'device_type' => 'api',
                'ip_address' => $request->ip(),
                'verification_method' => 'api',
                'created_by_id' => $user->id,
            ]);

            DB::commit();

            return $this->successResponse([
                'attendance_id' => $attendance->id,
                'check_in_time' => $now->toIso8601String(),
                'status' => $attendance->status,
            ], 'Checked in successfully');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->errorResponse('Failed to check in', null, 500);
        }
    }

    public function checkOut(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        DB::beginTransaction();
        try {
            // Find today's attendance
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();

            if (! $attendance) {
                DB::rollback();

                return $this->errorResponse('You are not checked in. Please check in first.');
            }

            // Update attendance with check-out time
            $attendance->check_out_time = $now;
            $attendance->status = 'checked_out';

            // Calculate total hours worked
            if ($attendance->check_in_time) {
                $checkIn = Carbon::parse($attendance->check_in_time);
                $totalMinutes = $checkIn->diffInMinutes($now);
                $attendance->working_hours = round($totalMinutes / 60, 2);

                // Calculate overtime (assuming 8 hours standard work day)
                $standardHours = 8;
                $overtimeHours = max(0, $attendance->working_hours - $standardHours);
                $attendance->overtime_hours = round($overtimeHours, 2);
            }

            if ($request->notes) {
                $attendance->notes = $attendance->notes ?
                    $attendance->notes.' | Checkout: '.$request->notes :
                    'Checkout: '.$request->notes;
            }

            $attendance->save();

            // Create attendance log
            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'date' => $today,
                'time' => $now->format('H:i:s'),
                'logged_at' => $now,
                'type' => 'check_out',
                'action_type' => 'api',
                'shift_id' => $user->shift_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->location_address,
                'device_type' => 'api',
                'ip_address' => $request->ip(),
                'verification_method' => 'api',
                'created_by_id' => $user->id,
            ]);

            DB::commit();

            return $this->successResponse([
                'attendance_id' => $attendance->id,
                'check_out_time' => $now->toIso8601String(),
                'total_hours' => $attendance->working_hours,
                'overtime_hours' => $attendance->overtime_hours,
            ], 'Checked out successfully');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->errorResponse('Failed to check out', null, 500);
        }
    }

    public function status(): JsonResponse
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Get today's attendance
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $isCheckedIn = false;
        $checkInTime = null;
        $workingHours = null;

        if ($attendance) {
            $isCheckedIn = $attendance->check_in_time && ! $attendance->check_out_time;
            $checkInTime = $attendance->check_in_time;

            if ($isCheckedIn && $attendance->check_in_time) {
                $checkIn = Carbon::parse($attendance->check_in_time);
                $workingMinutes = $checkIn->diffInMinutes(Carbon::now());
                $workingHours = round($workingMinutes / 60, 2);
            }
        }

        $setting = SystemSetting::where('key', 'allow_multiple_checkin')->first();
        $allowMultipleCheckin = $setting ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN) : false;

        return $this->successResponse([
            'is_checked_in' => $isCheckedIn,
            'check_in_time' => $checkInTime,
            'check_out_time' => $attendance ? $attendance->check_out_time : null,
            'working_hours' => $workingHours,
            'total_hours' => $attendance ? $attendance->working_hours : null,
            'allow_multiple_checkin' => $allowMultipleCheckin,
            'status' => $attendance ? $attendance->status : 'absent',
        ], 'Attendance status retrieved');
    }

    public function history(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = Auth::user();

        // Default to last 30 days if no dates provided
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('date', 'desc');

        $attendances = $query->paginate(20);

        $data = $attendances->map(fn ($attendance) => [
            'id' => $attendance->id,
            'date' => $attendance->date->format('Y-m-d'),
            'check_in_time' => $attendance->check_in_time,
            'check_out_time' => $attendance->check_out_time,
            'total_hours' => $attendance->working_hours,
            'overtime_hours' => $attendance->overtime_hours,
            'status' => $attendance->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance history retrieved',
            'data' => $data,
            'meta' => [
                'current_page' => $attendances->currentPage(),
                'total' => $attendances->total(),
                'per_page' => $attendances->perPage(),
                'last_page' => $attendances->lastPage(),
            ],
        ]);
    }

    public function logs(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'nullable|date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = Auth::user();
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

        // Get attendance for the specified date
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if (! $attendance) {
            return $this->successResponse([], 'No attendance logs for this date');
        }

        // Get all logs for the attendance
        $logs = AttendanceLog::where('attendance_id', $attendance->id)
            ->where('user_id', $user->id)
            ->orderBy('logged_at', 'desc')
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'type' => $log->type,
                'timestamp' => $log->logged_at,
                'latitude' => $log->latitude,
                'longitude' => $log->longitude,
                'address' => $log->address,
            ]);

        return $this->successResponse($logs, 'Attendance logs retrieved');
    }
}
