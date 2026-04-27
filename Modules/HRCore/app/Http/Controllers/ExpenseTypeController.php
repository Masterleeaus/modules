<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Responses\Error;
use App\Http\Responses\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\HRCore\app\Models\ExpenseType;
use Yajra\DataTables\Facades\DataTables;

class ExpenseTypeController extends Controller
{
    /**
     * Constructor with permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-expense-types', ['only' => ['index', 'indexAjax', 'show']]);
        $this->middleware('permission:hrcore.manage-expense-types', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
    }

    /**
     * Display expense types listing
     */
    public function index()
    {
        $pageData = [
            'title' => __('Expense Types'),
            'breadcrumbs' => [
                ['name' => __('Dashboard'), 'url' => route('dashboard')],
                ['name' => __('HR Core'), 'url' => route('hrcore.dashboard')],
                ['name' => __('Expense Types'), 'url' => '#'],
            ],
            'categories' => ExpenseType::getCategories(),
        ];

        return view('hrcore::expense-types.index', compact('pageData'));
    }

    /**
     * DataTable AJAX endpoint
     */
    public function indexAjax(Request $request)
    {
        $query = ExpenseType::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return DataTables::of($query)
            ->addColumn('max_amount', function ($model) {
                return $model->max_amount;
            })
            ->addColumn('requires_receipt', function ($model) {
                return $model->requires_receipt ?
                  '<span class="badge bg-label-info">Yes</span>' :
                  '<span class="badge bg-label-secondary">No</span>';
            })
            ->addColumn('status', function ($model) {
                $badgeClass = $model->status === Status::ACTIVE ? 'success' : 'secondary';

                return '<span class="badge bg-label-'.$badgeClass.'">'.ucfirst($model->status->value).'</span>';
            })
            ->addColumn('actions', function ($model) {
                $actions = [];

                if (Auth::user()->can('hrcore.manage-expense-types')) {
                    $actions[] = [
                        'label' => __('Edit'),
                        'icon' => 'bx bx-edit',
                        'onclick' => "editRecord({$model->id})",
                    ];

                    // Add Enable/Disable toggle
                    $toggleLabel = $model->status === Status::ACTIVE ? __('Disable') : __('Enable');
                    $toggleIcon = $model->status === Status::ACTIVE ? 'bx bx-block' : 'bx bx-check-circle';
                    $actions[] = [
                        'label' => $toggleLabel,
                        'icon' => $toggleIcon,
                        'onclick' => "toggleStatus({$model->id})",
                    ];

                    $actions[] = [
                        'label' => __('Delete'),
                        'icon' => 'bx bx-trash',
                        'onclick' => "deleteRecord({$model->id})",
                        'class' => 'text-danger',
                    ];
                }

                return view('components.datatable-actions', [
                    'id' => $model->id,
                    'actions' => $actions,
                ])->render();
            })
            ->rawColumns(['status', 'requires_receipt', 'actions'])
            ->make(true);
    }

    /**
     * Show single expense type
     */
    public function show($id)
    {
        $expenseType = ExpenseType::findOrFail($id);

        return Success::response($expenseType);
    }

    /**
     * Store new expense type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('expense_types')->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'default_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:default_amount',
            'requires_receipt' => 'boolean',
            'requires_approval' => 'boolean',
            'gl_account_code' => 'nullable|string|max:50',
            'status' => ['required', Rule::enum(Status::class)],
        ]);

        if ($validator->fails()) {
            return Error::response([
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();
            $data['created_by_id'] = Auth::id();
            $data['updated_by_id'] = Auth::id();

            $expenseType = ExpenseType::create($data);

            DB::commit();

            return Success::response([
                'message' => __('Expense type created successfully'),
                'expenseType' => $expenseType,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create expense type', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Error::response(__('Failed to create expense type'));
        }
    }

    /**
     * Update expense type
     */
    public function update(Request $request, $id)
    {
        $expenseType = ExpenseType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('expense_types')->ignore($expenseType->id)->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'default_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:default_amount',
            'requires_receipt' => 'boolean',
            'requires_approval' => 'boolean',
            'gl_account_code' => 'nullable|string|max:50',
            'status' => ['required', Rule::enum(Status::class)],
        ]);

        if ($validator->fails()) {
            return Error::response([
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();
            $data['updated_by_id'] = Auth::id();

            $expenseType->update($data);

            DB::commit();

            return Success::response([
                'message' => __('Expense type updated successfully'),
                'expenseType' => $expenseType->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update expense type', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return Error::response(__('Failed to update expense type'));
        }
    }

    /**
     * Delete expense type
     */
    public function destroy($id)
    {
        $expenseType = ExpenseType::findOrFail($id);

        // Check if expense type has any requests
        if ($expenseType->expenseRequests()->exists()) {
            return Error::response(__('Cannot delete expense type with existing requests'));
        }

        try {
            $expenseType->delete();

            return Success::response([
                'message' => __('Expense type deleted successfully'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete expense type', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return Error::response(__('Failed to delete expense type'));
        }
    }

    /**
     * Toggle expense type status
     */
    public function toggleStatus($id)
    {
        try {
            $expenseType = ExpenseType::findOrFail($id);

            $newStatus = $expenseType->status === Status::ACTIVE ? Status::INACTIVE : Status::ACTIVE;
            $expenseType->update([
                'status' => $newStatus,
                'updated_by_id' => Auth::id(),
            ]);

            return Success::response([
                'message' => __('Status updated successfully'),
                'status' => $newStatus->value,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to toggle expense type status', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return Error::response(__('Failed to update status'));
        }
    }

    /**
     * Check code validation for uniqueness
     */
    public function checkCodeValidationAjax(Request $request)
    {
        $code = $request->get('code');
        $id = $request->get('id');

        if (empty($code)) {
            return response()->json(false);
        }

        $query = ExpenseType::where('code', $code)->whereNull('deleted_at');

        if ($id) {
            $query->where('id', '!=', $id);
        }

        $exists = $query->exists();

        return response()->json(! $exists);
    }
}
