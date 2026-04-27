<?php

namespace Modules\HRCore\app\Http\Controllers\Api\V1;

use App\Enums\ExpenseRequestStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\HRCore\app\Http\Controllers\Api\BaseApiController;
use Modules\HRCore\app\Http\Resources\ExpenseRequestResource;
use Modules\HRCore\app\Http\Resources\ExpenseTypeResource;
use Modules\HRCore\app\Models\ExpenseRequest;
use Modules\HRCore\app\Models\ExpenseType;

/**
 * @OA\Tag(
 *     name="Expenses",
 *     description="Employee expense management endpoints"
 * )
 *
 * Expense API Controller
 *
 * This controller handles all expense-related operations for the ESS mobile app.
 * All endpoints require JWT authentication via Bearer token.
 *
 * Base URL: /api/essapp/v1/expenses
 *
 * Features:
 * - Submit expense requests
 * - Upload expense receipts
 * - View expense history
 * - Track expense status
 * - Manage expense types
 */
class ExpenseController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/essapp/v1/expenses/types",
     *     summary="Get available expense types",
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Expense types retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense types retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Travel"),
     *                     @OA\Property(property="description", type="string", example="Travel related expenses"),
     *                     @OA\Property(property="requires_proof", type="boolean", example=true),
     *                     @OA\Property(property="max_amount", type="number", format="float", example=5000.00, nullable=true),
     *                     @OA\Property(property="is_active", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getTypes(): JsonResponse
    {
        $types = ExpenseType::where('status', 'active')
            ->orderBy('name')
            ->get();

        return $this->successResponse(
            ExpenseTypeResource::collection($types),
            'Expense types retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/essapp/v1/expenses",
     *     summary="Submit a new expense request",
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"expense_date", "expense_type_id", "amount", "title"},
     *
     *             @OA\Property(property="expense_date", type="string", format="date", example="2024-01-15"),
     *             @OA\Property(property="expense_type_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=250.50),
     *             @OA\Property(property="currency", type="string", example="USD", default="USD"),
     *             @OA\Property(property="title", type="string", example="Client Meeting Lunch"),
     *             @OA\Property(property="description", type="string", example="Lunch with client at restaurant"),
     *             @OA\Property(property="project_code", type="string", example="PROJ-001", nullable=true),
     *             @OA\Property(property="cost_center", type="string", example="SALES", nullable=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Expense request created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense request submitted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ExpenseRequestResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'expense_date' => 'required|date|before_or_equal:today',
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'project_code' => 'nullable|string|max:50',
            'cost_center' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        // Verify expense type is active
        $expenseType = ExpenseType::find($request->expense_type_id);
        if (! $expenseType || ($expenseType->status !== 'active' && $expenseType->status !== \App\Enums\Status::ACTIVE)) {
            return $this->errorResponse('Selected expense type is not available');
        }

        // Check max amount if configured
        if ($expenseType->max_amount && $request->amount > $expenseType->max_amount) {
            return $this->errorResponse(
                "Amount exceeds maximum limit of {$expenseType->max_amount} for {$expenseType->name}"
            );
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();

            if (! $user) {
                throw new \Exception('User not authenticated');
            }

            // Create expense request
            $expenseRequest = ExpenseRequest::create([
                'expense_date' => $request->expense_date,
                'user_id' => $user->id,
                'expense_type_id' => $request->expense_type_id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'USD',
                'title' => $request->title,
                'description' => $request->description,
                'status' => ExpenseRequestStatus::PENDING,
                'department_id' => $user->department_id ?? null,
                'project_code' => $request->project_code,
                'cost_center' => $request->cost_center,
                'created_by_id' => $user->id,
            ]);

            DB::commit();

            // Load relationships for response
            $expenseRequest->load(['expenseType', 'user']);

            return $this->successResponse(
                new ExpenseRequestResource($expenseRequest),
                'Expense request submitted successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Expense creation failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->errorResponse('Failed to submit expense request: '.$e->getMessage(), null, 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/essapp/v1/expenses",
     *     summary="Get expense requests list",
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"pending", "approved", "rejected", "processed", "cancelled"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="Start date (Y-m-d)",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date")
     *     ),
     *
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="End date (Y-m-d)",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date")
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Expenses retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expenses retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ExpenseRequestResource")),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="currentPage", type="integer", example=1),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:pending,approved,rejected,processed,cancelled',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $query = ExpenseRequest::where('user_id', Auth::id())
            ->with(['expenseType', 'approvedBy'])
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->from) {
            $query->whereDate('expense_date', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('expense_date', '<=', $request->to);
        }

        $expenses = $query->paginate(20);

        return $this->paginatedResponse(
            $expenses->through(fn ($item) => new ExpenseRequestResource($item)),
            'Expenses retrieved successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/essapp/v1/expenses/{id}",
     *     summary="Get expense request details",
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Expense request ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Expense details retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense details retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ExpenseRequestResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Expense not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $expense = ExpenseRequest::where('user_id', Auth::id())
            ->with(['expenseType', 'approvedBy', 'rejectedBy', 'processedBy'])
            ->find($id);

        if (! $expense) {
            return $this->notFoundResponse('Expense request not found');
        }

        return $this->successResponse(
            new ExpenseRequestResource($expense),
            'Expense details retrieved successfully'
        );
    }

    /**
     * @OA\Put(
     *     path="/essapp/v1/expenses/{id}",
     *     summary="Update expense request",
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Expense request ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="expense_date", type="string", format="date"),
     *             @OA\Property(property="expense_type_id", type="integer"),
     *             @OA\Property(property="amount", type="number", format="float"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Expense updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cannot update expense (not in pending status)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Expense not found"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $expense = ExpenseRequest::where('user_id', Auth::id())->find($id);

        if (! $expense) {
            return $this->notFoundResponse('Expense request not found');
        }

        // Can only update pending expenses
        if ($expense->status !== ExpenseRequestStatus::PENDING) {
            return $this->errorResponse('Can only update pending expense requests');
        }

        $validator = Validator::make($request->all(), [
            'expense_date' => 'nullable|date|before_or_equal:today',
            'expense_type_id' => 'nullable|exists:expense_types,id',
            'amount' => 'nullable|numeric|min:0.01',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'project_code' => 'nullable|string|max:50',
            'cost_center' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        // Check max amount if expense type is being changed
        if ($request->has('expense_type_id') || $request->has('amount')) {
            $typeId = $request->expense_type_id ?? $expense->expense_type_id;
            $amount = $request->amount ?? $expense->amount;

            $expenseType = ExpenseType::find($typeId);
            if ($expenseType && $expenseType->max_amount && $amount > $expenseType->max_amount) {
                return $this->errorResponse(
                    "Amount exceeds maximum limit of {$expenseType->max_amount} for {$expenseType->name}"
                );
            }
        }

        $expense->update($request->only([
            'expense_date', 'expense_type_id', 'amount',
            'title', 'description', 'project_code', 'cost_center',
        ]));

        $expense->load(['expenseType']);

        return $this->successResponse(
            new ExpenseRequestResource($expense),
            'Expense updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/essapp/v1/expenses/{id}",
     *     summary="Cancel expense request",
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Expense request ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Expense cancelled successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense request cancelled successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Cannot cancel expense (not in pending status)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Expense not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $expense = ExpenseRequest::where('user_id', Auth::id())->find($id);

        if (! $expense) {
            return $this->notFoundResponse('Expense request not found');
        }

        // Can only cancel pending expenses
        if ($expense->status !== ExpenseRequestStatus::PENDING) {
            return $this->errorResponse('Can only cancel pending expense requests');
        }

        // Soft delete the expense instead of changing status
        $expense->delete();

        return $this->successResponse(
            null,
            'Expense request cancelled successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/essapp/v1/expenses/{id}/upload",
     *     summary="Upload receipt/document for expense",
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Expense request ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"file"},
     *
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="Receipt/document file (image or PDF)"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Document uploaded successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Document uploaded successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="filename", type="string", example="receipt_1234567890.pdf"),
     *                 @OA\Property(property="url", type="string", example="/storage/expenses/receipt_1234567890.pdf")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid file or expense not editable"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Expense not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function uploadDocument(Request $request, $id): JsonResponse
    {
        $expense = ExpenseRequest::where('user_id', Auth::id())->find($id);

        if (! $expense) {
            return $this->notFoundResponse('Expense request not found');
        }

        // Can only upload documents for pending expenses
        if ($expense->status !== ExpenseRequestStatus::PENDING) {
            return $this->errorResponse('Can only upload documents for pending expense requests');
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $file = $request->file('file');
            $filename = 'expense_'.$expense->id.'_'.time().'.'.$file->getClientOriginalExtension();

            // Store file
            $path = $file->storeAs('expenses/receipts', $filename, 'public');

            // Update expense attachments
            $attachments = $expense->attachments ?? [];
            $attachments[] = [
                'filename' => $filename,
                'path' => $path,
                'uploaded_at' => now()->toIso8601String(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];

            $expense->attachments = $attachments;
            $expense->save();

            return $this->successResponse([
                'filename' => $filename,
                'url' => Storage::url($path),
            ], 'Document uploaded successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to upload document. Please try again.', null, 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/essapp/v1/expenses/summary",
     *     summary="Get expense summary statistics",
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         description="Month (1-12)",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=12)
     *     ),
     *
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Year",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=2020)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Summary retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Summary retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_submitted", type="number", format="float", example=5000.00),
     *                 @OA\Property(property="total_approved", type="number", format="float", example=4500.00),
     *                 @OA\Property(property="total_pending", type="number", format="float", example=500.00),
     *                 @OA\Property(property="total_rejected", type="number", format="float", example=0),
     *                 @OA\Property(property="total_processed", type="number", format="float", example=4000.00),
     *                 @OA\Property(property="count_pending", type="integer", example=2),
     *                 @OA\Property(property="count_approved", type="integer", example=10),
     *                 @OA\Property(property="count_rejected", type="integer", example=0),
     *                 @OA\Property(
     *                     property="by_category",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="type", type="string", example="Travel"),
     *                         @OA\Property(property="amount", type="number", format="float", example=2000.00),
     *                         @OA\Property(property="count", type="integer", example=5)
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function summary(Request $request): JsonResponse
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $expenses = ExpenseRequest::where('user_id', Auth::id())
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get();

        // Calculate totals by status
        $summary = [
            'total_submitted' => $expenses->sum('amount'),
            'total_approved' => $expenses->where('status', ExpenseRequestStatus::APPROVED)->sum('approved_amount'),
            'total_pending' => $expenses->where('status', ExpenseRequestStatus::PENDING)->sum('amount'),
            'total_rejected' => $expenses->where('status', ExpenseRequestStatus::REJECTED)->sum('amount'),
            'total_processed' => $expenses->where('status', ExpenseRequestStatus::PROCESSED)->sum('approved_amount'),
            'count_pending' => $expenses->where('status', ExpenseRequestStatus::PENDING)->count(),
            'count_approved' => $expenses->where('status', ExpenseRequestStatus::APPROVED)->count(),
            'count_rejected' => $expenses->where('status', ExpenseRequestStatus::REJECTED)->count(),
        ];

        // Group by expense type
        $byCategory = ExpenseRequest::where('user_id', Auth::id())
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->join('expense_types', 'expense_requests.expense_type_id', '=', 'expense_types.id')
            ->groupBy('expense_types.id', 'expense_types.name')
            ->selectRaw('expense_types.name as type, SUM(expense_requests.amount) as amount, COUNT(*) as count')
            ->get();

        $summary['by_category'] = $byCategory;

        return $this->successResponse($summary, 'Summary retrieved successfully');
    }
}
