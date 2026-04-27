<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\ApiClasses\Error;
use App\ApiClasses\Success;
use App\Enums\ExpenseRequestStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HRNotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\FileManagerCore\Contracts\FileManagerInterface;
use Modules\FileManagerCore\DTO\FileUploadRequest;
use Modules\FileManagerCore\Enums\FileType;
use Modules\FileManagerCore\Enums\FileVisibility;
use Modules\HRCore\app\Models\Department;
use Modules\HRCore\app\Models\ExpenseRequest;
use Modules\HRCore\app\Models\ExpenseType;
use Yajra\DataTables\DataTables;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-expenses|hrcore.view-own-expenses|hrcore.view-team-expenses', ['only' => ['index', 'indexAjax', 'show']]);
        $this->middleware('permission:hrcore.view-own-expenses', ['only' => ['myExpenses', 'myExpensesAjax']]);
        $this->middleware('permission:hrcore.create-expense', ['only' => ['create', 'store']]);
        $this->middleware('permission:hrcore.edit-expense', ['only' => ['edit', 'update']]);
        $this->middleware('permission:hrcore.delete-expense', ['only' => ['destroy']]);
        $this->middleware('permission:hrcore.approve-expense', ['only' => ['approve']]);
        $this->middleware('permission:hrcore.reject-expense', ['only' => ['reject']]);
        $this->middleware('permission:hrcore.process-expense', ['only' => ['process']]);
    }

    public function index()
    {
        // Get all users who can submit expenses (you may want to filter by role)
        $employees = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['super_admin']); // Exclude super admin if needed
        })->get();

        // Get active expense types
        $expenseTypes = ExpenseType::where('status', Status::ACTIVE->value)->get();

        return view('hrcore::expenses.index', compact('employees', 'expenseTypes'));
    }

    public function myExpenses()
    {
        // Get active expense types for the dropdown
        $expenseTypes = ExpenseType::where('status', Status::ACTIVE->value)->get();

        // Get current user's expense statistics
        $user = auth()->user();
        $statistics = [
            'total' => ExpenseRequest::where('user_id', $user->id)->count(),
            'pending' => ExpenseRequest::where('user_id', $user->id)
                ->where('status', ExpenseRequestStatus::PENDING->value)->count(),
            'approved' => ExpenseRequest::where('user_id', $user->id)
                ->where('status', ExpenseRequestStatus::APPROVED->value)->count(),
            'rejected' => ExpenseRequest::where('user_id', $user->id)
                ->where('status', ExpenseRequestStatus::REJECTED->value)->count(),
            'processed' => ExpenseRequest::where('user_id', $user->id)
                ->where('status', ExpenseRequestStatus::PROCESSED->value)->count(),
        ];

        return view('hrcore::expenses.my-expenses', compact('expenseTypes', 'statistics'));
    }

    public function myExpensesAjax(Request $request)
    {
        try {
            $query = ExpenseRequest::with(['expenseType'])
                ->where('user_id', auth()->id()) // Only show current user's expenses
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->when($request->expense_type_id, function ($q) use ($request) {
                    return $q->where('expense_type_id', $request->expense_type_id);
                })
                ->when($request->date_from, function ($q) use ($request) {
                    return $q->whereDate('expense_date', '>=', $request->date_from);
                })
                ->when($request->date_to, function ($q) use ($request) {
                    return $q->whereDate('expense_date', '<=', $request->date_to);
                });

            return DataTables::of($query)
                ->addColumn('expense_type', function ($expense) {
                    return $expense->expenseType->name ?? 'N/A';
                })
                ->addColumn('amount', function ($expense) {
                    return '$'.number_format($expense->amount, 2);
                })
                ->addColumn('expense_date', function ($expense) {
                    return $expense->expense_date->format('d M Y');
                })
                ->addColumn('status', function ($expense) {
                    $statusColors = [
                        ExpenseRequestStatus::PENDING->value => 'bg-label-warning',
                        ExpenseRequestStatus::APPROVED->value => 'bg-label-success',
                        ExpenseRequestStatus::REJECTED->value => 'bg-label-danger',
                        ExpenseRequestStatus::PROCESSED->value => 'bg-label-info',
                    ];
                    // Get the actual value whether it's an enum or string
                    $statusValue = $expense->status instanceof ExpenseRequestStatus
                        ? $expense->status->value
                        : $expense->status;
                    $statusClass = $statusColors[$statusValue] ?? 'bg-label-secondary';

                    return '<span class="badge '.$statusClass.'">'.ucfirst($statusValue).'</span>';
                })
                ->addColumn('attachments', function ($expense) {
                    if ($expense->hasAttachments()) {
                        $count = $expense->files()->count();

                        return '<span class="badge bg-label-primary">'.$count.' file(s)</span>';
                    }

                    return '<span class="text-muted">No attachments</span>';
                })
                ->addColumn('actions', function ($model) {
                    $actions = [];

                    // View action always available for own expenses
                    $actions[] = [
                        'label' => __('View'),
                        'icon' => 'bx bx-show',
                        'onclick' => "viewExpense({$model->id})",
                        'class' => 'dropdown-item',
                    ];

                    // Get the actual status value
                    $statusValue = $model->status instanceof ExpenseRequestStatus
                        ? $model->status->value
                        : $model->status;

                    // Edit action only for pending expenses
                    if ($statusValue === 'pending' || $statusValue === ExpenseRequestStatus::PENDING->value) {
                        if (auth()->user()->can('hrcore.edit-expense')) {
                            $actions[] = [
                                'label' => __('Edit'),
                                'icon' => 'bx bx-edit',
                                'onclick' => "editExpense({$model->id})",
                                'class' => 'dropdown-item',
                            ];
                        }

                        // Delete action only for own pending expenses
                        if (auth()->user()->can('hrcore.delete-expense')) {
                            $actions[] = [
                                'label' => __('Delete'),
                                'icon' => 'bx bx-trash',
                                'onclick' => "deleteExpense({$model->id})",
                                'class' => 'dropdown-item text-danger',
                            ];
                        }
                    }

                    return view('components.datatable-actions', [
                        'id' => $model->id,
                        'actions' => $actions,
                    ])->render();
                })
                ->rawColumns(['status', 'attachments', 'actions'])
                ->make(true);
        } catch (Exception $e) {
            Log::error('Error in myExpensesAjax: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function indexAjax(Request $request)
    {
        try {
            $query = ExpenseRequest::with(['user', 'expenseType'])
                ->when($request->employee_id, function ($q) use ($request) {
                    return $q->where('user_id', $request->employee_id);
                })
                ->when($request->expense_type_id, function ($q) use ($request) {
                    return $q->where('expense_type_id', $request->expense_type_id);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->when($request->date_from, function ($q) use ($request) {
                    return $q->whereDate('expense_date', '>=', $request->date_from);
                })
                ->when($request->date_to, function ($q) use ($request) {
                    return $q->whereDate('expense_date', '<=', $request->date_to);
                });

            return DataTables::of($query)
                ->addColumn('user', function ($expense) {
                    return view('components.datatable-user', ['user' => $expense->user])->render();
                })
                ->addColumn('expense_type', function ($expense) {
                    return $expense->expenseType ? $expense->expenseType->name : '-';
                })
                ->addColumn('amount', function ($expense) {
                    return '<span class="fw-medium">'.$expense->formatted_amount.'</span>';
                })
                ->addColumn('status', function ($expense) {
                    $statusConfig = $this->getStatusConfig($expense->status);

                    return "<span class='badge {$statusConfig['class']}'>{$statusConfig['text']}</span>";
                })
                ->addColumn('attachments', function ($expense) {
                    if ($expense->hasAttachments()) {
                        $count = $expense->files()->count();

                        return "<i class='bx bx-paperclip text-primary'></i> <small>{$count}</small>";
                    }

                    return '<i class="bx bx-x text-muted"></i>';
                })
                ->addColumn('actions', function ($expense) {
                    $actions = [];

                    // View is always available
                    $actions[] = ['label' => __('View'), 'icon' => 'bx bx-show', 'onclick' => "viewRecord({$expense->id})"];

                    // Edit for pending expenses
                    if ($expense->can_edit) {
                        $actions[] = ['label' => __('Edit'), 'icon' => 'bx bx-edit', 'onclick' => "editRecord({$expense->id})"];
                    }

                    // Approve/Reject for pending expenses (for managers/HR)
                    if ($expense->can_approve) {
                        $actions[] = ['label' => __('Approve'), 'icon' => 'bx bx-check', 'onclick' => "approveRecord({$expense->id})", 'class' => 'dropdown-item text-success'];
                        $actions[] = ['label' => __('Reject'), 'icon' => 'bx bx-x', 'onclick' => "rejectRecord({$expense->id})", 'class' => 'dropdown-item text-danger'];
                    }

                    // Process for approved expenses
                    if ($expense->can_process) {
                        $actions[] = ['label' => __('Process'), 'icon' => 'bx bx-credit-card', 'onclick' => "processRecord({$expense->id})", 'class' => 'dropdown-item text-info'];
                    }

                    // Delete for own pending expenses
                    if ($expense->can_delete) {
                        $actions[] = ['label' => __('Delete'), 'icon' => 'bx bx-trash', 'onclick' => "deleteRecord({$expense->id})"];
                    }

                    return view('components.datatable-actions', [
                        'id' => $expense->id,
                        'actions' => $actions,
                    ])->render();
                })
                ->rawColumns(['user', 'amount', 'status', 'attachments', 'actions'])
                ->make(true);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return Error::response(__('Something went wrong. Please try again.'));
        }
    }

    private function getStatusConfig($status)
    {
        // Get the actual value whether it's an enum or string
        $statusValue = $status instanceof ExpenseRequestStatus
            ? $status->value
            : $status;

        return match ($statusValue) {
            ExpenseRequestStatus::PENDING->value => ['class' => 'bg-warning', 'text' => __('Pending')],
            ExpenseRequestStatus::APPROVED->value => ['class' => 'bg-success', 'text' => __('Approved')],
            ExpenseRequestStatus::REJECTED->value => ['class' => 'bg-danger', 'text' => __('Rejected')],
            ExpenseRequestStatus::PROCESSED->value => ['class' => 'bg-info', 'text' => __('Processed')],
            default => ['class' => 'bg-secondary', 'text' => __('Unknown')]
        };
    }

    public function create(Request $request)
    {
        $expenseTypes = ExpenseType::where('status', 'active')->get();
        $departments = Department::where('status', Status::ACTIVE->value)->get();

        // Store the return URL in session to redirect back after save
        if ($request->has('return_to')) {
            session(['expense_return_url' => $request->return_to]);
        }

        return view('hrcore::expenses.create', compact('expenseTypes', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'project_code' => 'nullable|string|max:100',
            'cost_center' => 'nullable|string|max:100',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        try {
            DB::beginTransaction();

            // Create expense first
            $expense = ExpenseRequest::create([
                'user_id' => auth()->id(),
                'expense_type_id' => $request->expense_type_id,
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'title' => $request->title,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'project_code' => $request->project_code,
                'cost_center' => $request->cost_center,
                'status' => ExpenseRequestStatus::PENDING,
            ]);

            // Handle attachments using FileManagerCore
            if ($request->hasFile('attachments')) {
                $fileManager = app(FileManagerInterface::class);

                foreach ($request->file('attachments') as $index => $file) {
                    try {
                        $uploadRequest = FileUploadRequest::fromRequest(
                            $file,
                            FileType::EXPENSE_RECEIPT,
                            ExpenseRequest::class,
                            $expense->id // Now we have the expense ID
                        )->withName('expense_'.$expense->expense_number.'_'.($index + 1))
                            ->withVisibility(FileVisibility::PRIVATE)
                            ->withDescription('Expense receipt for '.$expense->title)
                            ->withMetadata([
                                'expense_number' => $expense->expense_number,
                                'expense_type' => $expense->expenseType->name ?? 'N/A',
                                'user_id' => auth()->id(),
                                'uploaded_at' => now()->toISOString(),
                            ]);

                        $fileManager->uploadFile($uploadRequest);
                    } catch (\Exception $e) {
                        Log::error('File upload failed for expense: '.$e->getMessage());
                        // Continue with next file
                    }
                }
            }

            DB::commit();

            // Send new expense request notification to approvers
            try {
                $notificationService = app(HRNotificationService::class);
                $notificationService->sendNewExpenseRequestNotification($expense);
            } catch (\Exception $e) {
                Log::error('Failed to send expense request notification: '.$e->getMessage());
                // Don't fail the request if notification fails
            }

            // Get the return URL from session
            $returnTo = session()->pull('expense_return_url');

            // Determine the redirect URL based on return_to parameter
            if ($returnTo === 'my-expenses') {
                $returnUrl = route('hrcore.expenses.my-expenses');
            } else {
                $returnUrl = route('hrcore.expenses.index');
            }

            return Success::response([
                'message' => __('Expense request created successfully'),
                'data' => $expense,
                'redirect_url' => $returnUrl,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return Error::response(__('Failed to create expense request'));
        }
    }

    public function show($id)
    {
        $expense = ExpenseRequest::with(['user', 'expenseType', 'department', 'approvedBy', 'rejectedBy', 'processedBy'])
            ->findOrFail($id);

        // Check if user can view this expense
        if ($expense->user_id === auth()->id()) {
            Gate::authorize('hrcore.view-own-expenses');
        } else {
            Gate::authorize('hrcore.view-expenses');
        }

        return view('hrcore::expenses.show', compact('expense'));
    }

    public function edit(Request $request, $id)
    {
        $expense = ExpenseRequest::findOrFail($id);

        if (! $expense->can_edit) {
            return redirect()->route('hrcore.expenses.index')
                ->with('error', __('You cannot edit this expense request'));
        }

        // Store the return URL in session to redirect back after save
        if ($request->has('return_to')) {
            session(['expense_return_url' => $request->return_to]);
        }

        $expenseTypes = ExpenseType::where('status', 'active')->get();
        $departments = Department::where('status', Status::ACTIVE->value)->get();

        return view('hrcore::expenses.edit', compact('expense', 'expenseTypes', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $expense = ExpenseRequest::findOrFail($id);

        if (! $expense->can_edit) {
            return Error::response(__('You cannot edit this expense request'));
        }

        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'project_code' => 'nullable|string|max:100',
            'cost_center' => 'nullable|string|max:100',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        try {
            DB::beginTransaction();

            // Handle file removals if using FileManagerCore
            if ($request->has('removed_file_ids')) {
                $fileManager = app(FileManagerInterface::class);
                $removedIds = $request->removed_file_ids;

                foreach ($removedIds as $fileId) {
                    try {
                        $file = $expense->files()->find($fileId);
                        if ($file) {
                            $fileManager->deleteFile($file);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to remove file: '.$e->getMessage());
                    }
                }
            }

            // Handle new attachments using FileManagerCore
            if ($request->hasFile('attachments')) {
                $fileManager = app(FileManagerInterface::class);

                foreach ($request->file('attachments') as $index => $file) {
                    try {
                        $uploadRequest = FileUploadRequest::fromRequest(
                            $file,
                            FileType::EXPENSE_RECEIPT,
                            ExpenseRequest::class,
                            $expense->id
                        )->withName('expense_'.$expense->expense_number.'_'.time().'_'.($index + 1))
                            ->withVisibility(FileVisibility::PRIVATE)
                            ->withDescription('Updated expense receipt for '.$expense->title)
                            ->withMetadata([
                                'expense_number' => $expense->expense_number,
                                'expense_type' => $expense->expenseType->name ?? 'N/A',
                                'user_id' => auth()->id(),
                                'updated_at' => now()->toISOString(),
                            ]);

                        $fileManager->uploadFile($uploadRequest);
                    } catch (\Exception $e) {
                        Log::error('File upload failed for expense update: '.$e->getMessage());
                        // Continue with next file
                    }
                }
            }

            $expense->update([
                'expense_type_id' => $request->expense_type_id,
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'title' => $request->title,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'project_code' => $request->project_code,
                'cost_center' => $request->cost_center,
            ]);

            DB::commit();

            // Get the return URL from session
            $returnTo = session()->pull('expense_return_url');

            // Determine the redirect URL based on return_to parameter
            if ($returnTo === 'my-expenses') {
                $returnUrl = route('hrcore.expenses.my-expenses');
            } else {
                $returnUrl = route('hrcore.expenses.index');
            }

            return Success::response([
                'message' => __('Expense request updated successfully'),
                'data' => $expense,
                'redirect_url' => $returnUrl,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return Error::response(__('Failed to update expense request'));
        }
    }

    public function destroy($id)
    {
        try {
            $expense = ExpenseRequest::findOrFail($id);

            if (! $expense->can_delete) {
                return Error::response(__('You cannot delete this expense request'));
            }

            // Delete attached files using FileManagerCore
            if ($expense->hasAttachments()) {
                $fileManager = app(FileManagerInterface::class);
                foreach ($expense->files as $file) {
                    try {
                        $fileManager->deleteFile($file);
                    } catch (\Exception $e) {
                        Log::error('Failed to delete file during expense removal: '.$e->getMessage());
                    }
                }
            }

            $expense->delete();

            return Success::response(__('Expense request deleted successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return Error::response(__('Failed to delete expense request'));
        }
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'approved_amount' => 'nullable|numeric|min:0.01',
            'approval_remarks' => 'nullable|string|max:500',
        ]);

        try {
            $expense = ExpenseRequest::findOrFail($id);

            if (! $expense->can_approve) {
                return Error::response(__('You cannot approve this expense request'));
            }

            $expense->approve(
                auth()->user(),
                $request->approved_amount,
                $request->approval_remarks
            );

            // Send notification
            try {
                $notificationService = app(HRNotificationService::class);
                $notificationService->sendExpenseRequestApprovalNotification($expense, 'approved');
            } catch (\Exception $e) {
                Log::error('Failed to send expense approval notification: '.$e->getMessage());
                // Don't fail the approval if notification fails
            }

            return Success::response(__('Expense request approved successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return Error::response(__('Failed to approve expense request'));
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            $expense = ExpenseRequest::findOrFail($id);

            if (! $expense->can_approve) {
                return Error::response(__('You cannot reject this expense request'));
            }

            $expense->reject(auth()->user(), $request->rejection_reason);

            // Send notification
            try {
                $notificationService = app(HRNotificationService::class);
                $notificationService->sendExpenseRequestApprovalNotification($expense, 'rejected');
            } catch (\Exception $e) {
                Log::error('Failed to send expense rejection notification: '.$e->getMessage());
                // Don't fail the rejection if notification fails
            }

            return Success::response(__('Expense request rejected successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return Error::response(__('Failed to reject expense request'));
        }
    }

    public function process(Request $request, $id)
    {
        $request->validate([
            'payment_reference' => 'nullable|string|max:100',
            'processing_notes' => 'nullable|string|max:500',
        ]);

        try {
            $expense = ExpenseRequest::findOrFail($id);

            if (! $expense->can_process) {
                return Error::response(__('You cannot process this expense request'));
            }

            $expense->markAsProcessed(
                auth()->user(),
                $request->payment_reference,
                $request->processing_notes
            );

            return Success::response(__('Expense request processed successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return Error::response(__('Failed to process expense request'));
        }
    }

    // ===================================================================
    // SELF-SERVICE METHODS - Always use auth()->id()
    // These methods are called from /my/* routes with self_service middleware
    // ===================================================================

    /**
     * Create a new expense for the authenticated user (self-service)
     */
    public function createMyExpense(Request $request)
    {
        $expenseTypes = ExpenseType::where('status', 'active')->get();
        $departments = Department::where('status', Status::ACTIVE->value)->get();

        // Store the return URL in session to redirect back after save
        if ($request->has('return_to')) {
            session(['expense_return_url' => $request->return_to]);
        }

        return view('hrcore::expenses.my-create', compact('expenseTypes', 'departments'));
    }

    /**
     * Store a new expense for the authenticated user (self-service)
     * Always uses auth()->id() for user_id
     */
    public function storeMyExpense(Request $request)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'project_code' => 'nullable|string|max:100',
            'cost_center' => 'nullable|string|max:100',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        try {
            DB::beginTransaction();

            // Always use authenticated user's ID for self-service
            $expense = ExpenseRequest::create([
                'user_id' => auth()->id(), // Always use auth()->id() for self-service
                'expense_type_id' => $request->expense_type_id,
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'title' => $request->title,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'project_code' => $request->project_code,
                'cost_center' => $request->cost_center,
                'status' => ExpenseRequestStatus::PENDING,
            ]);

            // Handle attachments using FileManagerCore
            if ($request->hasFile('attachments')) {
                $fileManager = app(FileManagerInterface::class);

                foreach ($request->file('attachments') as $index => $file) {
                    try {
                        $uploadRequest = FileUploadRequest::fromRequest(
                            $file,
                            FileType::EXPENSE_RECEIPT,
                            ExpenseRequest::class,
                            $expense->id
                        )->withName('expense_'.$expense->expense_number.'_'.($index + 1))
                            ->withVisibility(FileVisibility::PRIVATE)
                            ->withDescription('Expense receipt for '.$expense->title)
                            ->withMetadata([
                                'expense_number' => $expense->expense_number,
                                'expense_type' => $expense->expenseType->name ?? 'N/A',
                                'user_id' => auth()->id(),
                                'uploaded_at' => now()->toISOString(),
                            ]);

                        $fileManager->uploadFile($uploadRequest);
                    } catch (\Exception $e) {
                        Log::error('File upload failed for expense: '.$e->getMessage());
                        // Continue with next file
                    }
                }
            }

            DB::commit();

            // Send notification
            try {
                $notificationService = app(HRNotificationService::class);
                $notificationService->sendNewExpenseRequestNotification($expense);
            } catch (\Exception $e) {
                Log::error('Failed to send expense request notification: '.$e->getMessage());
            }

            // Check for return URL
            $returnUrl = session()->pull('expense_return_url', route('hrcore.my.expenses'));

            return Success::response([
                'message' => __('Expense request submitted successfully'),
                'redirect' => $returnUrl,
            ]);

        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return Error::response(__('Failed to submit expense request'));
        }
    }

    /**
     * Show expense details for authenticated user (self-service)
     */
    public function showMyExpense($id)
    {
        try {
            $expense = ExpenseRequest::where('user_id', auth()->id())
                ->with(['expenseType', 'department', 'approvedBy', 'rejectedBy', 'processedBy', 'files'])
                ->findOrFail($id);

            // Check if request is AJAX
            if (request()->ajax()) {
                // Format the expense data for JSON response
                $expenseData = [
                    'id' => $expense->id,
                    'expense_number' => $expense->expense_number,
                    'expense_date' => $expense->expense_date->format('d M Y'),
                    'title' => $expense->title,
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'formatted_amount' => \App\Helpers\FormattingHelper::formatCurrency($expense->amount),
                    'status' => $expense->status,
                    'status_badge' => $this->getStatusBadge($expense->status),
                    'expense_type' => $expense->expenseType,
                    'department' => $expense->department,
                    'approved_by' => $expense->approvedBy,
                    'approved_at' => $expense->approved_at ? $expense->approved_at->format('d M Y H:i') : null,
                    'approval_remarks' => $expense->approval_remarks,
                    'rejected_by' => $expense->rejectedBy,
                    'rejected_at' => $expense->rejected_at ? $expense->rejected_at->format('d M Y H:i') : null,
                    'rejection_reason' => $expense->rejection_reason,
                    'processed_by' => $expense->processedBy,
                    'processed_at' => $expense->processed_at ? $expense->processed_at->format('d M Y H:i') : null,
                    'payment_reference' => $expense->payment_reference,
                    'processing_notes' => $expense->processing_notes,
                    'attachments' => $expense->files->map(function ($file) {
                        return [
                            'name' => $file->original_name,
                            'url' => route('file.download', $file->id),
                        ];
                    }),
                ];

                return Success::response([
                    'expense' => $expenseData,
                ]);
            }

            return view('hrcore::expenses.my-show', compact('expense'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return Error::response('Failed to load expense details: '.$e->getMessage());
            }
            throw $e;
        }
    }

    private function getStatusBadge($status)
    {
        $statusValue = $status instanceof ExpenseRequestStatus ? $status->value : $status;
        $statusColors = [
            ExpenseRequestStatus::PENDING->value => 'bg-label-warning',
            ExpenseRequestStatus::APPROVED->value => 'bg-label-success',
            ExpenseRequestStatus::REJECTED->value => 'bg-label-danger',
            ExpenseRequestStatus::PROCESSED->value => 'bg-label-info',
        ];
        $statusClass = $statusColors[$statusValue] ?? 'bg-label-secondary';

        return '<span class="badge '.$statusClass.'">'.ucfirst($statusValue).'</span>';
    }

    /**
     * Edit expense for authenticated user (self-service)
     */
    public function editMyExpense($id)
    {
        $expense = ExpenseRequest::where('user_id', auth()->id())
            ->where('status', ExpenseRequestStatus::PENDING)
            ->findOrFail($id);

        $expenseTypes = ExpenseType::where('status', 'active')->get();
        $departments = Department::where('status', Status::ACTIVE->value)->get();

        return view('hrcore::expenses.my-edit', compact('expense', 'expenseTypes', 'departments'));
    }

    /**
     * Update expense for authenticated user (self-service)
     */
    public function updateMyExpense(Request $request, $id)
    {
        $expense = ExpenseRequest::where('user_id', auth()->id())
            ->where('status', ExpenseRequestStatus::PENDING)
            ->findOrFail($id);

        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'project_code' => 'nullable|string|max:100',
            'cost_center' => 'nullable|string|max:100',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        try {
            DB::beginTransaction();

            $expense->update([
                'expense_type_id' => $request->expense_type_id,
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'title' => $request->title,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'project_code' => $request->project_code,
                'cost_center' => $request->cost_center,
            ]);

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                $fileManager = app(FileManagerInterface::class);

                foreach ($request->file('attachments') as $index => $file) {
                    try {
                        $uploadRequest = FileUploadRequest::fromRequest(
                            $file,
                            FileType::EXPENSE_RECEIPT,
                            ExpenseRequest::class,
                            $expense->id
                        )->withName('expense_'.$expense->expense_number.'_updated_'.($index + 1))
                            ->withVisibility(FileVisibility::PRIVATE)
                            ->withDescription('Updated expense receipt for '.$expense->title)
                            ->withMetadata([
                                'expense_number' => $expense->expense_number,
                                'expense_type' => $expense->expenseType->name ?? 'N/A',
                                'user_id' => auth()->id(),
                                'updated_at' => now()->toISOString(),
                            ]);

                        $fileManager->uploadFile($uploadRequest);
                    } catch (\Exception $e) {
                        Log::error('File upload failed for expense update: '.$e->getMessage());
                    }
                }
            }

            DB::commit();

            return Success::response([
                'message' => __('Expense request updated successfully'),
                'redirect' => route('hrcore.my.expenses'),
            ]);

        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return Error::response(__('Failed to update expense request'));
        }
    }

    /**
     * Delete expense for authenticated user (self-service)
     */
    public function deleteMyExpense($id)
    {
        try {
            $expense = ExpenseRequest::where('user_id', auth()->id())
                ->where('status', ExpenseRequestStatus::PENDING)
                ->findOrFail($id);

            $expense->delete();

            return Success::response(__('Expense request deleted successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return Error::response(__('Failed to delete expense request'));
        }
    }

    /**
     * Get my leaves list (self-service)
     */
    public function myLeaves()
    {
        // This method might be called from routes, redirect to myExpenses
        return redirect()->route('hrcore.my.expenses');
    }
}
