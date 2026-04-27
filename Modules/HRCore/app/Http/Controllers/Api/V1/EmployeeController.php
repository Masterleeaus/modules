<?php

namespace Modules\HRCore\app\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\FileManagerCore\Contracts\FileManagerInterface;
use Modules\FileManagerCore\DTO\FileUploadRequest;
use Modules\FileManagerCore\Enums\FileType;
use Modules\FileManagerCore\Enums\FileVisibility;
use Modules\HRCore\app\Http\Controllers\Api\BaseApiController;
use Modules\HRCore\app\Http\Resources\BankAccountResource;
use Modules\HRCore\app\Http\Resources\UserResource;
use Modules\HRCore\app\Models\BankAccount;
use Modules\HRCore\app\Models\EmployeeHistory;

class EmployeeController extends BaseApiController
{
    public function profile(): JsonResponse
    {
        $user = Auth::user();
        $user->load(['designation.department', 'shift', 'team', 'reportingTo']);

        return $this->successResponse(
            new UserResource($user),
            'Profile retrieved successfully'
        );
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'alternate_number' => 'nullable|string|max:20',
            'gender' => 'sometimes|in:male,female,other',
            'dob' => 'sometimes|date|before:today',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = Auth::user();
        $user->update($request->only([
            'first_name',
            'last_name',
            'phone',
            'alternate_number',
            'gender',
            'dob',
            'address',
        ]));

        $user->load(['designation.department', 'shift', 'team', 'reportingTo']);

        return $this->successResponse(
            new UserResource($user),
            'Profile updated successfully'
        );
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = Auth::user();

        // Handle file upload using FileManagerCore if available
        if ($request->hasFile('avatar')) {
            try {
                if (app()->bound(FileManagerInterface::class)) {
                    $fileManager = app(FileManagerInterface::class);

                    // Delete old profile picture from FileManagerCore if exists
                    $existingProfilePicture = $user->getProfilePictureFile();
                    if ($existingProfilePicture) {
                        $fileManager->deleteFile($existingProfilePicture);
                    }

                    // Delete old profile picture from legacy storage for cleanup
                    if ($user->profile_picture) {
                        Storage::disk('public')->delete('employee_profiles/'.$user->profile_picture);
                        $user->profile_picture = null; // Clear legacy field
                    }

                    $uploadRequest = FileUploadRequest::fromRequest(
                        $request->file('avatar'),
                        FileType::EMPLOYEE_PROFILE_PICTURE,
                        User::class,
                        $user->id
                    )->withName($user->code.'_profile_mobile')
                        ->withVisibility(FileVisibility::INTERNAL)
                        ->withDescription('Employee profile picture updated via mobile app')
                        ->withMetadata([
                            'employee_code' => $user->code,
                            'updated_via' => 'mobile_app',
                            'updated_at' => now()->toISOString(),
                        ]);

                    $fileManager->uploadFile($uploadRequest);

                } else {
                    // Fallback to legacy storage if FileManagerCore is not available
                    $file = $request->file('avatar');
                    $fileName = $user->code.'_'.time().'.'.$file->getClientOriginalExtension();

                    // Delete old profile picture if exists
                    if ($user->profile_picture) {
                        Storage::disk('public')->delete('employee_profiles/'.$user->profile_picture);
                    }

                    Storage::disk('public')->putFileAs('employee_profiles', $file, $fileName);
                    $user->profile_picture = $fileName;
                    $user->save();
                }
            } catch (\Exception $e) {
                Log::error("Failed to update profile picture for employee: {$user->code}", [
                    'error' => $e->getMessage(),
                ]);

                return $this->errorResponse('Failed to upload profile picture', null, 500);
            }
        }

        return $this->successResponse(
            ['profile_picture' => $user->getProfilePicture()],
            'Avatar updated successfully'
        );
    }

    public function bankAccounts(): JsonResponse
    {
        $bankAccounts = BankAccount::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        return $this->successResponse(
            BankAccountResource::collection($bankAccounts),
            'Bank accounts retrieved successfully'
        );
    }

    public function history(Request $request): JsonResponse
    {
        $query = EmployeeHistory::where('user_id', Auth::id())
            ->orderBy('effective_date', 'desc');

        if ($request->has('type')) {
            $query->where('event_type', $request->type);
        }

        $history = $query->paginate(20);

        return $this->paginatedResponse(
            $history,
            'History retrieved successfully'
        );
    }
}
