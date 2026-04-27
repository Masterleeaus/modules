<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Requests\Admin\Employee\StoreRequest as EmployeeStoreRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Recruit\Entities\JobApplicant;
use Modules\Recruit\Entities\JobPosting;
use Modules\Recruit\Entities\RecruitSetting;

/**
 * Manages the simplified FSM applicant pipeline (pre-employee state).
 *
 * The "hire" action converts an applicant into an employee by delegating
 * to EmployeeController@store, keeping a single source of truth for
 * employee creation. After hire, `converted_employee_id` is set to prevent
 * duplicate conversions.
 */
class JobApplicantController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.jobApplication');
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index()
    {
        $viewPermission = user()->permission('view_applicants');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->applicants   = JobApplicant::with(['jobPosting', 'interviewer'])->latest()->paginate(25);
        $this->positions    = JobApplicant::POSITIONS;
        $this->statuses     = JobApplicant::STATUSES;
        $this->jobPostings  = JobPosting::where('status', 'published')->get();

        return view('recruit::job-applicants.index', $this->data);
    }

    // -------------------------------------------------------------------------
    // Create / Store
    // -------------------------------------------------------------------------

    public function create()
    {
        $addPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->positions   = JobApplicant::POSITIONS;
        $this->sources     = JobApplicant::SOURCES;
        $this->jobPostings = JobPosting::where('status', 'published')->get();

        return view('recruit::job-applicants.create', $this->data);
    }

    public function store(Request $request)
    {
        $addPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'position_applied' => 'required|string|max:255',
            'resume'           => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Prevent duplicate applications within the same company
        $existing = JobApplicant::withTrashed()
            ->where('company_id', company()->id)
            ->where('email', $request->email)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            return Reply::error(__('recruit::app.applicantEmailExists'));
        }

        $applicant                  = new JobApplicant();
        $applicant->company_id      = company()->id;
        $applicant->first_name      = $request->first_name;
        $applicant->last_name       = $request->last_name;
        $applicant->email           = $request->email;
        $applicant->phone           = $request->phone;
        $applicant->position_applied = $request->position_applied;
        $applicant->status          = 'applied';
        $applicant->job_posting_id  = $request->job_posting_id;
        $applicant->cover_letter    = $request->cover_letter;
        $applicant->source          = $request->source;
        $applicant->availability_date = $request->availability_date;

        // Store resume on private disk
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store(
                JobApplicant::RESUME_PATH,
                JobApplicant::RESUME_DISK
            );
            $applicant->resume_path = $path;
        }

        $applicant->save();

        return Reply::successWithData(
            __('messages.recordSaved'),
            ['redirectUrl' => route('recruit.job-applicants.show', $applicant->id)]
        );
    }

    // -------------------------------------------------------------------------
    // Show / Edit / Update
    // -------------------------------------------------------------------------

    public function show(JobApplicant $jobApplicant)
    {
        $viewPermission = user()->permission('view_applicants');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->applicant  = $jobApplicant;
        $this->positions  = JobApplicant::POSITIONS;
        $this->statuses   = JobApplicant::STATUSES;

        return view('recruit::job-applicants.show', $this->data);
    }

    public function edit(JobApplicant $jobApplicant)
    {
        $editPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($editPermission, ['all', 'added', 'owned', 'both']));

        $this->applicant   = $jobApplicant;
        $this->positions   = JobApplicant::POSITIONS;
        $this->sources     = JobApplicant::SOURCES;
        $this->jobPostings = JobPosting::where('status', 'published')->get();

        return view('recruit::job-applicants.edit', $this->data);
    }

    public function update(Request $request, JobApplicant $jobApplicant)
    {
        $editPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($editPermission, ['all', 'added', 'owned', 'both']));

        $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'position_applied' => 'required|string|max:255',
            'status'           => 'required|in:' . implode(',', array_keys(JobApplicant::STATUSES)),
            'resume'           => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $jobApplicant->first_name       = $request->first_name;
        $jobApplicant->last_name        = $request->last_name;
        $jobApplicant->email            = $request->email;
        $jobApplicant->phone            = $request->phone;
        $jobApplicant->position_applied  = $request->position_applied;
        $jobApplicant->status           = $request->status;
        $jobApplicant->job_posting_id   = $request->job_posting_id;
        $jobApplicant->cover_letter     = $request->cover_letter;
        $jobApplicant->source           = $request->source;
        $jobApplicant->availability_date = $request->availability_date;
        $jobApplicant->interviewer_id   = $request->interviewer_id;

        if ($request->has('interview_notes')) {
            $existing = $jobApplicant->interview_notes ?? [];
            $existing[] = [
                'note' => $request->interview_notes,
                'by'   => user()->id,
                'at'   => now()->toIso8601String(),
            ];
            $jobApplicant->interview_notes = $existing;
        }

        if ($request->hasFile('resume')) {
            // Delete old resume if present
            if ($jobApplicant->resume_path) {
                Storage::disk(JobApplicant::RESUME_DISK)->delete($jobApplicant->resume_path);
            }

            $path = $request->file('resume')->store(
                JobApplicant::RESUME_PATH,
                JobApplicant::RESUME_DISK
            );
            $jobApplicant->resume_path = $path;
        }

        $jobApplicant->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function destroy(JobApplicant $jobApplicant)
    {
        $deletePermission = user()->permission('manage_recruitment');
        abort_403(!in_array($deletePermission, ['all', 'added', 'owned', 'both']));

        // Delete stored resume
        if ($jobApplicant->resume_path) {
            Storage::disk(JobApplicant::RESUME_DISK)->delete($jobApplicant->resume_path);
        }

        $jobApplicant->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }

    // -------------------------------------------------------------------------
    // Hire action — converts applicant → employee via EmployeeController@store
    // -------------------------------------------------------------------------

    /**
     * Convert a hired applicant into a Worksuite employee.
     *
     * Delegates to EmployeeController@store so that ALL employee creation
     * logic (roles, notifications, onboarding) lives in one place. After a
     * successful hire the applicant record is updated with:
     *   - status = 'hired'
     *   - converted_employee_id = new user.id
     *   - offer_accepted_at = now()
     */
    public function hire(Request $request, JobApplicant $jobApplicant)
    {
        $hirePermission = user()->permission('hire_applicant');
        abort_403(!in_array($hirePermission, ['all', 'added']));

        // Guard: already hired
        if ($jobApplicant->isHired()) {
            return Reply::error(__('recruit::app.applicantAlreadyHired'));
        }

        $request->validate([
            'joining_date'  => 'required|date',
            'department_id' => 'nullable|integer',
            'designation_id' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            // Build a synthetic request for EmployeeController@store
            $employeeRequest = new \Illuminate\Http\Request();
            $employeeRequest->replace([
                'name'             => $jobApplicant->full_name,
                'email'            => $jobApplicant->email,
                'mobile'           => $jobApplicant->phone,
                'joining_date'     => $request->joining_date,
                'department_id'    => $request->department_id,
                'designation_id'   => $request->designation_id,
                'employment_type'  => $request->employment_type ?? 'casual',
            ]);

            $employeeController = app(EmployeeController::class);

            /** @var \Illuminate\Http\JsonResponse $response */
            $response = $employeeController->store($employeeRequest);
            $responseData = $response->getData(true);

            if (!isset($responseData['status']) || $responseData['status'] !== 'success') {
                DB::rollBack();

                return Reply::error($responseData['error'] ?? __('recruit::app.hireFailedEmployeeCreate'));
            }

            // Extract the new user ID from the response
            $newUserId = $responseData['id'] ?? null;

            // Update applicant record
            $jobApplicant->status               = 'hired';
            $jobApplicant->converted_employee_id = $newUserId;
            $jobApplicant->offer_accepted_at    = Carbon::now();
            $jobApplicant->save();

            DB::commit();

            return Reply::successWithData(
                __('recruit::app.applicantHiredSuccess'),
                ['redirectUrl' => route('employees.show', $newUserId)]
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            return Reply::error($e->getMessage());
        }
    }
}
