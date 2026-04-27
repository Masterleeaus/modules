<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\Enums\UserAccountStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HRNotificationService;
use App\Services\Settings\ModuleSettingsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\HRCore\app\Models\Attendance;
use Modules\HRCore\app\Models\AttendanceLog;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-attendance|hrcore.view-own-attendance')->only(['index', 'indexAjax']);
        $this->middleware('permission:hrcore.view-attendance')->only(['show']);
        $this->middleware('permission:hrcore.create-attendance')->only(['store']);
        $this->middleware('permission:hrcore.edit-attendance')->only(['edit', 'update']);
        $this->middleware('permission:hrcore.delete-attendance')->only(['destroy']);
        $this->middleware('permission:hrcore.web-check-in')->only(['webCheckIn']);
        $this->middleware('permission:hrcore.view-attendance-statistics')->only(['statistics']);
        $this->middleware('permission:hrcore.export-attendance')->only(['export']);
    }

    public function index()
    {
        $users = User::where('status', UserAccountStatus::ACTIVE)
            ->get();

        $attendances = Attendance::whereDate('date', Carbon::today())
            ->first();

        $logs = AttendanceLog::get();

        return view('hrcore::attendance.index', [
            'users' => $users,
            'attendances' => $attendances ?? [],
            'attendanceLogs' => $logs ?? [],
        ]);
    }

    public function indexAjax(Request $request)
    {
        $query = Attendance::query()
            ->with('attendanceLogs');

        // Apply permission-based filtering
        if (auth()->user()->can('hrcore.view-own-attendance') && ! auth()->user()->can('hrcore.view-attendance')) {
            // User can only see their own attendance
            $query->where('user_id', auth()->id());
        } elseif (auth()->user()->hasRole('team-leader')) {
            // Team leader can see their team's attendance
            // This would need to be implemented based on your team structure
            // For now, we'll let them see all (you can customize this)
        }

        // User filter
        if ($request->has('userId') && $request->input('userId')) {
            Log::info('User ID: '.$request->input('userId'));
            $query->where('user_id', $request->input('userId'));
        }

        if ($request->has('date') && $request->input('date')) {
            Log::info('Date: '.$request->input('date'));
            $query->whereDate('date', $request->input('date'));
        } else {
            $query->whereDate('date', Carbon::today());
        }

        return DataTables::of($query)
            ->addColumn('id', function ($attendance) {
                return $attendance->id;
            })
            ->editColumn('check_in_time', function ($attendance) {
                if ($attendance->check_in_time) {
                    return Carbon::parse($attendance->check_in_time)->format('h:i A');
                }
                // Fallback to logs if not in attendance table
                $checkInAt = $attendance->attendanceLogs->where('type', 'check_in')->first();

                return $checkInAt ? $checkInAt->created_at->format('h:i A') : 'N/A';
            })
            ->editColumn('check_out_time', function ($attendance) {
                if ($attendance->check_out_time) {
                    return Carbon::parse($attendance->check_out_time)->format('h:i A');
                }
                // Fallback to logs if not in attendance table
                $checkOutAt = $attendance->attendanceLogs->where('type', 'check_out')->last();

                return $checkOutAt ? $checkOutAt->created_at->format('h:i A') : 'N/A';
            })
            ->addColumn('shift', function ($attendance) {
                return $attendance->shift ? $attendance->shift->name : 'N/A';
            })
            ->addColumn('status', function ($attendance) {
                return $attendance->status ?? 'present';
            })
            ->addColumn('user', function ($attendance) {
                return view('components.datatable-user', [
                    'user' => $attendance->user,
                    'showCode' => true,
                    'linkRoute' => 'hrcore.employees.show',
                ])->render();
            })
            ->addColumn('actions', function ($attendance) {
                $actions = [];

                // View details - if user has general view permission or can view own attendance
                if (auth()->user()->can('hrcore.view-attendance') ||
                   (auth()->user()->can('hrcore.view-own-attendance') && $attendance->user_id === auth()->id())) {
                    $actions[] = [
                        'label' => __('View Details'),
                        'icon' => 'bx bx-show',
                        'url' => route('hrcore.attendance.show', $attendance->id),
                    ];
                }

                // Edit attendance - only with proper permission
                if (auth()->user()->can('hrcore.edit-attendance')) {
                    $actions[] = [
                        'label' => __('Edit'),
                        'icon' => 'bx bx-edit',
                        'onclick' => "editAttendance({$attendance->id})",
                    ];
                }

                return view('components.datatable-actions', [
                    'id' => $attendance->id,
                    'actions' => $actions,
                ])->render();
            })
            ->rawColumns(['user', 'actions'])
            ->make(true);
    }

    /**
     * Display the specified attendance record
     */
    public function show($id)
    {
        $attendance = Attendance::with([
            'user.designation.department',
            'shift',
            'attendanceLogs',
        ])->findOrFail($id);

        // Check permissions - users can view own attendance or need general view permission
        if (! auth()->user()->can('hrcore.view-attendance') &&
            $attendance->user_id !== auth()->id() &&
            ! auth()->user()->can('hrcore.view-own-attendance')) {
            abort(403, __('You are not authorized to view this attendance record.'));
        }

        return view('hrcore::attendance.show', compact('attendance'));
    }

    /**
     * Display the web attendance page
     */
    public function webAttendance()
    {
        return view('hrcore::attendance.web-attendance');
    }

    /**
     * Show employee's own attendance records
     */
    public function myAttendance()
    {
        // Check permission - user must be able to view own attendance
        if (! auth()->user()->can('hrcore.view-own-attendance')) {
            abort(403, __('You are not authorized to view attendance records.'));
        }

        $user = auth()->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->with('attendanceLogs')
            ->orderBy('created_at', 'desc')
            ->get();

        $statistics = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
        ];

        return view('hrcore::attendance.my-attendance', compact('user', 'attendances', 'statistics'));
    }

    /**
     * Show regularization requests for employee (ESS - Employee Self Service)
     */
    public function regularization()
    {
        $user = auth()->user();

        // Get regularization requests for the logged-in employee only
        $regularizationRequests = \Illuminate\Support\Facades\DB::table('attendance_regularizations')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(); // Changed to get() for statistics calculation

        // Convert to collection with proper date and JSON handling
        $regularizationRequests = $regularizationRequests->map(function ($item) {
            $item->created_at = \Carbon\Carbon::parse($item->created_at);
            // Decode JSON fields
            if (is_string($item->attachments)) {
                $item->attachments = json_decode($item->attachments, true);
            }

            return $item;
        });

        return view('hrcore::attendance.regularization', compact('user', 'regularizationRequests'));
    }

    /**
     * Show attendance reports for employee
     */
    public function myReports()
    {
        $user = auth()->user();
        $startDate = now()->subMonths(3)->startOfMonth();
        $endDate = now()->endOfMonth();

        $monthlyStats = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $attendances = Attendance::where('user_id', $user->id)
                ->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->get();

            $monthlyStats[] = [
                'month' => $current->format('F Y'),
                'present' => $attendances->where('status', 'present')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'half_day' => $attendances->where('status', 'half_day')->count(),
                'total_hours' => $attendances->sum('total_hours'),
            ];

            $current->addMonth();
        }

        return view('hrcore::attendance.reports', compact('user', 'monthlyStats'));
    }

    /**
     * Get today's attendance status for the logged-in user
     */
    public function getTodayStatus()
    {
        $userId = auth()->id();
        $today = Carbon::today();

        try {
            // Check if multiple check-in/out is enabled
            $isMultipleCheckInEnabled = $this->isMultipleCheckInEnabled();

            // Get today's attendance record
            $attendance = Attendance::where('user_id', $userId)
                ->whereDate('date', $today)
                ->with('attendanceLogs')
                ->first();

            if (! $attendance) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'hasCheckedIn' => false,
                        'hasCheckedOut' => false,
                        'checkInTime' => null,
                        'checkOutTime' => null,
                        'logs' => [],
                        'isMultipleCheckInEnabled' => $isMultipleCheckInEnabled,
                        'canCheckIn' => true,
                    ],
                ]);
            }

            // Get check-in and check-out logs
            $checkInLog = $attendance->attendanceLogs->where('type', 'check_in')->first();
            $checkOutLog = $attendance->attendanceLogs->where('type', 'check_out')->last();
            $lastLog = $attendance->attendanceLogs->sortByDesc('created_at')->first();

            // Determine if user can check in again
            $canCheckIn = true;
            if (! $isMultipleCheckInEnabled && $checkOutLog) {
                $canCheckIn = false;
            }

            // Format logs for display
            $logs = $attendance->attendanceLogs->map(function ($log) {
                return [
                    'type' => $log->type,
                    'created_at' => $log->created_at->toISOString(),
                ];
            })->values()->toArray();

            // Get check-in and check-out times from attendance record or logs
            $checkInTime = null;
            $checkOutTime = null;

            // Log attendance record data for debugging
            Log::info('Attendance Record Data:', [
                'attendance_id' => $attendance->id,
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'checkInLog_exists' => (bool) $checkInLog,
                'checkOutLog_exists' => (bool) $checkOutLog,
                'checkInLog_created_at' => $checkInLog ? $checkInLog->created_at->toISOString() : null,
                'checkOutLog_created_at' => $checkOutLog ? $checkOutLog->created_at->toISOString() : null,
            ]);

            if ($attendance->check_in_time) {
                // Check if check_in_time is already a full datetime or just time
                $checkInTimeStr = $attendance->check_in_time;
                if (strpos($checkInTimeStr, ':') !== false && strlen($checkInTimeStr) <= 8) {
                    // It's just a time (HH:MM:SS format), add today's date
                    $checkInTime = Carbon::parse($today->format('Y-m-d').' '.$checkInTimeStr)->toISOString();
                    Log::info('Parsed check-in time as time only:', ['original' => $checkInTimeStr, 'parsed' => $checkInTime]);
                } else {
                    // It's already a full datetime
                    $checkInTime = Carbon::parse($checkInTimeStr)->toISOString();
                    Log::info('Parsed check-in time as datetime:', ['original' => $checkInTimeStr, 'parsed' => $checkInTime]);
                }
            } elseif ($checkInLog) {
                // Fallback to log's created_at
                $checkInTime = $checkInLog->created_at->toISOString();
                Log::info('Using check-in log created_at:', ['checkInTime' => $checkInTime]);
            }

            if ($attendance->check_out_time) {
                // Check if check_out_time is already a full datetime or just time
                $checkOutTimeStr = $attendance->check_out_time;
                if (strpos($checkOutTimeStr, ':') !== false && strlen($checkOutTimeStr) <= 8) {
                    // It's just a time (HH:MM:SS format), add today's date
                    $checkOutTime = Carbon::parse($today->format('Y-m-d').' '.$checkOutTimeStr)->toISOString();
                    Log::info('Parsed check-out time as time only:', ['original' => $checkOutTimeStr, 'parsed' => $checkOutTime]);
                } else {
                    // It's already a full datetime
                    $checkOutTime = Carbon::parse($checkOutTimeStr)->toISOString();
                    Log::info('Parsed check-out time as datetime:', ['original' => $checkOutTimeStr, 'parsed' => $checkOutTime]);
                }
            } elseif ($checkOutLog) {
                // Fallback to log's created_at
                $checkOutTime = $checkOutLog->created_at->toISOString();
                Log::info('Using check-out log created_at:', ['checkOutTime' => $checkOutTime]);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'hasCheckedIn' => (bool) $checkInLog || ! empty($attendance->check_in_time),
                    'hasCheckedOut' => (bool) $checkOutLog || ! empty($attendance->check_out_time),
                    'checkInTime' => $checkInTime,
                    'checkOutTime' => $checkOutTime,
                    'logs' => $logs,
                    'isMultipleCheckInEnabled' => $isMultipleCheckInEnabled,
                    'canCheckIn' => $canCheckIn,
                    'lastLogType' => $lastLog ? $lastLog->type : null,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Get today status error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('An error occurred while fetching attendance status.'),
            ], 500);
        }
    }

    /**
     * Handle web check-in/check-out
     */
    public function webCheckIn(Request $request)
    {
        $userId = auth()->id();
        $date = $request->input('date', Carbon::today()->toDateString());
        $time = $request->input('time');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        try {
            // Check if multiple check-in/out is enabled for user's role
            $isMultipleCheckInEnabled = $this->isMultipleCheckInEnabled();

            // Check if attendance record exists for today
            $attendance = Attendance::where('user_id', $userId)
                ->whereDate('date', $date)
                ->first();

            if (! $attendance) {
                // Create new attendance record
                $attendance = Attendance::create([
                    'user_id' => $userId,
                    'check_in_time' => Carbon::parse($date.' '.$time),
                    'shift_id' => auth()->user()->shift_id,
                    'date' => $date,
                    'created_at' => Carbon::parse($date.' '.$time),
                    'updated_at' => now(),
                ]);
            }

            // Check last log to determine if this is check-in or check-out
            $lastLog = AttendanceLog::where('attendance_id', $attendance->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // Determine the type of action
            $type = (! $lastLog || $lastLog->type === 'check_out') ? 'check_in' : 'check_out';

            // Check if user can perform this action
            if (! $isMultipleCheckInEnabled && $lastLog && $lastLog->type === 'check_out') {
                return response()->json([
                    'status' => 'failed',
                    'data' => __('You have already checked out for today. Multiple check-ins are not allowed.'),
                ], 400);
            }

            // Update attendance record if checking out and multiple check-in is disabled
            if ($type === 'check_out' && ! $isMultipleCheckInEnabled) {
                $attendance->check_out_time = Carbon::parse($date.' '.$time);
                $attendance->save();
            }

            // Create attendance log
            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'user_id' => $userId,
                'date' => $date,
                'time' => $time,
                'logged_at' => Carbon::parse($date.' '.$time),
                'type' => $type,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'created_at' => Carbon::parse($date.' '.$time),
                'updated_at' => now(),
            ]);

            // Check for late check-in notification
            if ($type === 'check_in') {
                try {
                    $notificationService = app(HRNotificationService::class);

                    // Check if this is a late check-in (customize based on your shift logic)
                    $user = auth()->user();
                    $shift = $user->shift ?? $attendance->shift;
                    $expectedTime = $shift ? Carbon::parse($date.' '.$shift->start_time) : Carbon::parse($date.' 09:00:00');
                    $actualTime = Carbon::parse($date.' '.$time);

                    if ($actualTime->gt($expectedTime)) {
                        $minutesLate = $actualTime->diffInMinutes($expectedTime);
                        $notificationService->sendLateCheckInAlert($attendance, $minutesLate);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send late check-in notification: '.$e->getMessage());
                    // Don't fail the check-in if notification fails
                }
            }

            $message = $type === 'check_in'
              ? __('Checked in successfully')
              : __('Checked out successfully');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => $message,
                    'type' => $type,
                    'isMultipleCheckInEnabled' => $isMultipleCheckInEnabled,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Web check-in error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('An error occurred. Please try again.'),
            ], 500);
        }
    }

    /**
     * Check if multiple check-in/out is enabled for the user
     */
    private function isMultipleCheckInEnabled(): bool
    {
        // Check settings first
        $settingsService = app(ModuleSettingsService::class);
        $isEnabled = $settingsService->get('HRCore', 'is_multiple_check_in_enabled', true);

        // If setting is disabled, return false regardless of permission
        if (! $isEnabled) {
            return false;
        }

        // If enabled in settings, check user permission
        return auth()->user()->can('hrcore.multiple-check-in');
    }

    /**
     * Get global attendance status for floating indicator
     */
    public function getGlobalStatus()
    {
        $userId = auth()->id();
        $today = Carbon::today();

        try {
            // Get today's attendance record
            $attendance = Attendance::where('user_id', $userId)
                ->whereDate('date', $today)
                ->with('attendanceLogs')
                ->first();

            if (! $attendance) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'isCheckedIn' => false,
                        'showIndicator' => false,
                    ],
                ]);
            }

            $isMultipleCheckInEnabled = $this->isMultipleCheckInEnabled();
            $lastLog = $attendance->attendanceLogs->sortByDesc('created_at')->first();

            // Determine if user is currently checked in
            $isCurrentlyCheckedIn = false;
            if ($isMultipleCheckInEnabled) {
                $isCurrentlyCheckedIn = $lastLog && $lastLog->type === 'check_in';
            } else {
                $isCurrentlyCheckedIn = $attendance->check_in_time && ! $attendance->check_out_time;
            }

            $response = [
                'isCheckedIn' => $isCurrentlyCheckedIn,
                'showIndicator' => $isCurrentlyCheckedIn,
                'checkInTime' => null,
                'workingHours' => null,
            ];

            // Use the attendance table's check_in_time for calculation
            if ($attendance->check_in_time) {
                // Parse the check_in_time properly (it's in UTC format)
                $checkInTime = Carbon::parse($attendance->check_in_time);
                $response['checkInTime'] = $checkInTime->format('h:i A');

                // Calculate working hours if currently checked in
                if ($isCurrentlyCheckedIn) {
                    $now = Carbon::now();

                    // Calculate total minutes difference
                    $totalMinutes = abs($now->diffInMinutes($checkInTime));
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;

                    // Format as "HH:MM" for display in utilities panel
                    $response['workingHours'] = sprintf('%02d:%02d', $hours, $minutes);
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $response,
            ]);

        } catch (\Exception $e) {
            Log::error('Global status error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('An error occurred while fetching status.'),
            ], 500);
        }
    }

    /**
     * Get attendance statistics for the given date
     */
    public function statistics(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        // Get total active users
        $totalUsers = User::where('status', UserAccountStatus::ACTIVE)->count();

        // Get attendance records for the date
        $attendances = Attendance::whereDate('date', $date)
            ->with('attendanceLogs')
            ->get();

        $present = 0;
        $late = 0;
        $absent = 0;

        // Calculate statistics
        foreach ($attendances as $attendance) {
            $checkIn = $attendance->attendanceLogs->where('type', 'check_in')->first();

            if ($checkIn) {
                // Check if late (you can customize this logic based on shift times)
                $checkInTime = Carbon::parse($checkIn->created_at);
                $shiftStartTime = Carbon::parse($date.' 09:00:00'); // Default 9 AM, adjust as needed

                if ($checkInTime->gt($shiftStartTime)) {
                    $late++;
                } else {
                    $present++;
                }
            }
        }

        // Calculate absent count
        $checkedInUserIds = $attendances->pluck('user_id')->toArray();
        $absent = User::where('status', UserAccountStatus::ACTIVE)
            ->whereNotIn('id', $checkedInUserIds)
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $totalUsers,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'date' => $date,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified attendance
     */
    public function edit($id)
    {
        $attendance = Attendance::with([
            'user.designation.department',
            'shift',
            'attendanceLogs',
        ])->findOrFail($id);

        // Get times from attendance table first, fallback to logs
        $checkInTime = '';
        $checkOutTime = '';

        if ($attendance->check_in_time) {
            $checkInTime = Carbon::parse($attendance->check_in_time)->format('H:i');
        } else {
            // Fallback to log if attendance table doesn't have the time
            $checkIn = $attendance->attendanceLogs->where('type', 'check_in')->first();
            $checkInTime = $checkIn ? $checkIn->created_at->format('H:i') : '';
        }

        if ($attendance->check_out_time) {
            $checkOutTime = Carbon::parse($attendance->check_out_time)->format('H:i');
        } else {
            // Fallback to log if attendance table doesn't have the time
            $checkOut = $attendance->attendanceLogs->where('type', 'check_out')->last();
            $checkOutTime = $checkOut ? $checkOut->created_at->format('H:i') : '';
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'attendance' => $attendance,
                'checkInTime' => $checkInTime,
                'checkOutTime' => $checkOutTime,
                'date' => $attendance->date ? $attendance->date->format('Y-m-d') : $attendance->created_at->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Update the specified attendance in storage
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'status' => 'required|in:present,absent,late,half-day',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $attendance = Attendance::findOrFail($id);

            // Parse the times with the attendance date
            $checkInDateTime = Carbon::parse($attendance->date->format('Y-m-d').' '.$request->check_in_time);
            $checkOutDateTime = $request->filled('check_out_time')
                ? Carbon::parse($attendance->date->format('Y-m-d').' '.$request->check_out_time)
                : null;

            // Update attendance record with actual times
            $updateData = [
                'check_in_time' => $checkInDateTime,
                'check_out_time' => $checkOutDateTime,
                'status' => $request->status,
                'notes' => $request->notes,
            ];

            // Calculate hours if both check-in and check-out are present
            if ($checkInDateTime && $checkOutDateTime) {
                $attendance->check_in_time = $checkInDateTime;
                $attendance->check_out_time = $checkOutDateTime;
                $attendance->calculateHours();
                $updateData['working_hours'] = $attendance->working_hours;
                $updateData['late_hours'] = $attendance->late_hours;
                $updateData['early_hours'] = $attendance->early_hours;
            } elseif ($checkInDateTime && $attendance->shift) {
                // Calculate late hours for check-in only
                $attendance->check_in_time = $checkInDateTime;
                $lateMinutes = $attendance->getLateMinutesAttribute();
                $updateData['late_hours'] = round($lateMinutes / 60, 2);
            }

            $attendance->update($updateData);

            // Update check-in log
            $checkInLog = $attendance->attendanceLogs->where('type', 'check_in')->first();
            if ($checkInLog) {
                $checkInLog->update([
                    'time' => $request->check_in_time,
                    'logged_at' => $checkInDateTime,
                    'created_at' => $checkInDateTime,
                ]);
            } else {
                // Create check-in log if it doesn't exist
                AttendanceLog::create([
                    'attendance_id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'date' => $attendance->date,
                    'time' => $request->check_in_time,
                    'logged_at' => $checkInDateTime,
                    'type' => 'check_in',
                    'shift_id' => $attendance->shift_id,
                    'created_at' => $checkInDateTime,
                    'updated_at' => now(),
                ]);
            }

            // Update or create check-out log
            if ($request->filled('check_out_time')) {
                $checkOutLog = $attendance->attendanceLogs->where('type', 'check_out')->last();
                if ($checkOutLog) {
                    $checkOutLog->update([
                        'time' => $request->check_out_time,
                        'logged_at' => $checkOutDateTime,
                        'created_at' => $checkOutDateTime,
                    ]);
                } else {
                    AttendanceLog::create([
                        'attendance_id' => $attendance->id,
                        'user_id' => $attendance->user_id,
                        'date' => $attendance->date,
                        'time' => $request->check_out_time,
                        'logged_at' => $checkOutDateTime,
                        'type' => 'check_out',
                        'shift_id' => $attendance->shift_id,
                        'created_at' => $checkOutDateTime,
                        'updated_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => __('Attendance updated successfully'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance update error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to update attendance: '.$e->getMessage()),
            ], 500);
        }
    }
}
