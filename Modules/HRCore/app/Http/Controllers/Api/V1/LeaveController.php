<?php

namespace Modules\HRCore\app\Http\Controllers\Api\V1;

use App\Services\Settings\ModuleSettingsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\FileManagerCore\Contracts\FileManagerInterface;
use Modules\FileManagerCore\DTO\FileUploadRequest;
use Modules\FileManagerCore\Enums\FileType;
use Modules\FileManagerCore\Enums\FileVisibility;
use Modules\HRCore\app\Http\Controllers\Api\BaseApiController;
use Modules\HRCore\app\Http\Resources\CompensatoryOffResource;
use Modules\HRCore\app\Http\Resources\HolidayResource;
use Modules\HRCore\app\Http\Resources\LeaveBalanceResource;
use Modules\HRCore\app\Http\Resources\LeaveRequestResource;
use Modules\HRCore\app\Http\Resources\LeaveTypeResource;
use Modules\HRCore\app\Models\Attendance;
use Modules\HRCore\app\Models\CompensatoryOff;
use Modules\HRCore\app\Models\Holiday;
use Modules\HRCore\app\Models\LeaveRequest;
use Modules\HRCore\app\Models\LeaveType;
use Modules\HRCore\app\Models\UserAvailableLeave;

class LeaveController extends BaseApiController
{
    public function balance(): JsonResponse
    {
        $userId = Auth::id();
        $currentYear = Carbon::now()->year;

        // Get existing balances for the user
        $existingBalances = UserAvailableLeave::where('user_id', $userId)
            ->where('year', $currentYear)
            ->with('leaveType')
            ->get()
            ->keyBy('leave_type_id');

        // Get all active leave types
        $leaveTypes = LeaveType::where('status', 'active')->get();

        // Build complete balance list with 0 for missing types
        $balances = collect();

        foreach ($leaveTypes as $leaveType) {
            if ($existingBalances->has($leaveType->id)) {
                // Use existing balance record
                $balances->push($existingBalances[$leaveType->id]);
            } else {
                // Create a virtual balance record with 0 values
                $virtualBalance = new UserAvailableLeave([
                    'user_id' => $userId,
                    'leave_type_id' => $leaveType->id,
                    'year' => $currentYear,
                    'entitled_leaves' => 0,
                    'available_leaves' => 0,
                    'used_leaves' => 0,
                    'carried_forward_leaves' => 0,
                ]);
                $virtualBalance->setRelation('leaveType', $leaveType);
                // Set id to null for virtual records (non-persisted)
                $virtualBalance->id = null;
                $virtualBalance->exists = false; // Mark as non-persisted record

                $balances->push($virtualBalance);
            }
        }

        return $this->successResponse(
            LeaveBalanceResource::collection($balances),
            'Leave balance retrieved successfully'
        );
    }

    public function types(): JsonResponse
    {
        $types = LeaveType::where('status', 'active')
            ->orderBy('name')
            ->get();

        return $this->successResponse(
            LeaveTypeResource::collection($types),
            'Leave types retrieved successfully'
        );
    }

    public function history(Request $request): JsonResponse
    {
        $query = LeaveRequest::where('user_id', Auth::id())
            ->with(['leaveType', 'approvedBy'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('from_date')) {
            $query->whereDate('from_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('to_date', '<=', $request->to_date);
        }

        $leaves = $query->paginate(20);

        return $this->paginatedResponse(
            $leaves->through(fn ($item) => new LeaveRequestResource($item)),
            'Leave history retrieved successfully'
        );
    }

    public function apply(Request $request): JsonResponse
    {
        // Get settings for leave validation
        $settingsService = app(ModuleSettingsService::class);
        $minAdvanceNoticeDays = (int) $settingsService->get('HRCore', 'min_advance_notice_days', '1');
        $minDate = Carbon::now()->addDays($minAdvanceNoticeDays)->toDateString();

        // Convert string boolean values to actual booleans for form data
        $data = $request->all();
        if (isset($data['is_half_day'])) {
            $data['is_half_day'] = filter_var($data['is_half_day'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($data['is_abroad'])) {
            $data['is_abroad'] = filter_var($data['is_abroad'], FILTER_VALIDATE_BOOLEAN);
        }

        $validator = Validator::make($data, [
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date|after_or_equal:'.$minDate,
            'to_date' => 'required|date|after_or_equal:from_date',
            'is_half_day' => 'nullable|boolean',
            'half_day_type' => 'required_if:is_half_day,true|in:first_half,second_half',
            'reason' => 'required|string|max:500',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:50',
            'is_abroad' => 'nullable|boolean',
            'abroad_location' => 'required_if:is_abroad,true|string|max:200',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        // Calculate leave days
        $fromDate = Carbon::parse($data['from_date']);
        $toDate = Carbon::parse($data['to_date']);
        $leaveDays = $this->calculateLeaveDays($fromDate, $toDate, $data['is_half_day'] ?? false);

        // Check leave balance
        $availableLeave = UserAvailableLeave::where('user_id', Auth::id())
            ->where('leave_type_id', $data['leave_type_id'])
            ->first();

        if (! $availableLeave || $availableLeave->available_leaves < $leaveDays) {
            return $this->errorResponse('Insufficient leave balance');
        }

        // Check for overlapping leaves
        $overlapping = LeaveRequest::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('from_date', [$fromDate, $toDate])
                    ->orWhereBetween('to_date', [$fromDate, $toDate])
                    ->orWhere(function ($q) use ($fromDate, $toDate) {
                        $q->where('from_date', '<=', $fromDate)
                            ->where('to_date', '>=', $toDate);
                    });
            })
            ->exists();

        if ($overlapping) {
            return $this->errorResponse('You already have a leave request for the selected dates');
        }

        DB::beginTransaction();
        try {
            // Create leave request first to get the ID
            $leaveRequest = LeaveRequest::create([
                'user_id' => Auth::id(),
                'leave_type_id' => $data['leave_type_id'],
                'from_date' => $data['from_date'],
                'to_date' => $data['to_date'],
                'is_half_day' => $data['is_half_day'] ?? false,
                'half_day_type' => $data['half_day_type'] ?? null,
                'total_days' => $leaveDays,
                'user_notes' => $data['reason'], // Using user_notes as per migration
                'emergency_contact' => $data['emergency_contact'] ?? null,
                'emergency_phone' => $data['emergency_phone'] ?? null,
                'is_abroad' => $data['is_abroad'] ?? false,
                'abroad_location' => $data['abroad_location'] ?? null,
                'status' => 'pending',
                'created_by_id' => Auth::id(),
            ]);

            // Handle document upload using FileManagerCore if available
            if ($request->hasFile('document')) {
                try {
                    if (app()->bound(FileManagerInterface::class)) {
                        $fileManager = app(FileManagerInterface::class);
                        $user = Auth::user();

                        $uploadRequest = FileUploadRequest::fromRequest(
                            $request->file('document'),
                            FileType::LEAVE_DOCUMENT,
                            LeaveRequest::class,
                            $leaveRequest->id
                        )->withName('leave_'.$user->code.'_'.$leaveRequest->id)
                            ->withVisibility(FileVisibility::INTERNAL)
                            ->withDescription('Leave request document for '.$data['from_date'].' to '.$data['to_date'])
                            ->withMetadata([
                                'employee_code' => $user->code,
                                'leave_request_id' => $leaveRequest->id,
                                'leave_type' => $leaveRequest->leaveType->name ?? 'Unknown',
                                'from_date' => $data['from_date'],
                                'to_date' => $data['to_date'],
                                'uploaded_via' => 'mobile_app',
                            ]);

                        $uploadedFile = $fileManager->uploadFile($uploadRequest);

                        // Store the file path reference in the leave request
                        $leaveRequest->document = $uploadedFile->path;
                        $leaveRequest->save();

                    } else {
                        // Fallback to legacy storage if FileManagerCore is not available
                        $file = $request->file('document');
                        $fileName = 'leave_'.Auth::id().'_'.time().'.'.$file->getClientOriginalExtension();
                        $documentPath = $file->storeAs('leave_documents', $fileName, 'public');

                        $leaveRequest->document = $documentPath;
                        $leaveRequest->save();
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to upload leave document for request: {$leaveRequest->id}", [
                        'error' => $e->getMessage(),
                    ]);
                    // Don't fail the whole request if document upload fails
                    // Leave request is more important than the document
                }
            }

            // Don't deduct balance immediately - wait for approval
            // This is more realistic as leave should only be deducted when approved

            // Send notification to manager
            $this->notifyManager($leaveRequest);

            DB::commit();

            $leaveRequest->load(['leaveType', 'approvedBy']);

            return $this->successResponse(
                new LeaveRequestResource($leaveRequest),
                'Leave request submitted successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit leave request', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to submit leave request', null, 500);
        }
    }

    public function show($id): JsonResponse
    {
        $leaveRequest = LeaveRequest::where('user_id', Auth::id())
            ->with(['leaveType', 'approvedBy'])
            ->find($id);

        if (! $leaveRequest) {
            return $this->notFoundResponse('Leave request not found');
        }

        return $this->successResponse(
            new LeaveRequestResource($leaveRequest),
            'Leave request retrieved successfully'
        );
    }

    public function update(Request $request, $id): JsonResponse
    {
        $leaveRequest = LeaveRequest::where('user_id', Auth::id())->find($id);

        if (! $leaveRequest) {
            return $this->notFoundResponse('Leave request not found');
        }

        if ($leaveRequest->status !== 'pending') {
            return $this->errorResponse("Cannot update {$leaveRequest->status->value} leave request");
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'sometimes|string|max:500',
            'contact_during_leave' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $leaveRequest->update($request->only(['reason', 'contact_during_leave']));
        $leaveRequest->load(['leaveType', 'approvedBy']);

        return $this->successResponse(
            new LeaveRequestResource($leaveRequest),
            'Leave request updated successfully'
        );
    }

    public function cancel($id): JsonResponse
    {
        $leaveRequest = LeaveRequest::where('user_id', Auth::id())->find($id);

        if (! $leaveRequest) {
            return $this->notFoundResponse('Leave request not found');
        }

        if ($leaveRequest->status === 'approved' && Carbon::parse($leaveRequest->from_date)->isPast()) {
            return $this->errorResponse('Cannot cancel leave that has already started');
        }

        DB::beginTransaction();
        try {
            // Only restore leave balance if the request was already approved
            if ($leaveRequest->status === 'approved') {
                $availableLeave = UserAvailableLeave::where('user_id', Auth::id())
                    ->where('leave_type_id', $leaveRequest->leave_type_id)
                    ->first();

                if ($availableLeave) {
                    $availableLeave->available_leaves += $leaveRequest->total_days;
                    $availableLeave->used_leaves -= $leaveRequest->total_days;
                    $availableLeave->save();
                }
            }

            $leaveRequest->status = 'cancelled';
            $leaveRequest->cancelled_at = Carbon::now();
            $leaveRequest->cancelled_by_id = Auth::id();
            $leaveRequest->cancel_reason = 'Cancelled by employee';
            $leaveRequest->save();

            DB::commit();

            return $this->successResponse(null, 'Leave request cancelled successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Failed to cancel leave request', null, 500);
        }
    }

    public function holidays(Request $request): JsonResponse
    {
        $year = $request->year ?? Carbon::now()->year;

        $holidays = Holiday::whereYear('date', $year)
            ->orderBy('date')
            ->get();

        return $this->successResponse(
            HolidayResource::collection($holidays),
            'Holidays retrieved successfully'
        );
    }

    public function applyCompOff(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'worked_on' => 'required|date|before_or_equal:today',
            'reason' => 'required|string|max:500',
            'supporting_documents' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $workedDate = Carbon::parse($request->worked_on);

        // Check if already applied for this date
        $existing = CompensatoryOff::where('user_id', Auth::id())
            ->where('worked_date', $workedDate)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existing) {
            return $this->errorResponse('Compensatory off already requested for this date');
        }

        // Check if user actually worked on this date
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('check_in_time', $workedDate)
            ->where('status', 'present')
            ->first();

        if (! $attendance) {
            return $this->errorResponse('No attendance record found for the selected date');
        }

        // Check if it's a holiday or weekend
        $isHoliday = Holiday::whereDate('date', $workedDate)->exists();
        $isWeekend = $workedDate->isWeekend();

        if (! $isHoliday && ! $isWeekend) {
            return $this->errorResponse('Compensatory off can only be requested for holidays or weekends');
        }

        $compOff = CompensatoryOff::create([
            'user_id' => Auth::id(),
            'worked_date' => $workedDate,
            'hours_worked' => 8, // Default 8 hours
            'comp_off_days' => 1, // Default 1 day
            'reason' => $request->reason,
            'status' => 'pending',
            'expiry_date' => $workedDate->copy()->addDays(90), // Expires after 90 days
        ]);

        return $this->successResponse(
            new CompensatoryOffResource($compOff),
            'Compensatory off request submitted successfully'
        );
    }

    public function compOffList(Request $request): JsonResponse
    {
        $query = CompensatoryOff::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $compOffs = $query->paginate(20);

        return $this->paginatedResponse(
            $compOffs->through(fn ($item) => new CompensatoryOffResource($item)),
            'Compensatory offs retrieved successfully'
        );
    }

    public function approve(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        // Get leave request
        $leaveRequest = LeaveRequest::with('user')->find($id);

        if (! $leaveRequest) {
            return $this->notFoundResponse('Leave request not found');
        }

        // Check if current user is the manager of the leave requester
        if ($leaveRequest->user->reporting_to_id !== $user->id) {
            return $this->forbiddenResponse('You are not authorized to approve this leave request');
        }

        if ($leaveRequest->status !== 'pending') {
            return $this->errorResponse('This leave request has already been processed');
        }

        DB::beginTransaction();
        try {
            // Update leave request
            $leaveRequest->status = 'approved';
            $leaveRequest->approved_by_id = $user->id;
            $leaveRequest->approved_at = Carbon::now();
            $leaveRequest->approval_notes = $request->notes;
            $leaveRequest->save();

            // Deduct leave balance
            $availableLeave = UserAvailableLeave::where('user_id', $leaveRequest->user_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->first();

            if ($availableLeave) {
                $availableLeave->available_leaves -= $leaveRequest->total_days;
                $availableLeave->used_leaves += $leaveRequest->total_days;
                $availableLeave->save();
            }

            DB::commit();

            return $this->successResponse(null, 'Leave request approved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve leave request', [
                'leave_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to approve leave request', null, 500);
        }
    }

    public function reject(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = Auth::user();

        // Get leave request
        $leaveRequest = LeaveRequest::with('user')->find($id);

        if (! $leaveRequest) {
            return $this->notFoundResponse('Leave request not found');
        }

        // Check if current user is the manager of the leave requester
        if ($leaveRequest->user->reporting_to_id !== $user->id) {
            return $this->forbiddenResponse('You are not authorized to reject this leave request');
        }

        if ($leaveRequest->status !== 'pending') {
            return $this->errorResponse('This leave request has already been processed');
        }

        DB::beginTransaction();
        try {
            // Update leave request
            $leaveRequest->status = 'rejected';
            $leaveRequest->rejected_by_id = $user->id;
            $leaveRequest->rejected_at = Carbon::now();
            $leaveRequest->approval_notes = $request->reason;
            $leaveRequest->save();

            DB::commit();

            return $this->successResponse(null, 'Leave request rejected');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject leave request', [
                'leave_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to reject leave request', null, 500);
        }
    }

    public function teamLeaves(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Get all direct reports
        $teamMemberIds = \App\Models\User::where('reporting_to_id', $user->id)
            ->pluck('id');

        if ($teamMemberIds->isEmpty()) {
            return $this->successResponse([], 'No team members found');
        }

        $query = LeaveRequest::whereIn('user_id', $teamMemberIds)
            ->with(['user', 'leaveType'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->paginate(20);

        return $this->paginatedResponse(
            $leaves->through(fn ($item) => new LeaveRequestResource($item)),
            'Team leaves retrieved successfully'
        );
    }

    /**
     * Send notification to manager about new leave request
     */
    private function notifyManager(LeaveRequest $leaveRequest): void
    {
        try {
            $user = Auth::user();
            if ($user->reporting_to_id) {
                // TODO: Implement actual notification (email/push notification)
                Log::info('Leave request notification sent to manager', [
                    'leave_id' => $leaveRequest->id,
                    'employee_id' => $user->id,
                    'manager_id' => $user->reporting_to_id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send leave notification', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Calculate leave days between two dates
     */
    private function calculateLeaveDays(Carbon $fromDate, Carbon $toDate, ?bool $isHalfDay): float
    {
        if ($isHalfDay) {
            return 0.5;
        }

        $settingsService = app(ModuleSettingsService::class);
        $includeWeekends = $settingsService->get('HRCore', 'weekend_included_in_leave', false);
        $includeHolidays = $settingsService->get('HRCore', 'holidays_included_in_leave', false);

        $days = 0;
        $currentDate = $fromDate->copy();

        while ($currentDate <= $toDate) {
            $isWeekend = $currentDate->isWeekend();
            $isHoliday = Holiday::whereDate('date', $currentDate)->exists();

            // Skip weekends if not included
            if (! $includeWeekends && $isWeekend) {
                $currentDate->addDay();

                continue;
            }

            // Skip holidays if not included
            if (! $includeHolidays && $isHoliday) {
                $currentDate->addDay();

                continue;
            }

            $days++;
            $currentDate->addDay();
        }

        return $days;
    }
}
