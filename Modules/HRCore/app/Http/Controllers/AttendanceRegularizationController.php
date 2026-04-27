<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\HRCore\app\Models\Attendance;
use Modules\HRCore\app\Models\AttendanceRegularization;
use Yajra\DataTables\Facades\DataTables;

class AttendanceRegularizationController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-attendance-regularization|hrcore.view-own-attendance-regularization')->only(['index', 'indexAjax']);
        $this->middleware('permission:hrcore.view-attendance-regularization')->only(['show']);
        $this->middleware('permission:hrcore.create-attendance-regularization')->only(['create', 'store']);
        $this->middleware('permission:hrcore.edit-attendance-regularization')->only(['edit', 'update']);
        $this->middleware('permission:hrcore.delete-attendance-regularization')->only(['destroy']);
        $this->middleware('permission:hrcore.approve-attendance-regularization')->only(['approve', 'reject']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hrcore::attendance.regularization.index');
    }

    /**
     * Get data for DataTables
     */
    public function indexAjax(Request $request)
    {
        $query = AttendanceRegularization::query()
            ->with(['user.designation.department', 'approvedBy']);

        // Apply permission-based filtering
        if (auth()->user()->can('hrcore.view-own-attendance-regularization') && ! auth()->user()->can('hrcore.view-attendance-regularization')) {
            $query->where('user_id', auth()->id());
        }

        // Apply filters
        if ($request->has('status') && $request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type') && $request->input('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('user_id') && $request->input('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('date_from') && $request->input('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to') && $request->input('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        return DataTables::of($query)
            ->addColumn('id', function ($regularization) {
                return $regularization->id;
            })
            ->addColumn('user', function ($regularization) {
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
            ->addColumn('status', function ($regularization) {
                return '<span class="badge '.$regularization->getStatusBadgeClass().'">'.$regularization->getStatusLabel().'</span>';
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
            ->addColumn('approved_by', function ($regularization) {
                if ($regularization->approvedBy) {
                    return $regularization->approvedBy->getFullName();
                }

                return '-';
            })
            ->addColumn('actions', function ($regularization) {
                $actions = [
                    [
                        'label' => __('View'),
                        'icon' => 'bx bx-show',
                        'onclick' => "viewRegularization({$regularization->id})",
                    ],
                ];

                if ($regularization->status === 'pending') {
                    if (auth()->user()->can('hrcore.edit-attendance-regularization') && $regularization->user_id === auth()->id()) {
                        $actions[] = [
                            'label' => __('Edit'),
                            'icon' => 'bx bx-edit',
                            'onclick' => "editRegularization({$regularization->id})",
                        ];
                    }

                    if (auth()->user()->can('hrcore.approve-attendance-regularization')) {
                        $actions[] = [
                            'label' => __('Approve'),
                            'icon' => 'bx bx-check',
                            'onclick' => "approveRegularization({$regularization->id})",
                            'class' => 'text-success',
                        ];
                        $actions[] = [
                            'label' => __('Reject'),
                            'icon' => 'bx bx-x',
                            'onclick' => "rejectRegularization({$regularization->id})",
                            'class' => 'text-danger',
                        ];
                    }
                }

                return view('components.datatable-actions', [
                    'id' => $regularization->id,
                    'actions' => $actions,
                ])->render();
            })
            ->rawColumns(['user', 'type', 'status', 'requested_times', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hrcore::attendance.regularization.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'type' => 'required|in:missing_checkin,missing_checkout,wrong_time,forgot_punch,other',
            'requested_check_in_time' => 'nullable|date_format:H:i',
            'requested_check_out_time' => 'nullable|date_format:H:i|after:requested_check_in_time',
            'reason' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Handle file uploads
                $attachments = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('attendance-regularization', 'public');
                        $attachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'type' => $file->getMimeType(),
                        ];
                    }
                }

                // Get existing attendance record if exists
                $attendance = Attendance::where('user_id', auth()->id())
                    ->whereDate('created_at', $request->date)
                    ->first();

                AttendanceRegularization::create([
                    'user_id' => auth()->id(),
                    'attendance_id' => $attendance?->id,
                    'date' => $request->date,
                    'type' => $request->type,
                    'requested_check_in_time' => $request->requested_check_in_time,
                    'requested_check_out_time' => $request->requested_check_out_time,
                    'actual_check_in_time' => $attendance?->check_in_time,
                    'actual_check_out_time' => $attendance?->check_out_time,
                    'reason' => $request->reason,
                    'attachments' => $attachments,
                    'status' => 'pending',
                ]);
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => __('Attendance regularization request submitted successfully'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance regularization creation error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to submit regularization request'),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $regularization = AttendanceRegularization::with([
            'user.designation.department',
            'attendance.attendanceLogs',
            'approvedBy',
        ])->findOrFail($id);

        // Check permissions
        if (! auth()->user()->can('hrcore.view-attendance-regularization') &&
            $regularization->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'regularization' => $regularization,
                'user' => $regularization->user,
                'attendance' => $regularization->attendance,
                'approved_by' => $regularization->approvedBy,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $regularization = AttendanceRegularization::findOrFail($id);

        // Check permissions
        if ($regularization->user_id !== auth()->id() || $regularization->status !== 'pending') {
            abort(403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $regularization,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $regularization = AttendanceRegularization::findOrFail($id);

        // Check permissions
        if ($regularization->user_id !== auth()->id() || $regularization->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'type' => 'required|in:missing_checkin,missing_checkout,wrong_time,forgot_punch,other',
            'requested_check_in_time' => 'nullable|date_format:H:i',
            'requested_check_out_time' => 'nullable|date_format:H:i|after:requested_check_in_time',
            'reason' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request, $regularization) {
                // Handle file uploads
                $attachments = $regularization->attachments ?? [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('attendance-regularization', 'public');
                        $attachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'type' => $file->getMimeType(),
                        ];
                    }
                }

                $regularization->update([
                    'date' => $request->date,
                    'type' => $request->type,
                    'requested_check_in_time' => $request->requested_check_in_time,
                    'requested_check_out_time' => $request->requested_check_out_time,
                    'reason' => $request->reason,
                    'attachments' => $attachments,
                ]);
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => __('Attendance regularization request updated successfully'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance regularization update error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to update regularization request'),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $regularization = AttendanceRegularization::findOrFail($id);

        // Check permissions
        if ($regularization->user_id !== auth()->id() || $regularization->status !== 'pending') {
            abort(403);
        }

        try {
            // Delete uploaded files
            if ($regularization->attachments) {
                foreach ($regularization->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }

            $regularization->delete();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => __('Attendance regularization request deleted successfully'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance regularization deletion error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to delete regularization request'),
            ], 500);
        }
    }

    /**
     * Approve a regularization request
     */
    public function approve(Request $request, $id)
    {
        $regularization = AttendanceRegularization::findOrFail($id);

        if ($regularization->status !== 'pending') {
            return response()->json([
                'status' => 'failed',
                'data' => __('This request has already been processed'),
            ], 400);
        }

        $request->validate([
            'manager_comments' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request, $regularization) {
                $regularization->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'manager_comments' => $request->manager_comments,
                ]);

                // Update attendance record if needed
                $this->updateAttendanceRecord($regularization);
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => __('Attendance regularization approved successfully'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance regularization approval error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to approve regularization request'),
            ], 500);
        }
    }

    /**
     * Reject a regularization request
     */
    public function reject(Request $request, $id)
    {
        $regularization = AttendanceRegularization::findOrFail($id);

        if ($regularization->status !== 'pending') {
            return response()->json([
                'status' => 'failed',
                'data' => __('This request has already been processed'),
            ], 400);
        }

        $request->validate([
            'manager_comments' => 'required|string|max:500',
        ]);

        try {
            $regularization->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'manager_comments' => $request->manager_comments,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => __('Attendance regularization rejected'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance regularization rejection error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to reject regularization request'),
            ], 500);
        }
    }

    /**
     * Update attendance record based on approved regularization
     */
    private function updateAttendanceRecord(AttendanceRegularization $regularization)
    {
        // Implementation depends on specific business logic
        // This is a placeholder for the actual attendance update logic
    }

    /**
     * Self-Service Methods
     */

    /**
     * Display my attendance regularization requests
     */
    public function myRegularizations()
    {
        return view('hrcore::attendance.regularization.my-index');
    }

    /**
     * Get my regularization requests for DataTables
     */
    public function myRegularizationsAjax(Request $request)
    {
        $query = AttendanceRegularization::query()
            ->where('user_id', auth()->id())
            ->with(['approvedBy']);

        // Apply filters
        if ($request->has('status') && $request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type') && $request->input('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('date_from') && $request->input('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to') && $request->input('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        return DataTables::of($query)
            ->addColumn('id', function ($regularization) {
                return $regularization->id;
            })
            ->editColumn('date', function ($regularization) {
                return $regularization->date->format('M d, Y');
            })
            ->addColumn('type', function ($regularization) {
                return '<span class="badge bg-label-info">'.$regularization->getTypeLabel().'</span>';
            })
            ->addColumn('status', function ($regularization) {
                return '<span class="badge '.$regularization->getStatusBadgeClass().'">'.$regularization->getStatusLabel().'</span>';
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
            ->addColumn('approved_by', function ($regularization) {
                if ($regularization->approvedBy) {
                    return $regularization->approvedBy->getFullName();
                }

                return '-';
            })
            ->addColumn('actions', function ($regularization) {
                $actions = [
                    [
                        'label' => __('View'),
                        'icon' => 'bx bx-show',
                        'onclick' => "viewMyRegularization({$regularization->id})",
                    ],
                ];

                if ($regularization->status === 'pending') {
                    $actions[] = [
                        'label' => __('Edit'),
                        'icon' => 'bx bx-edit',
                        'onclick' => "editMyRegularization({$regularization->id})",
                    ];
                    $actions[] = [
                        'label' => __('Delete'),
                        'icon' => 'bx bx-trash',
                        'onclick' => "deleteMyRegularization({$regularization->id})",
                        'class' => 'text-danger',
                    ];
                }

                return view('components.datatable-actions', [
                    'id' => $regularization->id,
                    'actions' => $actions,
                ])->render();
            })
            ->rawColumns(['type', 'status', 'requested_times', 'actions'])
            ->make(true);
    }

    /**
     * Show form for creating my regularization request
     */
    public function createMyRegularization()
    {
        return view('hrcore::attendance.regularization.my-create');
    }

    /**
     * Store my regularization request
     */
    public function storeMyRegularization(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'type' => 'required|in:missing_checkin,missing_checkout,wrong_time,forgot_punch,other',
            'requested_check_in_time' => 'nullable|date_format:H:i',
            'requested_check_out_time' => 'nullable|date_format:H:i|after:requested_check_in_time',
            'reason' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Handle file uploads
                $attachments = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('attendance-regularization', 'public');
                        $attachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'type' => $file->getMimeType(),
                        ];
                    }
                }

                // Get existing attendance record if exists
                $attendance = Attendance::where('user_id', auth()->id())
                    ->whereDate('created_at', $request->date)
                    ->first();

                AttendanceRegularization::create([
                    'user_id' => auth()->id(), // Always use auth()->id() for self-service
                    'attendance_id' => $attendance?->id,
                    'date' => $request->date,
                    'type' => $request->type,
                    'requested_check_in_time' => $request->requested_check_in_time,
                    'requested_check_out_time' => $request->requested_check_out_time,
                    'actual_check_in_time' => $attendance?->check_in_time,
                    'actual_check_out_time' => $attendance?->check_out_time,
                    'reason' => $request->reason,
                    'attachments' => $attachments,
                    'status' => 'pending',
                ]);
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => __('Attendance regularization request submitted successfully'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('My attendance regularization creation error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to submit regularization request'),
            ], 500);
        }
    }

    /**
     * Display my specific regularization request
     */
    public function showMyRegularization($id)
    {
        $regularization = AttendanceRegularization::with([
            'attendance.attendanceLogs',
            'approvedBy',
        ])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'regularization' => $regularization,
                'attendance' => $regularization->attendance,
                'approved_by' => $regularization->approvedBy,
            ],
        ]);
    }

    /**
     * Show form for editing my regularization request
     */
    public function editMyRegularization($id)
    {
        $regularization = AttendanceRegularization::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $regularization,
        ]);
    }

    /**
     * Update my regularization request
     */
    public function updateMyRegularization(Request $request, $id)
    {
        $regularization = AttendanceRegularization::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'type' => 'required|in:missing_checkin,missing_checkout,wrong_time,forgot_punch,other',
            'requested_check_in_time' => 'nullable|date_format:H:i',
            'requested_check_out_time' => 'nullable|date_format:H:i|after:requested_check_in_time',
            'reason' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request, $regularization) {
                // Handle file uploads
                $attachments = $regularization->attachments ?? [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('attendance-regularization', 'public');
                        $attachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'type' => $file->getMimeType(),
                        ];
                    }
                }

                $regularization->update([
                    'date' => $request->date,
                    'type' => $request->type,
                    'requested_check_in_time' => $request->requested_check_in_time,
                    'requested_check_out_time' => $request->requested_check_out_time,
                    'reason' => $request->reason,
                    'attachments' => $attachments,
                ]);
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => __('Attendance regularization request updated successfully'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('My attendance regularization update error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to update regularization request'),
            ], 500);
        }
    }

    /**
     * Delete my regularization request
     */
    public function deleteMyRegularization($id)
    {
        $regularization = AttendanceRegularization::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        try {
            // Delete uploaded files
            if ($regularization->attachments) {
                foreach ($regularization->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }

            $regularization->delete();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => __('Attendance regularization request deleted successfully'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('My attendance regularization deletion error: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to delete regularization request'),
            ], 500);
        }
    }
}
