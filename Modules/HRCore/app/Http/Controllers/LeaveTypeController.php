<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\HRCore\app\Models\LeaveType;
use Yajra\DataTables\Facades\DataTables;

class LeaveTypeController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-leave-types')->only(['index', 'indexAjax']);
        $this->middleware('permission:hrcore.view-leave-types')->only(['show']);
        $this->middleware('permission:hrcore.create-leave-types')->only(['create', 'store']);
        $this->middleware('permission:hrcore.edit-leave-types')->only(['edit', 'update']);
        $this->middleware('permission:hrcore.delete-leave-types')->only(['destroy']);
        $this->middleware('permission:hrcore.manage-leave-types')->only(['toggleStatus', 'checkCodeValidationAjax']);
    }

    /**
     * Display leave types listing page
     */
    public function index()
    {
        return view('hrcore::leave-types.index');
    }

    /**
     * DataTable server-side processing
     */
    public function indexAjax(Request $request)
    {
        $query = LeaveType::query();

        return DataTables::of($query)
            ->addColumn('is_proof_required', function ($leaveType) {
                return $leaveType->is_proof_required
                    ? '<span class="badge bg-label-success">Required</span>'
                    : '<span class="badge bg-label-secondary">Not Required</span>';
            })
            ->addColumn('status', function ($leaveType) {
                if ($leaveType->status instanceof Status) {
                    return $leaveType->status->badge();
                }

                try {
                    $status = Status::from($leaveType->status);

                    return $status->badge();
                } catch (\ValueError $e) {
                    return '<span class="badge bg-label-secondary">Unknown</span>';
                }
            })
            ->addColumn('actions', function ($leaveType) {
                $actions = [];

                // View action
                if (auth()->user()->can('hrcore.view-leave-types')) {
                    $actions[] = [
                        'label' => __('View'),
                        'icon' => 'bx bx-show',
                        'onclick' => "viewLeaveType({$leaveType->id})",
                    ];
                }

                // Edit action
                if (auth()->user()->can('hrcore.edit-leave-types')) {
                    $actions[] = [
                        'label' => __('Edit'),
                        'icon' => 'bx bx-edit',
                        'onclick' => "editLeaveType({$leaveType->id})",
                    ];
                }

                // Status toggle action
                if (auth()->user()->can('hrcore.manage-leave-types')) {
                    $actions[] = [
                        'label' => $leaveType->status === Status::ACTIVE ? __('Deactivate') : __('Activate'),
                        'icon' => $leaveType->status === Status::ACTIVE ? 'bx bx-x' : 'bx bx-check',
                        'onclick' => "toggleStatus({$leaveType->id})",
                    ];
                }

                // Delete action
                if (auth()->user()->can('hrcore.delete-leave-types')) {
                    if (! empty($actions)) {
                        $actions[] = ['divider' => true];
                    }
                    $actions[] = [
                        'label' => __('Delete'),
                        'icon' => 'bx bx-trash',
                        'onclick' => "deleteLeaveType({$leaveType->id})",
                    ];
                }

                return view('components.datatable-actions', [
                    'id' => $leaveType->id,
                    'actions' => $actions,
                ])->render();
            })
            ->rawColumns(['is_proof_required', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Store new leave type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code',
            'notes' => 'nullable|string|max:1000',
            'is_proof_required' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'data' => $validator->errors(),
            ], 422);
        }

        try {
            $leaveType = LeaveType::create([
                'name' => $request->name,
                'code' => $request->code,
                'notes' => $request->notes,
                'is_proof_required' => $request->boolean('is_proof_required'),
                'status' => Status::ACTIVE,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => ['message' => __('Leave type created successfully')],
            ]);

        } catch (Exception $e) {
            Log::error('Leave type creation failed: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to create leave type'),
            ], 500);
        }
    }

    /**
     * Show leave type details
     */
    public function show($id)
    {
        try {
            $leaveType = LeaveType::with(['createdBy', 'updatedBy'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $leaveType->id,
                    'name' => $leaveType->name,
                    'code' => $leaveType->code,
                    'notes' => $leaveType->notes,
                    'is_proof_required' => $leaveType->is_proof_required,
                    'status' => $leaveType->status->label(),
                    'status_raw' => $leaveType->status->value,
                    // Accrual settings
                    'is_accrual_enabled' => $leaveType->is_accrual_enabled,
                    'accrual_frequency' => $leaveType->accrual_frequency,
                    'accrual_rate' => $leaveType->accrual_rate,
                    'max_accrual_limit' => $leaveType->max_accrual_limit,
                    // Carry forward settings
                    'allow_carry_forward' => $leaveType->allow_carry_forward,
                    'max_carry_forward' => $leaveType->max_carry_forward,
                    'carry_forward_expiry_months' => $leaveType->carry_forward_expiry_months,
                    // Encashment settings
                    'allow_encashment' => $leaveType->allow_encashment,
                    'max_encashment_days' => $leaveType->max_encashment_days,
                    // Special type
                    'is_comp_off_type' => $leaveType->is_comp_off_type,
                    // Audit information
                    'created_by_name' => $leaveType->createdBy ? $leaveType->createdBy->full_name : null,
                    'created_at_formatted' => $leaveType->created_at ? $leaveType->created_at->format('M d, Y h:i A') : null,
                    'updated_by_name' => $leaveType->updatedBy ? $leaveType->updatedBy->full_name : null,
                    'updated_at_formatted' => $leaveType->updated_at ? $leaveType->updated_at->format('M d, Y h:i A') : null,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'data' => __('Leave type not found'),
            ], 404);
        }
    }

    /**
     * Show leave type edit form
     */
    public function edit($id)
    {
        try {
            $leaveType = LeaveType::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $leaveType->id,
                    'name' => $leaveType->name,
                    'code' => $leaveType->code,
                    'notes' => $leaveType->notes,
                    'is_proof_required' => $leaveType->is_proof_required,
                    'status' => $leaveType->status->value,
                    // Accrual settings
                    'is_accrual_enabled' => $leaveType->is_accrual_enabled,
                    'accrual_frequency' => $leaveType->accrual_frequency,
                    'accrual_rate' => $leaveType->accrual_rate,
                    'max_accrual_limit' => $leaveType->max_accrual_limit,
                    // Carry forward settings
                    'allow_carry_forward' => $leaveType->allow_carry_forward,
                    'max_carry_forward' => $leaveType->max_carry_forward,
                    'carry_forward_expiry_months' => $leaveType->carry_forward_expiry_months,
                    // Encashment settings
                    'allow_encashment' => $leaveType->allow_encashment,
                    'max_encashment_days' => $leaveType->max_encashment_days,
                    // Special type
                    'is_comp_off_type' => $leaveType->is_comp_off_type,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'data' => __('Leave type not found'),
            ], 404);
        }
    }

    /**
     * Update leave type
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code,'.$id,
            'notes' => 'nullable|string|max:1000',
            'is_proof_required' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'data' => $validator->errors(),
            ], 422);
        }

        try {
            $leaveType = LeaveType::findOrFail($id);

            $leaveType->update([
                'name' => $request->name,
                'code' => $request->code,
                'notes' => $request->notes,
                'is_proof_required' => $request->boolean('is_proof_required'),
            ]);

            return response()->json([
                'status' => 'success',
                'data' => ['message' => __('Leave type updated successfully')],
            ]);

        } catch (Exception $e) {
            Log::error('Leave type update failed: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to update leave type'),
            ], 500);
        }
    }

    /**
     * Delete leave type
     */
    public function destroy($id)
    {
        try {
            $leaveType = LeaveType::findOrFail($id);

            // Check if leave type is being used in any leave requests
            if ($leaveType->leaveRequests()->exists()) {
                return response()->json([
                    'status' => 'failed',
                    'data' => __('Cannot delete leave type as it is being used in leave requests'),
                ], 400);
            }

            $leaveType->delete();

            return response()->json([
                'status' => 'success',
                'data' => ['message' => __('Leave type deleted successfully')],
            ]);

        } catch (Exception $e) {
            Log::error('Leave type deletion failed: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to delete leave type'),
            ], 500);
        }
    }

    /**
     * Toggle leave type status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $leaveType = LeaveType::findOrFail($id);

            $leaveType->status = $leaveType->status === Status::ACTIVE ? Status::INACTIVE : Status::ACTIVE;
            $leaveType->save();

            return response()->json([
                'status' => 'success',
                'data' => ['message' => __('Leave type status updated successfully')],
            ]);

        } catch (Exception $e) {
            Log::error('Leave type status update failed: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to update leave type status'),
            ], 500);
        }
    }

    /**
     * Check if leave type code is unique
     */
    public function checkCodeValidationAjax(Request $request)
    {
        $code = $request->code;
        $id = $request->id;

        if (! $code) {
            return response()->json(['valid' => false]);
        }

        $query = LeaveType::where('code', $code);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        $exists = $query->exists();

        return response()->json(['valid' => ! $exists]);
    }
}
