<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\Enums\UserAccountStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\HRCore\app\Models\Attendance;
use Modules\HRCore\app\Models\AttendanceRegularization;
use Yajra\DataTables\Facades\DataTables;

class AttendanceDashboardController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-manager-attendance-dashboard')->only(['index', 'getStats', 'getTeamAttendance']);
        $this->middleware('permission:hrcore.view-attendance-regularization')->only(['getPendingRegularizations']);
    }

    /**
     * Display the manager attendance dashboard
     */
    public function index()
    {
        return view('hrcore::attendance.dashboard.index');
    }

    /**
     * Get attendance statistics for dashboard
     */
    public function getStats(Request $request)
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Get team members (you may need to adjust this based on your hierarchy logic)
        $teamMembers = $this->getTeamMembers();
        $teamMemberIds = $teamMembers->pluck('id');

        // Today's statistics
        $todayStats = [
            'present' => Attendance::whereIn('user_id', $teamMemberIds)
                ->whereDate('created_at', $today)
                ->whereNotNull('check_in_time')
                ->count(),
            'absent' => $teamMembers->count() - Attendance::whereIn('user_id', $teamMemberIds)
                ->whereDate('created_at', $today)
                ->whereNotNull('check_in_time')
                ->count(),
            'late' => Attendance::whereIn('user_id', $teamMemberIds)
                ->whereDate('created_at', $today)
                ->where('late_hours', '>', 0)
                ->count(),
            'early_departures' => Attendance::whereIn('user_id', $teamMemberIds)
                ->whereDate('created_at', $today)
                ->where('early_hours', '>', 0)
                ->count(),
        ];

        // Weekly statistics
        $weeklyStats = [
            'avg_attendance' => round(Attendance::whereIn('user_id', $teamMemberIds)
                ->where('created_at', '>=', $thisWeek)
                ->whereNotNull('check_in_time')
                ->count() / 7, 1),
            'total_hours' => Attendance::whereIn('user_id', $teamMemberIds)
                ->where('created_at', '>=', $thisWeek)
                ->whereNotNull('check_out_time')
                ->sum('working_hours') ?? 0,
        ];

        // Pending regularizations
        $pendingRegularizations = AttendanceRegularization::whereIn('user_id', $teamMemberIds)
            ->where('status', 'pending')
            ->count();

        // Monthly trends (last 30 days)
        $monthlyTrends = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $present = Attendance::whereIn('user_id', $teamMemberIds)
                ->whereDate('created_at', $date)
                ->whereNotNull('check_in_time')
                ->count();

            $monthlyTrends[] = [
                'date' => $date->format('M d'),
                'present' => $present,
                'percentage' => $teamMembers->count() > 0 ? round(($present / $teamMembers->count()) * 100, 1) : 0,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'today' => $todayStats,
                'weekly' => $weeklyStats,
                'pending_regularizations' => $pendingRegularizations,
                'monthly_trends' => $monthlyTrends,
                'team_size' => $teamMembers->count(),
            ],
        ]);
    }

    /**
     * Get current team attendance for real-time view
     */
    public function getTeamAttendance(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $teamMemberIds = $this->getTeamMembers()->pluck('id');

        $query = User::whereIn('id', $teamMemberIds)
            ->with([
                'attendances' => function ($q) use ($date) {
                    $q->whereDate('created_at', $date);
                },
                'designation.department',
            ]);

        return DataTables::of($query)
            ->addColumn('status', function ($user) {
                $attendance = $user->attendances->first();

                if (! $attendance || ! $attendance->check_in_time) {
                    return '<span class="badge bg-label-danger">Absent</span>';
                }

                if ($attendance->check_out_time) {
                    return '<span class="badge bg-label-success">Completed</span>';
                }

                return '<span class="badge bg-label-primary">Present</span>';
            })
            ->addColumn('check_in', function ($user) {
                $attendance = $user->attendances->first();
                if ($attendance && $attendance->check_in_time) {
                    $time = Carbon::parse($attendance->check_in_time)->format('H:i');
                    $badge = ($attendance->late_hours > 0) ? 'bg-label-warning' : 'bg-label-success';

                    return "<span class='badge {$badge}'>{$time}</span>";
                }

                return '-';
            })
            ->addColumn('check_out', function ($user) {
                $attendance = $user->attendances->first();
                if ($attendance && $attendance->check_out_time) {
                    $time = Carbon::parse($attendance->check_out_time)->format('H:i');
                    $badge = ($attendance->early_hours > 0) ? 'bg-label-warning' : 'bg-label-success';

                    return "<span class='badge {$badge}'>{$time}</span>";
                }

                return '-';
            })
            ->addColumn('total_hours', function ($user) {
                $attendance = $user->attendances->first();
                if ($attendance && $attendance->working_hours) {
                    return round($attendance->working_hours, 2).' hrs';
                }

                return '-';
            })
            ->addColumn('department', function ($user) {
                return $user->designation && $user->designation->department
                  ? $user->designation->department->name
                  : 'N/A';
            })
            ->addColumn('employee_info', function ($user) {
                return view('components.datatable-user', [
                    'user' => $user,
                    'showCode' => true,
                    'linkRoute' => 'hrcore.employees.show',
                ])->render();
            })
            ->rawColumns(['status', 'check_in', 'check_out', 'employee_info'])
            ->make(true);
    }

    /**
     * Get pending regularizations for manager review
     */
    public function getPendingRegularizations(Request $request)
    {
        $teamMembers = $this->getTeamMembers();

        $query = AttendanceRegularization::query()
            ->with(['user.designation.department'])
            ->whereIn('user_id', $teamMembers->pluck('id'))
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addColumn('employee_info', function ($regularization) {
                return view('components.datatable-user', [
                    'user' => $regularization->user,
                    'showCode' => true,
                    'linkRoute' => 'hrcore.employees.show',
                ])->render();
            })
            ->editColumn('date', function ($regularization) {
                return $regularization->date->format('M d, Y');
            })
            ->addColumn('type', function ($regularization) {
                return '<span class="badge bg-label-info">'.$regularization->getTypeLabel().'</span>';
            })
            ->addColumn('requested_times', function ($regularization) {
                $html = '';
                if ($regularization->requested_check_in_time) {
                    $html .= '<div><small class="text-muted">In:</small> '.$regularization->requested_check_in_time.'</div>';
                }
                if ($regularization->requested_check_out_time) {
                    $html .= '<div><small class="text-muted">Out:</small> '.$regularization->requested_check_out_time.'</div>';
                }

                return $html ?: '-';
            })
            ->addColumn('actions', function ($regularization) {
                $actions = [
                    [
                        'label' => __('View'),
                        'icon' => 'bx bx-show',
                        'onclick' => "viewRegularization({$regularization->id})",
                    ],
                    [
                        'label' => __('Approve'),
                        'icon' => 'bx bx-check',
                        'onclick' => "approveRegularization({$regularization->id})",
                        'class' => 'text-success',
                    ],
                    [
                        'label' => __('Reject'),
                        'icon' => 'bx bx-x',
                        'onclick' => "rejectRegularization({$regularization->id})",
                        'class' => 'text-danger',
                    ],
                ];

                return view('components.datatable-actions', [
                    'id' => $regularization->id,
                    'actions' => $actions,
                ])->render();
            })
            ->rawColumns(['employee_info', 'type', 'requested_times', 'actions'])
            ->make(true);
    }

    /**
     * Get attendance summary report
     */
    public function getAttendanceSummary(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $teamMembers = $this->getTeamMembers();
        if ($request->user_id) {
            $teamMembers = $teamMembers->where('id', $request->user_id);
        }

        $summary = [];
        foreach ($teamMembers as $user) {
            $attendances = Attendance::where('user_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $summary[] = [
                'user' => $user,
                'total_days' => $attendances->count(),
                'present_days' => $attendances->whereNotNull('check_in_time')->count(),
                'late_days' => $attendances->where('late_hours', '>', 0)->count(),
                'early_departures' => $attendances->where('early_hours', '>', 0)->count(),
                'total_hours' => round($attendances->sum('working_hours'), 2),
                'avg_hours' => round($attendances->where('working_hours', '>', 0)->avg('working_hours'), 2),
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $summary,
        ]);
    }

    /**
     * Get team members based on user's role and hierarchy
     * This is a simplified version - you may need to implement your own logic
     */
    private function getTeamMembers()
    {
        $user = auth()->user();

        // If user is admin or has view-all permissions, return all active users
        if ($user->hasRole('admin') || $user->can('hrcore.view-attendance')) {
            return User::where('status', UserAccountStatus::ACTIVE)
                ->with(['designation.department'])
                ->get();
        }

        // For managers, get their team members
        // This is a simplified approach - implement your hierarchy logic here
        return User::where('status', UserAccountStatus::ACTIVE)
            ->where('reporting_to_id', $user->id) // Using the correct field name
            ->orWhere('id', $user->id) // Include self
            ->with(['designation.department'])
            ->get();
    }
}
