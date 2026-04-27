<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Recruit\Entities\JobPosting;
use Modules\Recruit\Entities\RecruitSetting;

/**
 * CRUD management of job postings (vacancy management).
 */
class JobPostingController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Job Postings';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index()
    {
        $viewPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->jobPostings = JobPosting::latest()->paginate(25);
        $this->positions   = JobPosting::POSITION_TYPES;
        $this->statuses    = JobPosting::STATUSES;

        return view('recruit::job-postings.index', $this->data);
    }

    public function create()
    {
        $addPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->positions       = JobPosting::POSITION_TYPES;
        $this->employmentTypes = JobPosting::EMPLOYMENT_TYPES;

        return view('recruit::job-postings.create', $this->data);
    }

    public function store(Request $request)
    {
        $addPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $request->validate([
            'title'         => 'required|string|max:255',
            'position_type' => 'required|string|max:255',
            'status'        => 'nullable|in:draft,published,closed',
        ]);

        $posting                  = new JobPosting();
        $posting->company_id      = company()->id;
        $posting->title           = $request->title;
        $posting->position_type   = $request->position_type;
        $posting->description     = $request->description;
        $posting->requirements    = $request->requirements;
        $posting->location        = $request->location;
        $posting->employment_type = $request->employment_type ?? 'casual';
        $posting->pay_rate        = $request->pay_rate;
        $posting->pay_unit        = $request->pay_unit ?? 'hour';
        $posting->status          = $request->status ?? 'draft';
        $posting->close_date      = $request->close_date;
        $posting->created_by      = user()->id;
        $posting->slug            = Str::slug($request->title . '-' . uniqid());
        $posting->save();

        return Reply::successWithData(
            __('messages.recordSaved'),
            ['redirectUrl' => route('recruit.job-postings.index')]
        );
    }

    public function show(JobPosting $jobPosting)
    {
        $viewPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->jobPosting = $jobPosting->load('applicants');

        return view('recruit::job-postings.show', $this->data);
    }

    public function edit(JobPosting $jobPosting)
    {
        $editPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($editPermission, ['all', 'added', 'owned', 'both']));

        $this->jobPosting      = $jobPosting;
        $this->positions       = JobPosting::POSITION_TYPES;
        $this->employmentTypes = JobPosting::EMPLOYMENT_TYPES;
        $this->statuses        = JobPosting::STATUSES;

        return view('recruit::job-postings.edit', $this->data);
    }

    public function update(Request $request, JobPosting $jobPosting)
    {
        $editPermission = user()->permission('manage_recruitment');
        abort_403(!in_array($editPermission, ['all', 'added', 'owned', 'both']));

        $request->validate([
            'title'         => 'required|string|max:255',
            'position_type' => 'required|string|max:255',
            'status'        => 'nullable|in:draft,published,closed',
        ]);

        $jobPosting->title           = $request->title;
        $jobPosting->position_type   = $request->position_type;
        $jobPosting->description     = $request->description;
        $jobPosting->requirements    = $request->requirements;
        $jobPosting->location        = $request->location;
        $jobPosting->employment_type = $request->employment_type ?? $jobPosting->employment_type;
        $jobPosting->pay_rate        = $request->pay_rate;
        $jobPosting->pay_unit        = $request->pay_unit ?? $jobPosting->pay_unit;
        $jobPosting->status          = $request->status ?? $jobPosting->status;
        $jobPosting->close_date      = $request->close_date;
        $jobPosting->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function destroy(JobPosting $jobPosting)
    {
        $deletePermission = user()->permission('manage_recruitment');
        abort_403(!in_array($deletePermission, ['all', 'added', 'owned', 'both']));

        $jobPosting->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }
}
