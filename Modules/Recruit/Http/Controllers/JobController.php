<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\CompanyAddress;
use App\Models\Currency;
use App\Models\EmployeeDetails;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Recruit\DataTables\InterviewScheduleDataTable;
use Modules\Recruit\DataTables\JobApplicationsDataTable;
use Modules\Recruit\DataTables\JobDataTable;
use Modules\Recruit\DataTables\JobOfferLetterDataTable;
use Modules\Recruit\Entities\JobInterviewStage;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitCustomQuestion;
use Modules\Recruit\Entities\Recruiter;
use Modules\Recruit\Entities\RecruitInterviewStage;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobAddress;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitJobCategory;
use Modules\Recruit\Entities\RecruitJobHistory;
use Modules\Recruit\Entities\RecruitJobOfferLetter;
use Modules\Recruit\Entities\RecruitJobQuestion;
use Modules\Recruit\Entities\RecruitJobSkill;
use Modules\Recruit\Entities\RecruitJobSubCategory;
use Modules\Recruit\Entities\RecruitJobType;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Entities\RecruitSkill;
use Modules\Recruit\Entities\RecruitWorkExperience;
use Modules\Recruit\Http\Requests\StoreJobRequest;

class JobController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'recruit::app.menu.job';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, user()->modules));
            // dd($this->user);
            return $next($request);
        });
    }

    public function index(JobDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_job');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->departments = Team::all();
        $this->employees = Recruiter::with('user:id,name,image')
            ->where('status', '=', 'enabled')
            ->get();
        $this->locations = CompanyAddress::all();

        return $dataTable->render('recruit::jobs.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = __('recruit::modules.job.addJob');

        $addPermission = user()->permission('add_job');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->job = (request()['duplicate_job']) ? RecruitJob::with('team', 'skills', 'address', 'workExperience', 'employee', 'jobType', 'recruiter')->findOrFail(request()['duplicate_job']) : null;
        $this->jobSkills = RecruitJobSkill::where('recruit_job_id', request()['duplicate_job'])->get()->pluck('recruit_skill_id')->toArray();
        $this->jobLocation = RecruitJobAddress::where('recruit_job_id', request()['duplicate_job'])->get()->pluck('company_address_id')->toArray();

        $this->departments = Team::all();
        $this->workExperience = RecruitWorkExperience::all();
        $this->jobTypes = RecruitJobType::all();
        $this->skills = RecruitSkill::all();
        $this->locations = CompanyAddress::all();
        $this->employees = Recruiter::with('user')->where('status', '=', 'enabled')->get();
        $this->stages = RecruitInterviewStage::all();
        $this->jobInterviews = JobInterviewStage::where('recruit_job_id', request()['duplicate_job'])->pluck('recruit_interview_stage_id')->toArray();
        $this->categories = RecruitJobCategory::all();
        $this->currencies = Currency::all();
        $this->Subcategories = RecruitJobSubCategory::all();
        $this->questions = RecruitCustomQuestion::where('status', 'enable')->where('category', 'job_application')->get();

        if (request()->ajax()) {
            $html = view('recruit::jobs.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::jobs.ajax.create';

        return view('recruit::jobs.create', $this->data);
    }

    public function show($id)
    {
        $this->job = RecruitJob::with('team', 'files', 'employee', 'jobType', 'category', 'subcategory')->withTrashed()->find($id);
        $this->pageTitle = ucwords($this->job->title);

        if ($this->job->currency_id != null) {
            $this->currencySymbol = Currency::where('id', '=', $this->job->currency_id)->first();
        }
        else {
            $this->currencySymbol = null;
        }

        $this->viewPermission = user()->permission('view_job');
        abort_403(!($this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && $this->job->added_by == user()->id)
            || ($this->viewPermission == 'owned' && user()->id == $this->job->recruiter_id)
            || ($this->viewPermission == 'both' && user()->id == $this->job->recruiter_id)
            || $this->job->added_by == user()->id));

        $this->statusSymbol = ($this->job->status == 'open') ? 'light-green' : 'red';

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';

        switch ($tab) {
        case 'interview':
            return $this->interview($id);
        case 'candidate':
            return $this->candidate($id);
        case 'offerletter':
            return $this->jobOffer($id);
        case 'history':
            return $this->history($id);
        default:
            $this->view = 'recruit::jobs.ajax.profile';
            break;
        }

        $this->openingsCount = $this->job->total_positions;

        $this->inProgressCount = RecruitJobApplication::where('recruit_job_id', $this->job->id)->whereHas('job', function ($q) {
            $q->where('status', '=', 'open')
                ->where(
                    function ($query) {
                        return $query
                            ->where('recruit_application_status_id', '!=', 4)
                            ->where('recruit_application_status_id', '!=', 5);
                    });
        })->count();

        $this->scheduledCount = RecruitJobApplication::where('recruit_job_id', $this->job->id)
            ->where('recruit_application_status_id', 3)
            ->count();
        $this->offerReleasedCount = RecruitJobOfferLetter::where('recruit_job_id', $this->job->id)->count();
        $this->applicationStatus = $this->applicationChartData($this->job);

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('recruit::jobs.show', $this->data);
    }

    public function applicationChartData($job)
    {
        $labels = ['4', '5'];
        $data['labels'] = [__('recruit::app.menu.hired'), __('app.rejected')];
        $data['colors'] = ['#0A66C2', '#ee1127'];
        $data['values'] = [];

        foreach ($labels as $label) {
            $data['values'][] = RecruitJobApplication::where('recruit_job_id', $job->id)->where('recruit_application_status_id', $label)->count();
        }

        return $data;
    }

    public function jobOffer($id)
    {
        $viewPermission = user()->permission('view_offer_letter');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->jobId = $id;
        $this->jobs = RecruitJob::all();

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->view = 'recruit::jobs.ajax.offerTab';

        $dataTable = new JobOfferLetterDataTable;

        return $dataTable->render('recruit::jobs.show', $this->data);
    }

    public function candidate($id)
    {
        $viewPermission = user()->permission('view_job_application');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));
        $this->jobId = $id;
        $this->jobs = RecruitJob::all();
        $this->jobApp = RecruitJob::where('id', $id)->first();
        $this->locations = CompanyAddress::all();
        $this->applicationStatus = RecruitApplicationStatus::all();

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->view = 'recruit::jobs.ajax.candidate';

        $dataTable = new JobApplicationsDataTable;

        return $dataTable->render('recruit::jobs.show', $this->data);
    }

    public function history($id)
    {
        $viewPermission = user()->permission('view_job');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned']));

        $this->activity = RecruitJobHistory::where('recruit_job_id', $id)->orderByDesc('updated_at')->get();
        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->view = 'recruit::jobs.ajax.history';

        $dataTable = new JobApplicationsDataTable;

        return $dataTable->render('recruit::jobs.show', $this->data);
    }

    public function interview($id)
    {
        $viewPermission = user()->permission('view_interview_schedule');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->jobId = $id;
        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->view = 'recruit::jobs.ajax.interview';

        $dataTable = new InterviewScheduleDataTable;

        return $dataTable->render('recruit::jobs.show', $this->data);
    }

    public function store(StoreJobRequest $request)
    {

        $addPermission = user()->permission('add_job');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $endDate = !$request->has('without_end_date') ? Carbon::createFromFormat($this->company->date_format, $request->end_date)->format('Y-m-d') : null;

        $job = new RecruitJob;
        $job->title = $request->title;
        $job->slug = Str::slug($request->title, '-');
        $job->job_description = $request->job_description == '<p><br></p>' ? null : $request->job_description;
        $job->total_positions = $request->total_positions;
        $job->remaining_openings = $request->total_positions;
        $job->department_id = $request->department_id;
        $job->recruit_job_type_id = $request->job_type_id;
        $job->start_date = Carbon::createFromFormat($this->company->date_format, $request->start_date)->format('Y-m-d');
        $job->end_date = $endDate;
        $job->status = $request->status;
        $job->recruit_job_category_id = $request->category_id;
        $job->recruit_job_sub_category_id = $request->sub_category_id;
        $job->currency_id = $request->currency_id;
        $job->meta_details = [
            'title' => $request->meta_title ?: $request->title,
            'description' => $request->meta_description ?: strip_tags(Str::substr(html_entity_decode($request->job_description), 0, 150)),
        ];

        $job->recruiter_id = $request->recruiter;
        $job->recruit_work_experience_id = $request->work_experience;
        $job->pay_type = $request->paytype;
        $job->start_amount = $request->start_amount;
        $job->end_amount = $request->end_amount;
        $job->pay_according = $request->pay_according;
        $job->disclose_salary = $request->disclose_salary ?: 'no';
        $job->remote_job = $request->remote_job ?: 'no';
        $job->is_photo_require = $request->is_photo_require ?: '0';
        $job->is_resume_require = $request->is_resume_require ?: '0';
        $job->is_dob_require = $request->is_dob_require ?: '0';
        $job->is_gender_require = $request->is_gender_require ?: '0';
        $job->is_currentctc_require = $request->is_currentctc_require ?: '0';
        $job->is_expectedctc_require = $request->is_expectedctc_require ?: '0';
        $job->save();

        if (!empty($request->skill_id)) {
            foreach ($request->skill_id as $tag) {
                $jobSkill = new RecruitJobSkill;
                $jobSkill->recruit_job_id = $job->id;
                $jobSkill->recruit_skill_id = $tag;
                $jobSkill->save();
            }
        }

        if (!empty($request->stage_id)) {
            foreach ($request->stage_id as $stageID) {
                $interviewStage = new JobInterviewStage;
                $interviewStage->recruit_job_id = $job->id;
                $interviewStage->recruit_interview_stage_id = $stageID;
                $interviewStage->save();
            }
        }

        if (!empty($request->location_id)) {
            foreach ($request->location_id as $locationID) {
                $jobAddress = new RecruitJobAddress;
                $jobAddress->recruit_job_id = $job->id;
                $jobAddress->company_address_id = $locationID;
                $jobAddress->save();
            }
        }

        $job->question()->sync($request->checkQuestionColumn);

        if (request()->add_more == 'true') {
            $html = $this->create();

            return Reply::successWithData(__('recruit::messages.jobAdded'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('recruit::messages.jobAdded'), ['redirectUrl' => route('jobs.index')]);
    }

    public function edit($id)
    {
        $this->pageTitle = __('recruit::modules.job.editJob');
        $this->job = RecruitJob::findOrFail($id);
        $this->noEndDate = $this->job->end_date ? false : true;
        $this->employee_detail = EmployeeDetails::join('users', 'employee_details.user_id', '=', 'users.id')->where('employee_details.id', $this->job->recruiter_id)->select('employee_details.id', 'users.name')->get();
        $this->employees = Recruiter::with('user')->where('status', '=', 'enabled')->get();

        $this->editPermission = user()->permission('edit_job');
        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->job->added_by == user()->id)
            || ($this->editPermission == 'owned' && user()->id == $this->job->recruiter_id)
            || ($this->editPermission == 'both' && user()->id == $this->job->recruiter_id)
            || $this->job->added_by == user()->id));

        $this->locations = CompanyAddress::all();
        $this->workExperience = RecruitWorkExperience::all();
        $this->recruit_skills = RecruitJobSkill::where('recruit_job_id', $id)->get();
        $this->selected_skills = $this->recruit_skills->pluck('recruit_skill_id')->toArray();
        $this->job_locations = RecruitJobAddress::where('recruit_job_id', $id)->get();
        $this->selected_locations = $this->job_locations->pluck('company_address_id')->toArray();
        $this->skills = RecruitSkill::all();
        $this->jobTypes = RecruitJobType::all();
        $this->departments = Team::all();
        $this->stages = RecruitInterviewStage::all();
        $this->jobInterviews = JobInterviewStage::where('recruit_job_id', $id)->get()->pluck('recruit_interview_stage_id')->toArray();
        $this->categories = RecruitJobCategory::all();
        $this->currencies = Currency::all();
        $this->questions = RecruitCustomQuestion::where('status', 'enable')->where('category', 'job_application')->get();
        $this->allQuestions = RecruitJobQuestion::where('recruit_job_id', $id)->get();
        $this->selectedQuestions = $this->allQuestions->pluck('recruit_custom_question_id')->toArray();
        $this->subcategories = !is_null($this->job->recruit_job_sub_category_id) ? RecruitJobSubCategory::where('id', $this->job->recruit_job_sub_category_id)->get() : [];

        if (request()->ajax()) {
            $html = view('recruit::jobs.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::jobs.ajax.edit';

        return view('recruit::jobs.create', $this->data);
    }

    public function update(StoreJobRequest $request, $id)
    {
        $this->editPermission = user()->permission('edit_job');
        $job = RecruitJob::findOrFail($id);
        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $job->added_by == user()->id)
            || ($this->editPermission == 'owned' && user()->id == $job->recruiter_id)
            || ($this->editPermission == 'both' && user()->id == $job->recruiter_id)
            || $job->added_by == user()->id));

        $endDate = !$request->has('without_end_date') ? Carbon::createFromFormat($this->company->date_format, $request->end_date)->format('Y-m-d') : null;

        $job->title = $request->title;
        $job->slug = Str::slug($request->title, '-');
        $job->job_description = $request->job_description == '<p><br></p>' ? null : $request->job_description;
        $job->total_positions = $request->total_positions;
        $job->remaining_openings = $request->total_positions;
        $job->department_id = $request->department_id;
        $job->start_date = Carbon::createFromFormat($this->company->date_format, $request->start_date)->format('Y-m-d');
        $job->end_date = $endDate;
        $job->status = $request->status;
        $job->meta_details = [
            'title' => $request->meta_title ?: $job->title,
            'description' => $request->meta_description ?: strip_tags(Str::substr(html_entity_decode($job->job_description), 0, 150)),
        ];
        $job->recruiter_id = $request->recruiter;
        $job->currency_id = $request->currency_id;
        $job->recruit_job_category_id = $request->category_id;
        $job->recruit_job_sub_category_id = $request->sub_category_id;
        $job->recruit_job_type_id = $request->job_type_id;
        $job->recruit_work_experience_id = $request->work_experience;
        $job->pay_type = $request->paytype;
        $job->start_amount = $request->start_amount;
        $job->end_amount = $request->end_amount;
        $job->pay_according = $request->pay_according;
        $job->disclose_salary = $request->disclose_salary ?: 'no';
        $job->remote_job = $request->remote_job ?: 'no';
        $job->is_photo_require = $request->is_photo_require ?: '0';
        $job->is_resume_require = $request->is_resume_require ?: '0';
        $job->is_dob_require = $request->is_dob_require ?: '0';
        $job->is_gender_require = $request->is_gender_require ?: '0';
        $job->is_currentctc_require = $request->is_currentctc_require ?: '0';
        $job->is_expectedctc_require = $request->is_expectedctc_require ?: '0';


        $job->save();

        $job->address()->sync($request->location_id);
        $job->question()->sync($request->checkQuestionColumn);

        if (!empty($request->skill_id)) {
            RecruitJobSkill::where('recruit_job_id', $id)->delete();

            foreach ($request->skill_id as $tag) {
                $jobSkill = new RecruitJobSkill;
                $jobSkill->recruit_job_id = $job->id;
                $jobSkill->recruit_skill_id = $tag;
                $jobSkill->save();
            }
        }

        if (!empty($request->stage_id)) {
            JobInterviewStage::where('recruit_job_id', $id)->delete();

            foreach ($request->stage_id as $stageID) {
                $interviewStage = new JobInterviewStage;
                $interviewStage->recruit_job_id = $job->id;
                $interviewStage->recruit_interview_stage_id = $stageID;
                $interviewStage->save();
            }
        }

        return Reply::successWithData(__('recruit::messages.jobUpdate'), ['redirectUrl' => route('jobs.index')]);
    }

    public function destroy($id)
    {
        $job = RecruitJob::findOrFail($id);
        $this->deletePermission = user()->permission('delete_job');
        abort_403(!($this->deletePermission == 'all'
            || ($this->deletePermission == 'added' && $job->added_by == user()->id)
            || ($this->deletePermission == 'owned' && user()->id == $job->recruiter_id)
            || ($this->deletePermission == 'both' && user()->id == $job->recruiter_id)
            || $job->added_by == user()->id));
        $job->delete();

        $jobInterviews = JobInterviewStage::where('recruit_job_id', $id)->get();

        foreach ($jobInterviews as $jobInterview) {
            $jobInterview->delete();
        }

        return Reply::successWithData(__('recruit::messages.jobDeleted'), ['redirectUrl' => route('jobs.index')]);
    }

    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        case 'change-status':
            $this->changeStatus($request);

            return Reply::success(__('messages.updateSuccess'));
        default:
            return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_job') != 'all');
        RecruitJob::whereIn('id', explode(',', $request->row_ids))->delete();

        return true;
    }

    protected function changeStatus($request)
    {
        abort_403(user()->permission('edit_job') != 'all');
        RecruitJob::whereIn('id', explode(',', $request->row_ids))->update(['status' => $request->status]);

        return true;
    }

    public function changeJobStatus(Request $request)
    {
        $jobId = $request->jobId;

        $this->job = RecruitJob::findOrFail($jobId);

        $this->editPermission = user()->permission('edit_job');
        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->job->added_by == user()->id)
            || ($this->editPermission == 'owned' && user()->id == $this->job->recruiter_id)
            || ($this->editPermission == 'both' && user()->id == $this->job->recruiter_id)
            || $this->job->added_by == user()->id));
        $status = $request->status;
        $jobStatus = RecruitJob::findOrFail($jobId);
        $jobStatus->status = $status;
        $jobStatus->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function fetchJob(Request $request)
    {
        $application = RecruitJobApplication::with('job')->where('id', $request->application_id)->first();
        $jobId = $application->job->id;
        $data = RecruitJob::with('stages')->findOrFail($jobId);
        $data = $data->stages->pluck('name', 'id')->toArray();

        return Reply::dataOnly(['status' => 'success', 'stages' => $data]);
    }

}
