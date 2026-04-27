<?php

namespace Modules\HRCore\App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\Status;
use App\Enums\UserAccountStatus;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\Settings\ModuleSettingsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\FileManagerCore\Contracts\FileManagerInterface;
use Modules\FileManagerCore\DTO\FileUploadRequest;
use Modules\FileManagerCore\Enums\FileType;
use Modules\FileManagerCore\Enums\FileVisibility;
use Modules\HRCore\App\Models\Designation;
use Modules\HRCore\App\Models\EmployeeHistory;
use Modules\HRCore\App\Models\Shift;
use Modules\HRCore\App\Models\Team;
use Modules\HRCore\App\Rules\ValidAttendanceType;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-employees|hrcore.view-own-employees')->only(['index', 'indexAjax', 'search']);
        $this->middleware('permission:hrcore.view-employees|hrcore.view-employee-details')->only(['show', 'myProfile']);
        $this->middleware('permission:hrcore.create-employees')->only(['create', 'store']);
        $this->middleware('permission:hrcore.edit-employees|hrcore.edit-employee-personal-info|hrcore.edit-employee-work-info')->only(['edit', 'update', 'updateStatus']);
        $this->middleware('permission:hrcore.delete-employees')->only(['destroy']);
        $this->middleware('permission:hrcore.manage-employee-lifecycle')->only(['changeLifecycleState']);
    }

    /**
     * Generate employee code based on settings
     */
    protected function generateEmployeeCode(): string
    {
        $settingsService = app(ModuleSettingsService::class);
        $prefix = $settingsService->get('HRCore', 'employee_code_prefix', 'EMP');
        $startNumber = (int) $settingsService->get('HRCore', 'employee_code_start_number', '1000');

        // Get the last employee code with this prefix
        $lastEmployee = User::where('code', 'like', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEmployee) {
            // Extract the number from the last code (handling formats like EMP-001 or EMP1000)
            $code = $lastEmployee->code;
            // Remove prefix and any non-numeric characters
            $numericPart = preg_replace('/[^0-9]/', '', str_replace($prefix, '', $code));
            $lastNumber = (int) $numericPart;
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = $startNumber;
        }

        // Format with leading zeros if the start number has them
        $numberLength = strlen((string) $startNumber);

        return $prefix.'-'.str_pad($nextNumber, $numberLength, '0', STR_PAD_LEFT);
    }

    /**
     * Display employee self-service profile (No admin permission required)
     */
    public function selfServiceProfile()
    {
        $user = auth()->user();

        // Load relationships (designation.department for nested relationship)
        $user->load(['team', 'designation.department', 'shift', 'roles', 'manager']);

        // Get employee's attendance statistics for current month
        $currentMonth = now()->startOfMonth();
        $attendanceStats = \Modules\HRCore\app\Models\Attendance::where('user_id', $user->id)
            ->whereBetween('created_at', [$currentMonth, now()])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get leave balance using the User model method
        $leaveBalance = $user->getLeaveBalances();

        return view('hrcore::employee.self-service-profile', compact('user', 'attendanceStats', 'leaveBalance'));
    }

    /**
     * Update employee's own profile
     */
    public function updateSelfProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'personal_email' => 'nullable|email|unique:users,personal_email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'blood_group' => 'nullable|string|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
        ]);

        try {
            $user->update($validated);

            return redirect()->route('hrcore.self-service.profile')
                ->with('success', 'Profile updated successfully.');
        } catch (Exception $e) {
            Log::error('Failed to update self profile: '.$e->getMessage());

            return back()->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Update profile photo
     */
    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();

        try {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Store new photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully.',
                'photo_url' => asset('storage/'.$path),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update profile photo: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile photo.',
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed|different:current_password',
        ]);

        $user = auth()->user();

        // Check current password
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return back()->with('success', 'Password changed successfully.');
        } catch (Exception $e) {
            Log::error('Failed to change password: '.$e->getMessage());

            return back()->with('error', 'Failed to change password. Please try again.');
        }
    }

    /**
     * Display employee listing page
     */
    public function index()
    {

        // Get statistics (excluding clients and tenants)
        $employeeQuery = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['client', 'tenant']);
        });

        $active = (clone $employeeQuery)->where('status', UserAccountStatus::ACTIVE)->count();
        $inactive = (clone $employeeQuery)->where('status', UserAccountStatus::INACTIVE)->count();
        $relieved = (clone $employeeQuery)->where('status', UserAccountStatus::RELIEVED)->count();
        $terminated = (clone $employeeQuery)->where('status', UserAccountStatus::TERMINATED)->count();

        // Get filter data (excluding client and tenant roles)
        $roles = Role::whereNotIn('name', ['client', 'tenant'])
            ->select('id', 'name')
            ->get();
        $teams = Team::where('status', Status::ACTIVE)
            ->select('id', 'name', 'code')
            ->get();
        $designations = Designation::where('status', Status::ACTIVE)
            ->select('id', 'name', 'code')
            ->get();

        return view('hrcore::employee.index', compact(
            'active',
            'inactive',
            'relieved',
            'terminated',
            'roles',
            'teams',
            'designations'
        ));
    }

    /**
     * DataTable server-side processing
     */
    public function indexAjax(Request $request)
    {
        $query = User::query()
            ->with(['team', 'designation', 'shift', 'roles'])
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['client', 'tenant']);
            });

        // Apply filters
        if ($request->filled('roleFilter')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->roleFilter);
            });
        }

        if ($request->filled('teamFilter')) {
            $query->where('team_id', $request->teamFilter);
        }

        if ($request->filled('designationFilter')) {
            $query->where('designation_id', $request->designationFilter);
        }

        if ($request->filled('statusFilter')) {
            $query->where('status', $request->statusFilter);
        }

        // Apply permission-based filtering
        if (auth()->user()->can('hrcore.view-own-employees') && ! auth()->user()->can('hrcore.view-employees')) {
            $query->where('reporting_to_id', auth()->id());
        }

        return DataTables::of($query)
            ->filterColumn('first_name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('code', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('user', function ($employee) {
                return view('components.datatable-user', ['user' => $employee])->render();
            })
            ->addColumn('role', function ($employee) {
                return $employee->roles->pluck('name')->implode(', ');
            })
            ->addColumn('team', function ($employee) {
                return $employee->team ? $employee->team->name : '-';
            })
            ->addColumn('designation', function ($employee) {
                return $employee->designation ? $employee->designation->name : '-';
            })
            ->addColumn('attendance_type', function ($employee) {
                $types = [
                    'open' => '<span class="badge bg-label-success">Open</span>',
                    'geofence' => '<span class="badge bg-label-info">Geofence</span>',
                    'ip_address' => '<span class="badge bg-label-warning">IP Address</span>',
                    'qr_code' => '<span class="badge bg-label-primary">QR Code</span>',
                    'site' => '<span class="badge bg-label-secondary">Site</span>',
                    'dynamic_qr' => '<span class="badge bg-label-dark">Dynamic QR</span>',
                    'face_recognition' => '<span class="badge bg-label-danger">Face Recognition</span>',
                ];

                return $types[$employee->attendance_type] ?? '-';
            })
            ->addColumn('status', function ($employee) {
                if ($employee->status instanceof UserAccountStatus) {
                    return $employee->status->badge();
                }

                // Fallback for string values
                try {
                    $status = UserAccountStatus::from($employee->status);

                    return $status->badge();
                } catch (\ValueError $e) {
                    return '<span class="badge bg-label-secondary">Unknown</span>';
                }
            })
            ->addColumn('actions', function ($employee) {
                $actions = [
                    [
                        'label' => __('View Profile'),
                        'icon' => 'bx bx-show',
                        'url' => route('hrcore.employees.show', $employee->id),
                    ],
                ];

                if (auth()->user()->can('hrcore.edit-employees')) {
                    $actions[] = [
                        'label' => __('Edit'),
                        'icon' => 'bx bx-edit',
                        'url' => route('hrcore.employees.edit', $employee->id),
                    ];
                }

                if (auth()->user()->can('hrcore.delete-employees')) {
                    $actions[] = [
                        'divider' => true,
                    ];
                    $actions[] = [
                        'label' => __('Delete'),
                        'icon' => 'bx bx-trash',
                        'onclick' => "deleteEmployee({$employee->id})",
                    ];
                }

                return view('components.datatable-actions', [
                    'id' => $employee->id,
                    'actions' => $actions,
                ])->render();
            })
            ->rawColumns(['user', 'attendance_type', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Search employees for dropdown/autocomplete
     */
    public function search(Request $request)
    {

        try {
            $query = $request->get('q', '');
            $page = $request->get('page', 1);
            $perPage = 20;

            $employees = User::whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['client', 'tenant']);
            })
                ->where('status', UserAccountStatus::ACTIVE)
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($subQuery) use ($query) {
                        $subQuery->where('first_name', 'like', "%{$query}%")
                            ->orWhere('last_name', 'like', "%{$query}%")
                            ->orWhere('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%")
                            ->orWhere('code', 'like', "%{$query}%");
                    });
                })
                ->select('id', 'first_name', 'last_name', 'name', 'code', 'email')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->paginate($perPage, ['*'], 'page', $page);

            // Format the response with proper full names
            $formattedEmployees = $employees->getCollection()->map(function ($employee) {
                $fullName = trim($employee->first_name.' '.$employee->last_name);
                if (empty($fullName)) {
                    $fullName = $employee->name ?: 'Unknown';
                }

                return [
                    'id' => $employee->id,
                    'name' => $fullName,
                    'code' => $employee->code,
                    'email' => $employee->email,
                ];
            });

            return response()->json([
                'data' => $formattedEmployees->toArray(),
                'has_more' => $employees->hasMorePages(),
                'total' => $employees->total(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => [],
                'has_more' => false,
                'total' => 0,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show employee create form
     */
    public function create()
    {

        $shifts = Shift::where('status', Status::ACTIVE)->get();
        $teams = Team::where('status', Status::ACTIVE)->get();
        $designations = Designation::where('status', Status::ACTIVE)->get();
        $roles = Role::whereNotIn('name', ['client', 'tenant'])->get();
        $reportingManagers = User::where('status', UserAccountStatus::ACTIVE)
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['client', 'tenant']);
            })
            ->select('id', 'first_name', 'last_name', 'code')
            ->get();

        // Get default password from settings
        $settingsService = app(ModuleSettingsService::class);
        $defaultPassword = $settingsService->get('HRCore', 'default_password', '123456');

        return view('hrcore::employee.create', compact(
            'shifts',
            'teams',
            'designations',
            'roles',
            'reportingManagers',
            'defaultPassword'
        ));
    }

    /**
     * Store new employee
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'code' => 'nullable|string|unique:users,code',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:'.implode(',', array_column(Gender::cases(), 'value')),
            'date_of_joining' => 'required|date',
            'designation_id' => 'required|exists:designations,id',
            'team_id' => 'required|exists:teams,id',
            'shift_id' => 'required|exists:shifts,id',
            'reporting_to_id' => 'nullable|exists:users,id',
            'role' => 'required|exists:roles,name',
            'attendance_type' => ['required', new ValidAttendanceType],
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'address' => 'nullable|string|max:500',
        ], [
            // Custom error messages
            'code.unique' => 'The employee number already exists. Please use a different employee number.',
            'email.unique' => 'The email address already exists. Please use a different email address.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Generate employee code if not provided
            $employeeCode = $request->code ?: $this->generateEmployeeCode();

            // Additional check for duplicate employee code (in case of auto-generation)
            if (User::where('code', $employeeCode)->exists()) {
                return redirect()->back()
                    ->withErrors(['code' => 'The employee number "'.$employeeCode.'" already exists. Please use a different employee number.'])
                    ->withInput();
            }

            // Create user
            $user = new User;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->name = $request->first_name.' '.$request->last_name; // Set the name field for login
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->code = $employeeCode;
            $user->dob = $request->date_of_birth;
            $user->gender = $request->gender;
            $user->date_of_joining = $request->date_of_joining;
            $user->address = $request->address;
            $user->designation_id = $request->designation_id;
            $user->team_id = $request->team_id;
            $user->shift_id = $request->shift_id;
            $user->reporting_to_id = $request->reporting_to_id;
            $user->attendance_type = $request->attendance_type;
            $user->status = UserAccountStatus::ACTIVE;

            // Get settings service
            $settingsService = app(ModuleSettingsService::class);

            // Set default password from settings
            $defaultPassword = $settingsService->get('HRCore', 'default_password', '123456');
            $user->password = bcrypt($defaultPassword);

            // Set probation period from settings
            $probationDays = (int) $settingsService->get('HRCore', 'default_probation_period', '90');
            if ($probationDays > 0) {
                $user->probation_end_date = now()->addDays($probationDays);
            }

            $user->save();

            // Handle profile picture using FileManagerCore
            if ($request->hasFile('profile_picture')) {
                Log::info("Profile picture upload attempt for new employee: {$user->code}", [
                    'file_name' => $request->file('profile_picture')->getClientOriginalName(),
                    'file_size' => $request->file('profile_picture')->getSize(),
                    'mime_type' => $request->file('profile_picture')->getMimeType(),
                ]);
                try {
                    if (app()->bound(FileManagerInterface::class)) {
                        Log::info('FileManagerInterface is bound - proceeding with FileManagerCore upload for new employee');
                        $fileManager = app(FileManagerInterface::class);

                        $uploadRequest = FileUploadRequest::fromRequest(
                            $request->file('profile_picture'),
                            FileType::EMPLOYEE_PROFILE_PICTURE,
                            User::class,
                            $user->id
                        )->withName($user->code.'_profile')
                            ->withVisibility(FileVisibility::INTERNAL)
                            ->withDescription('Employee profile picture')
                            ->withMetadata([
                                'employee_code' => $user->code,
                                'department' => $user->team_id,
                                'uploaded_by' => auth()->id(),
                                'uploaded_at' => now()->toISOString(),
                            ]);

                        $profilePictureFile = $fileManager->uploadFile($uploadRequest);

                        Log::info("Profile picture uploaded successfully for employee: {$user->code}", [
                            'file_id' => $profilePictureFile->id,
                            'file_name' => $profilePictureFile->name,
                        ]);
                    } else {
                        // Fallback to legacy storage if FileManagerCore is not available
                        $file = $request->file('profile_picture');
                        $fileName = $user->code.'_'.time().'.'.$file->getClientOriginalExtension();

                        if (! Storage::disk('public')->exists('employee_profiles')) {
                            Storage::disk('public')->makeDirectory('employee_profiles');
                        }

                        Storage::disk('public')->putFileAs('employee_profiles', $file, $fileName);
                        $user->profile_picture = $fileName;
                        $user->save();

                        Log::info("Profile picture uploaded using legacy storage for employee: {$user->code}");
                    }
                } catch (Exception $e) {
                    Log::error("Failed to upload profile picture for employee: {$user->code}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]);

                    // For debugging - temporarily throw the exception to see it
                    // Remove this after debugging
                    if (config('app.debug')) {
                        throw $e;
                    }

                    // Continue without profile picture rather than failing the entire employee creation
                }
            }

            // Assign role
            $user->assignRole($request->role);

            DB::commit();

            return redirect()->route('hrcore.employees.index')
                ->with('success', __('Employee created successfully'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Employee creation failed: '.$e->getMessage());

            // Check for specific database integrity errors
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                // Extract field name from error message if possible
                if (str_contains($e->getMessage(), 'users_code_unique') || str_contains($e->getMessage(), "'code'")) {
                    return redirect()->back()
                        ->withErrors(['code' => 'The employee number already exists. Please use a different employee number.'])
                        ->withInput();
                } elseif (str_contains($e->getMessage(), 'users_email_unique') || str_contains($e->getMessage(), "'email'")) {
                    return redirect()->back()
                        ->withErrors(['email' => 'The email address already exists. Please use a different email address.'])
                        ->withInput();
                }
            }

            return redirect()->back()
                ->with('error', __('Failed to create employee. Please try again.'))
                ->withInput();
        }
    }

    /**
     * Show current user's profile
     */
    public function myProfile()
    {
        return $this->show(auth()->id());
    }

    /**
     * Show employee details
     */
    public function show($id)
    {

        $employee = User::with([
            'team',
            'designation.department',
            'shift',
            'roles',
            'reportingTo',
            'bankAccounts',
            'attendances' => function ($query) {
                $query->latest()->limit(10);
            },
            'leaveRequests' => function ($query) {
                $query->latest()->limit(10);
            },
            'lifecycleStates.createdBy',
            'lifecycleStates.approvedBy',
            'employeeHistories.changedBy',
        ])->findOrFail($id);

        // Get leave balances for all active leave types
        $leaveTypes = \Modules\HRCore\app\Models\LeaveType::where('status', Status::ACTIVE)->get();
        $leaveBalances = [];

        foreach ($leaveTypes as $leaveType) {
            $balance = $employee->getLeaveBalance($leaveType->id);
            $pendingLeaves = \Modules\HRCore\app\Models\LeaveRequest::where('user_id', $employee->id)
                ->where('leave_type_id', $leaveType->id)
                ->where('status', \App\Enums\LeaveRequestStatus::PENDING->value)
                ->sum('total_days');

            $leaveBalances[] = [
                'leaveType' => $leaveType,
                'totalBalance' => $balance,
                'pendingLeaves' => $pendingLeaves,
                'availableBalance' => $balance - $pendingLeaves,
            ];
        }

        return view('hrcore::employee.show', compact('employee', 'leaveBalances'));
    }

    /**
     * Show employee edit form
     */
    public function edit($id)
    {

        $employee = User::findOrFail($id);
        $shifts = Shift::where('status', Status::ACTIVE)->get();
        $teams = Team::where('status', Status::ACTIVE)->get();
        $designations = Designation::where('status', Status::ACTIVE)->get();
        $roles = Role::whereNotIn('name', ['client', 'tenant'])->get();
        $reportingManagers = User::where('status', UserAccountStatus::ACTIVE)
            ->where('id', '!=', $id)
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['client', 'tenant']);
            })
            ->select('id', 'first_name', 'last_name', 'code')
            ->get();
        $currentRole = $employee->roles->first()->name ?? null;

        return view('hrcore::employee.edit', compact(
            'employee',
            'shifts',
            'teams',
            'designations',
            'roles',
            'reportingManagers',
            'currentRole'
        ));
    }

    /**
     * Update employee
     */
    public function update(Request $request, $id)
    {

        $employee = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'required|string|max:20',
            'code' => 'required|string|unique:users,code,'.$id,
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:'.implode(',', array_column(Gender::cases(), 'value')),
            'date_of_joining' => 'required|date',
            'designation_id' => 'required|exists:designations,id',
            'team_id' => 'required|exists:teams,id',
            'shift_id' => 'required|exists:shifts,id',
            'reporting_to_id' => 'nullable|exists:users,id',
            'attendance_type' => ['required', new ValidAttendanceType],
            'status' => 'sometimes|required|in:'.implode(',', array_column(UserAccountStatus::cases(), 'value')),
            'address' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'role' => 'sometimes|required|exists:roles,name',
        ], [
            // Custom error messages
            'code.unique' => 'The employee number already exists. Please use a different employee number.',
            'email.unique' => 'The email address already exists. Please use a different email address.',
        ]);

        if ($validator->fails()) {
            // Return validation errors as JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'failed',
                    'data' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Store old data for history tracking
            $oldData = [
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'designation_id' => $employee->designation_id,
                'team_id' => $employee->team_id,
                'shift_id' => $employee->shift_id,
                'reporting_to_id' => $employee->reporting_to_id,
                'status' => $employee->status,
            ];

            // Update personal information
            if (auth()->user()->can('hrcore.edit-employee-personal-info')) {
                $employee->first_name = $request->first_name;
                $employee->last_name = $request->last_name;
                $employee->name = $request->first_name.' '.$request->last_name; // Set the name field for login
                $employee->email = $request->email;
                $employee->phone = $request->phone;
                $employee->dob = $request->date_of_birth;
                $employee->gender = $request->gender;
                $employee->address = $request->address;

                // Handle profile picture using FileManagerCore
                if ($request->hasFile('profile_picture')) {
                    Log::info("Profile picture upload attempt for employee: {$employee->code}", [
                        'file_name' => $request->file('profile_picture')->getClientOriginalName(),
                        'file_size' => $request->file('profile_picture')->getSize(),
                        'mime_type' => $request->file('profile_picture')->getMimeType(),
                    ]);
                    try {
                        if (app()->bound(FileManagerInterface::class)) {
                            Log::info('FileManagerInterface is bound - proceeding with FileManagerCore upload');
                            $fileManager = app(FileManagerInterface::class);

                            // Delete old profile picture from FileManagerCore if exists
                            $existingProfilePicture = $employee->getProfilePictureFile();
                            if ($existingProfilePicture) {
                                $fileManager->deleteFile($existingProfilePicture);
                                Log::info("Old profile picture deleted for employee: {$employee->code}", [
                                    'old_file_id' => $existingProfilePicture->id,
                                ]);
                            }

                            // Delete old profile picture from legacy storage for cleanup
                            if ($employee->profile_picture) {
                                Storage::disk('public')->delete('employee_profiles/'.$employee->profile_picture);
                                $employee->profile_picture = null; // Clear legacy field
                            }

                            $uploadRequest = FileUploadRequest::fromRequest(
                                $request->file('profile_picture'),
                                FileType::EMPLOYEE_PROFILE_PICTURE,
                                User::class,
                                $employee->id
                            )->withName($employee->code.'_profile_updated')
                                ->withVisibility(FileVisibility::INTERNAL)
                                ->withDescription('Updated employee profile picture')
                                ->withMetadata([
                                    'employee_code' => $employee->code,
                                    'department' => $employee->team_id,
                                    'updated_by' => auth()->id(),
                                    'updated_at' => now()->toISOString(),
                                ]);

                            $profilePictureFile = $fileManager->uploadFile($uploadRequest);

                            Log::info("Profile picture updated successfully for employee: {$employee->code}", [
                                'file_id' => $profilePictureFile->id,
                                'file_name' => $profilePictureFile->name,
                            ]);
                        } else {
                            Log::warning('FileManagerInterface not bound - falling back to legacy storage');
                            // Fallback to legacy storage if FileManagerCore is not available
                            $file = $request->file('profile_picture');
                            $fileName = $employee->code.'_'.time().'.'.$file->getClientOriginalExtension();

                            // Delete old profile picture if exists
                            if ($employee->profile_picture) {
                                Storage::disk('public')->delete('employee_profiles/'.$employee->profile_picture);
                            }

                            Storage::disk('public')->putFileAs('employee_profiles', $file, $fileName);
                            $employee->profile_picture = $fileName;

                            Log::info("Profile picture updated using legacy storage for employee: {$employee->code}");
                        }
                    } catch (Exception $e) {
                        Log::error("Failed to update profile picture for employee: {$employee->code}", [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ]);

                        // Continue without updating profile picture rather than failing the entire update
                    }
                }
            }

            // Update work information
            if (auth()->user()->can('hrcore.edit-employee-work-info')) {
                $employee->code = $request->code;
                $employee->date_of_joining = $request->date_of_joining;
                $employee->designation_id = $request->designation_id;
                $employee->team_id = $request->team_id;
                $employee->shift_id = $request->shift_id;
                $employee->reporting_to_id = $request->reporting_to_id;
                $employee->attendance_type = $request->attendance_type;
            }

            // Update status
            if (auth()->user()->can('hrcore.manage-employee-status') && $request->has('status')) {
                $employee->status = $request->status;
            }

            $employee->save();

            // Update role if provided and user has permission
            if (auth()->user()->can('hrcore.manage-user-roles') && $request->has('role')) {
                $employee->syncRoles([$request->role]);
            }

            // Create history records for significant changes
            $newData = [
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'designation_id' => $employee->designation_id,
                'team_id' => $employee->team_id,
                'shift_id' => $employee->shift_id,
                'reporting_to_id' => $employee->reporting_to_id,
                'status' => $employee->status,
            ];

            // Determine event type based on changes
            $eventType = \Modules\HRCore\App\Models\EmployeeHistory::EVENT_WORK_INFO_UPDATE;

            if ($oldData['designation_id'] != $newData['designation_id']) {
                $eventType = \Modules\HRCore\App\Models\EmployeeHistory::EVENT_DESIGNATION_CHANGE;
            } elseif ($oldData['team_id'] != $newData['team_id']) {
                $eventType = \Modules\HRCore\App\Models\EmployeeHistory::EVENT_TEAM_TRANSFER;
            } elseif ($oldData['reporting_to_id'] != $newData['reporting_to_id']) {
                $eventType = \Modules\HRCore\App\Models\EmployeeHistory::EVENT_REPORTING_CHANGE;
            } elseif ($oldData['status'] != $newData['status']) {
                $eventType = \Modules\HRCore\App\Models\EmployeeHistory::EVENT_STATUS_CHANGE;
            } elseif ($oldData['shift_id'] != $newData['shift_id']) {
                $eventType = \Modules\HRCore\App\Models\EmployeeHistory::EVENT_SHIFT_CHANGE;
            }

            // Only create history if there are actual changes
            if ($oldData != $newData) {
                \Modules\HRCore\App\Models\EmployeeHistory::recordChange(
                    $employee,
                    $eventType,
                    $oldData,
                    $newData,
                    $request->reason,
                    $request->remarks
                );
            }

            DB::commit();

            // Return appropriate response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => ['message' => __('Employee updated successfully')],
                ]);
            }

            return redirect()->route('hrcore.employees.show', $employee->id)
                ->with('success', __('Employee updated successfully'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Employee update failed: '.$e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'failed',
                    'data' => __('Failed to update employee'),
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Failed to update employee. Please try again.'))
                ->withInput();
        }
    }

    /**
     * Delete employee
     */
    public function destroy($id)
    {

        try {
            $employee = User::findOrFail($id);

            // Soft delete the employee
            $employee->delete();

            return response()->json([
                'status' => 'success',
                'data' => ['message' => __('Employee deleted successfully')],
            ]);

        } catch (Exception $e) {
            Log::error('Employee deletion failed: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to delete employee'),
            ], 500);
        }
    }

    /**
     * Update employee status
     */
    public function updateStatus(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:'.implode(',', array_column(UserAccountStatus::cases(), 'value')),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'data' => $validator->errors(),
            ], 422);
        }

        try {
            $employee = User::findOrFail($id);
            $employee->status = $request->status;
            $employee->save();

            return response()->json([
                'status' => 'success',
                'data' => ['message' => __('Employee status updated successfully')],
            ]);

        } catch (Exception $e) {
            Log::error('Employee status update failed: '.$e->getMessage());

            return response()->json([
                'status' => 'failed',
                'data' => __('Failed to update employee status'),
            ], 500);
        }
    }

    /**
     * Change employee lifecycle state
     */
    public function changeLifecycleState(Request $request, $id)
    {
        $this->authorize('hrcore.manage-employee-lifecycle');

        $request->validate([
            'state' => 'required|in:onboarding,active,inactive,probation,relieved,terminated,retired,resigned,suspended',
            'effective_date' => 'required|date',
            'reason' => 'nullable|string|max:500',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $employee = User::whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['client', 'tenant']);
            })->findOrFail($id);

            // Get current lifecycle state
            $currentState = DB::table('employee_lifecycle_states')
                ->where('user_id', $employee->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // Create new lifecycle state record
            DB::table('employee_lifecycle_states')->insert([
                'user_id' => $employee->id,
                'state' => $request->state,
                'previous_state' => $currentState ? $currentState->state : null,
                'effective_date' => $request->effective_date,
                'reason' => $request->reason,
                'remarks' => $request->remarks,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update employee status based on lifecycle state
            $statusMapping = [
                'onboarding' => 'active',
                'active' => 'active',
                'inactive' => 'inactive',
                'probation' => 'active',
                'relieved' => 'inactive',
                'terminated' => 'inactive',
                'retired' => 'inactive',
                'resigned' => 'inactive',
                'suspended' => 'suspended',
            ];

            $employee->employee_status = $request->state;

            // Map to account status if needed
            if (isset($statusMapping[$request->state])) {
                $accountStatus = $statusMapping[$request->state];
                $employee->status = UserAccountStatus::from($accountStatus);
            }

            // Set resignation/exit dates if applicable
            if (in_array($request->state, ['resigned', 'relieved', 'terminated'])) {
                $employee->resignation_date = $request->effective_date;
                $employee->last_working_date = $request->effective_date;
                $employee->exit_reason = $request->reason;
                $employee->exit_remarks = $request->remarks;
            }

            $employee->save();

            // Log the change in employee history
            EmployeeHistory::create([
                'user_id' => $employee->id,
                'event_type' => 'lifecycle_change',
                'old_data' => ['state' => $currentState ? $currentState->state : 'new_employee'],
                'new_data' => ['state' => $request->state],
                'reason' => $request->reason,
                'remarks' => $request->remarks,
                'changed_by' => auth()->id(),
                'effective_date' => $request->effective_date,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Employee lifecycle state changed successfully'),
                'redirect' => route('hrcore.employees.show', $employee->id),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to change employee lifecycle state: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Failed to change employee lifecycle state'),
            ], 500);
        }
    }
}
