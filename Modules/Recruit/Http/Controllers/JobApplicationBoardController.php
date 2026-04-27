<?php

namespace Modules\Recruit\Http\Controllers;

use Carbon\Carbon;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\CompanyAddress;
use Illuminate\Support\Str;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitSetting;
use App\Http\Controllers\AccountBaseController;
use App\Models\User;
use Modules\Zoom\Entities\ZoomMeeting;
use Modules\Zoom\Entities\ZoomSetting;
use Zoom;
use Modules\Recruit\Traits\ZoomSettings;
use Illuminate\Support\Facades\DB;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitJobboardSetting;
use Modules\Recruit\Entities\RecruitApplicationSkill;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitApplicationStatusCategory;
use Modules\Recruit\Entities\RecruitInterviewComments;
use Modules\Recruit\Entities\RecruitInterviewEmployees;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Entities\RecruitInterviewStage;
use Modules\Recruit\Entities\RecruitJobOfferLetter;
use Modules\Recruit\Events\CandidateInterviewScheduleEvent;
use Modules\Recruit\Events\HostInterviewEvent;
use Modules\Recruit\Events\InterviewScheduleEvent;
use Modules\Recruit\Events\JobApplicationStatusChangeEvent;
use Modules\Recruit\Events\OfferLetterEvent;
use Modules\Recruit\Http\Requests\ApplicationStatus\StoreApplicationStatus;
use Modules\Recruit\Http\Requests\OfferLetter\StoreJobLetter;
use Modules\Recruit\Http\Requests\StoreRemark;
use Modules\Recruit\Http\Requests\ZoomMeeting\StoreInterview;

class JobApplicationBoardController extends AccountBaseController
{
    use ZoomSettings;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.jobApplication');
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });

    }

    public function index(Request $request)
    {

        $viewPermission = user()->permission('view_job_application');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->startDate = now()->subDays(15)->format($this->company->date_format);
        $this->endDate = now()->addDays(15)->format($this->company->date_format);
        $this->locations = CompanyAddress::all();
        $this->jobs = RecruitJob::where('company_id', '=', company()->id)->get();
        $this->taskLabels = RecruitApplicationStatus::where('company_id', company()->id)->get();
        $this->currentLocations = RecruitJobApplication::select('current_location')->where('current_location', '!=', null)->distinct()->get();

        if (request()->ajax()) {
            $startDate = ($request->startDate != 'null' && $request->startDate != 'undefined') ? Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString() : null;
            $endDate = ($request->endDate != 'null' && $request->endDate != 'undefined') ? Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString() : null;

            $this->boardEdit = (request()->has('boardEdit') && request('boardEdit') == 'false') ? false : true;
            $this->boardDelete = (request()->has('boardDelete') && request('boardDelete') == 'false') ? false : true;
            $this->rejectColumn = RecruitApplicationStatus::join('recruit_application_status_categories', 'recruit_application_status_categories.id', 'recruit_application_status.recruit_application_status_category_id')->where('recruit_application_status_categories.name', 'rejected')->select('recruit_application_status.*')->first();

            $boardColumns = RecruitApplicationStatus::select('recruit_application_status.*')
                ->withCount(['applications as applications_count' => function ($q) use ($startDate, $endDate, $request) {
                    $q->leftJoin('recruit_jobs', 'recruit_jobs.id', '=', 'recruit_job_applications.recruit_job_id')
                        ->select(DB::raw('count(distinct recruit_job_applications.id)'));

                    if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '' && $request->startDate != 'undefined') {
                        $q = $q->whereDate('recruit_job_applications.created_at', '>=', $startDate);
                    }

                    if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '' && $request->endDate != 'undefined') {
                        $q = $q->where('recruit_job_applications.created_at', '<=', $endDate);
                    }

                    if ($request->status != 'all' && $request->status != '' && $request->status != 'null') {
                        $q = $q->where('recruit_job_applications.recruit_application_status_id', $request->status);
                    }

                    if ($request->jobID != 'all' && $request->jobID != '' && $request->jobID != 'null') {
                        $q = $q->where('recruit_job_applications.recruit_job_id', $request->jobID);
                    }

                    if ($request->location != 'all' && $request->location != '' && $request->location != 'null') {
                        $q = $q->where('recruit_job_applications.location_id', $request->location);
                    }

                    if ($request->gender != null && $request->gender != 'all') {
                        $q = $q->where('recruit_job_applications.gender', '=', $request->gender);
                    }

                    if ($request->total_experience != null && $request->total_experience != 'all') {
                        $q = $q->where('recruit_job_applications.total_experience', '=', $request->total_experience);
                    }

                    if ($request->current_location != null && $request->current_location != 'all') {
                        $q = $q->where('recruit_job_applications.current_location', '=', $request->current_location);
                    }

                    if ($request->current_ctc_min != null && $request->current_ctc_min != '') {
                        $q = $q->where('recruit_job_applications.current_ctc', '>=', $request->current_ctc_min);
                    }

                    if ($request->current_ctc_max != null && $request->current_ctc_max != '') {
                        $q = $q->where('recruit_job_applications.current_ctc', '<=', $request->current_ctc_max);
                    }

                    if ($request->expected_ctc_min != null && $request->expected_ctc_min != '') {
                        $q = $q->where('recruit_job_applications.expected_ctc', '>=', $request->expected_ctc_min);
                    }

                    if ($request->expected_ctc_max != null && $request->expected_ctc_max != '') {
                        $q = $q->where('recruit_job_applications.expected_ctc', '<=', $request->expected_ctc_max);
                    }

                    if ($request->searchText != '') {
                        $q->where(function ($query) {
                            $query->where('recruit_job_applications.full_name', 'like', '%' . request('searchText') . '%')
                                ->orWhere('recruit_jobs.title', 'like', '%' . request('searchText') . '%');
                        });
                    }
                }])
                ->with(['applications' => function ($q) use ($startDate, $endDate, $request) {
                    $q->leftJoin('recruit_jobs', 'recruit_jobs.id', '=', 'recruit_job_applications.recruit_job_id')
                        ->select('recruit_job_applications.*');

                    if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '' && $request->startDate != 'undefined') {
                        $q = $q->whereDate('recruit_job_applications.created_at', '>=', $startDate);
                    }

                    if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '' && $request->endDate != 'undefined') {
                        $q = $q->where('recruit_job_applications.created_at', '<=', $endDate);
                    }

                    if ($request->status != 'all' && $request->status != '' && $request->status != 'null') {
                        $q = $q->where('recruit_job_applications.recruit_application_status_id', $request->status);
                    }

                    if ($request->jobID != 'all' && $request->jobID != '' && $request->jobID != 'null') {
                        $q = $q->where('recruit_job_applications.recruit_job_id', $request->jobID);
                    }

                    if ($request->location != 'all' && $request->location != '' && $request->location != 'null') {
                        $q = $q->where('recruit_job_applications.location_id', $request->location);
                    }

                    if ($request->gender != null && $request->gender != 'all') {
                        $q = $q->where('recruit_job_applications.gender', '=', $request->gender);
                    }

                    if ($request->total_experience != null && $request->total_experience != 'all') {
                        $q = $q->where('recruit_job_applications.total_experience', '=', $request->total_experience);
                    }

                    if ($request->current_location != null && $request->current_location != 'all') {
                        $q = $q->where('recruit_job_applications.current_location', '=', $request->current_location);
                    }

                    if ($request->current_ctc_min != null && $request->current_ctc_min != '') {
                        $q = $q->where('recruit_job_applications.current_ctc', '>=', $request->current_ctc_min);
                    }

                    if ($request->current_ctc_max != null && $request->current_ctc_max != '') {
                        $q = $q->where('recruit_job_applications.current_ctc', '<=', $request->current_ctc_max);
                    }

                    if ($request->expected_ctc_min != null && $request->expected_ctc_min != '') {
                        $q = $q->where('recruit_job_applications.expected_ctc', '>=', $request->expected_ctc_min);
                    }

                    if ($request->expected_ctc_max != null && $request->expected_ctc_max != '') {
                        $q = $q->where('recruit_job_applications.expected_ctc', '<=', $request->expected_ctc_max);
                    }

                    if ($request->searchText != '') {
                        $q->where(function ($query) {
                            $query->where('recruit_job_applications.full_name', 'like', '%' . request('searchText') . '%')
                                ->orWhere('recruit_jobs.title', 'like', '%' . request('searchText') . '%');
                        });
                    }
                },

                    'applications.job', 'applications.applicationStatus', 'userSetting'])
                ->orderBy('position', 'asc');

            $boardColumns = $boardColumns->get()->map(function ($query) {
                $query->setRelation('applications', $query->applications->take($this->taskBoardColumnLength));
                return $query;
            });

            $result = array();

            foreach ($boardColumns as $key => $boardColumn) {
                $result['boardColumns'][] = $boardColumn;
                $result['boardColumns'][$key]['applications'] = $boardColumn->applications;
            }

            $this->result = $result;

            $this->startDate = $startDate;
            $this->endDate = $endDate;
            $view = view('recruit::job-applications.board', $this->data)->render();

            return Reply::dataOnly(['view' => $view]);
        }

        session()->forget('pusher_settings');

        return view('recruit::job-applications.index', $this->data);
    }

    public function loadMore(Request $request)
    {
        $startDate = ($request->startDate != 'null' && $request->startDate != 'undefined') ? Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString() : null;
        $endDate = ($request->endDate != 'null' && $request->endDate != 'undefined') ? Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString() : null;
        $skip = $request->currentTotalTasks;
        $totalTasks = $request->totalTasks;

        $tasks = RecruitJobApplication::with(['job', 'applicationStatus'])
            ->leftJoin('recruit_jobs', 'recruit_jobs.id', '=', 'recruit_job_applications.recruit_job_id')
            ->where('recruit_job_applications.recruit_application_status_id', $request->columnId)
            ->orderBy('recruit_job_applications.column_priority', 'asc');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '' && $request->startDate != 'undefined') {
            $tasks = $tasks->whereDate('recruit_job_applications.created_at', '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '' && $request->endDate != 'undefined') {
            $tasks = $tasks->where('recruit_job_applications.created_at', '<=', $endDate);
        }

        if ($request->status != 'all' && $request->status != '' && $request->status != 'null') {
            $tasks = $tasks->where('recruit_job_applications.recruit_application_status_id', $request->status);
        }

        if ($request->jobID != 'all' && $request->jobID != '' && $request->jobID != 'null') {
            $tasks = $tasks->where('recruit_job_applications.recruit_job_id', $request->jobID);
        }

        if ($request->location != 'all' && $request->location != '' && $request->location != 'null') {
            $tasks = $tasks->where('recruit_job_applications.location_id', $request->location);
        }

        if ($request->searchText != '') {
            $tasks = $tasks->where(function ($query) {
                $query->where('recruit_job_applications.full_name', 'like', '%' . request('searchText') . '%')
                    ->orWhere('recruit_jobs.title', 'like', '%' . request('searchText') . '%');
            });
        }

        $tasks = $tasks->select('recruit_job_applications.*');
        $tasks->skip($skip)->take($this->taskBoardColumnLength);
        $tasks = $tasks->get();

        $this->tasks = $tasks;

        if ($totalTasks <= ($skip + $this->taskBoardColumnLength)) {
            $loadStatus = 'hide';
        }
        else {
            $loadStatus = 'show';
        }

        $view = view('recruit::job-applications.load_more', $this->data)->render();

        return Reply::dataOnly(['view' => $view, 'load_more' => $loadStatus]);
    }

    public function create()
    {

        abort_403(user()->permission('add_application_status') !== 'all');

        $this->statuses = RecruitApplicationStatus::select('id', 'status', 'position')->orderBy('position', 'asc')->get();
        $this->categories = RecruitApplicationStatusCategory::where('company_id', '=', company()->id)->with('status')->get();
        $firstPosition = $this->statuses->first();
        $this->firstStatus = $this->statuses->filter(function ($stat) use ($firstPosition) {
            if ($stat->position == 0) {
                return $stat->position == 0;
            }
            elseif ($stat->position == 1) {
                return $stat->position == 1;
            }
            else {
                return $stat->position == $firstPosition->position;
            }
        })->first();

        return view('recruit::job-applications.status.create_status', $this->data);
    }

    public function edit($id)
    {
        abort_403(user()->permission('edit_application_status') !== 'all');

        $status = RecruitApplicationStatus::with('category')->select('id', 'status', 'color', 'position', 'recruit_application_status_category_id', 'action')->where('id', $id)->first();
        $categories = RecruitApplicationStatusCategory::where('company_id', '=', company()->id)->with('status')->get();

        $allStatuses = RecruitApplicationStatus::select('id', 'status', 'position')->orderBy('position')->get();
        $statuses = $allStatuses->filter(function ($stat) use ($status) {
            return $stat->position !== $status->position && $stat->position !== $status->position - 1;
        });


        $statuses = $statuses->filter(function ($stat) use ($status) {
            return $stat->position !== $status->position;
        });

        $firstStatus = $allStatuses->filter(function ($stat) {
            if ($stat->position === 0) {
                return $stat->position === 0;
            }
            else {
                return $stat->position === 1;
            }
        })->first();

        return view('recruit::job-applications.status.edit_status', compact('status', 'statuses', 'firstStatus', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $status = RecruitApplicationStatus::select('id', 'status', 'color', 'position', 'recruit_application_status_category_id', 'action')->where('id', $id)->first();

        if ($request->position !== 'no_change') {
            if ($request->position === 'before_first') {
                $statuses = RecruitApplicationStatus::select('id', 'position')->where('position', '<', $status->position)->get();

                foreach ($statuses as $stat) {
                    $stat->position = $stat->position + 1;

                    $stat->save();
                }

                $status->position = 1;
            }
            else {
                if ($status->position - $request->position > 1) {
                    $statuses = RecruitApplicationStatus::select('id', 'position')->where('position', '>', $request->position)->where('position', '<=', $status->position)->get();

                    foreach ($statuses as $stat) {
                        $stat->position = $stat->position + 1;

                        $stat->save();
                    }

                    $status->position = $request->position + 1;
                }

                if ($request->position - $status->position > 0) {
                    $statuses = RecruitApplicationStatus::select('id', 'position')->where('position', '<=', $request->position)->where('position', '>=', $status->position)->get();

                    foreach ($statuses as $stat) {
                        $stat->position = $stat->position - 1;

                        $stat->save();
                    }

                    $status->position = $request->position;
                }
            }
        }

        $status->recruit_application_status_category_id = $request->category_id;
        $status->action = $request->action ? 'yes' : 'no';
        $status->status = $request->status;
        $status->color = $request->color;
        $status->save();

        $recruitSetting = RecruitSetting::first();
        $mail = $recruitSetting->mail_setting;

        foreach ($mail as $key => $value) {
            if ($value['id'] == $id) {
                $value['name'] = $request->status;
                $mail[$key] = $value;
            }
        }

        $recruitSetting->mail_setting = $mail;
        $recruitSetting->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function addSkill(Request $request)
    {
        if (!empty($request->skill_id)) {
            RecruitApplicationSkill::where('recruit_job_application_id', $request->application_id)->delete();

            foreach ($request->skill_id as $tag) {
                $jobSkill = new RecruitApplicationSkill();
                $jobSkill->recruit_job_application_id = $request->application_id;
                $jobSkill->recruit_skill_id = $tag;
                $jobSkill->save();
            }
        }

        return Reply::success(__('recruit::modules.message.skillAdded'));
    }

    public function collapseColumn(Request $request)
    {
        $setting = RecruitJobboardSetting::firstOrNew([
            'user_id' => user()->id,
            'recruit_application_status_id' => $request->boardColumnId,
        ]);
        $setting->collapsed = (($request->type == 'minimize') ? 1 : 0);
        $setting->save();

        return Reply::dataOnly(['status' => 'success']);
    }

    public function storeStatus(StoreApplicationStatus $request)
    {
        $statuses = RecruitApplicationStatus::select('id', 'position')->where('position', '>', $request->position)->get();

        foreach ($statuses as $status) {
            $status->position = $status->position + 1;
            $status->save();
        }

        $status = new RecruitApplicationStatus();
        $status->recruit_application_status_category_id = $request->category_id;
        $status->status = $request->status;
        $status->slug = str_slug($request->status, '_');
        $status->color = $request->color;
        $status->position = $request->position + 1;
        $status->action = $request->action ? 'yes' : 'no';
        $status->save();

        $setting = RecruitSetting::first();
        $mail = $setting->mail_setting;
        $mail[$status->id] = ['id' => $status->id, 'name' => $status->status, 'status' => true];
        $setting->mail_setting = $mail;
        $setting->save();

        return Reply::success(__('messages.recordSaved'));
    }

    public function updateIndex(Request $request)
    {
        $applicationIds = $request->applicationIds;
        $boardColumnId = $request->boardColumnId;
        $movingAppId = $request->movingAppId;
        $priorities = $request->prioritys;

        $board = RecruitApplicationStatus::with('category')->findOrFail($boardColumnId);

        if ($request->has('applicationIds')) {
            foreach ($applicationIds as $key => $taskId) {
                if (!is_null($taskId)) {
                    $task = RecruitJobApplication::findOrFail($taskId);
                    $task->column_priority = $priorities[$key];
                    $task->recruit_application_status_id = $boardColumnId;

                    if ($movingAppId == $taskId) {
                        $movingApplication = $task;
                        $task->save();

                    } else {
                        $task->saveQuietly();
                    }
                }
            }
        }

        $send = $this->statusForMailSend($boardColumnId);

        if ($send && isset($movingApplication)) {
            event(new JobApplicationStatusChangeEvent($movingApplication));
        }

        $appId = $applicationIds;

        return Reply::dataOnly(['status' => 'success', 'board' => $board, 'appId' => $appId]);
    }

    public function statusForMailSend($id)
    {
        $settings = RecruitSetting::first();
        $mail = $settings->mail_setting;

        foreach ($mail as $mailDetails) {
            if ($mailDetails['id'] == $id && $mailDetails['status'] == true) {
                return true;
            }
        }
    }

    public function destroy($id)
    {
        $this->deletePermission = user()->permission('delete_application_status');
        abort_403(!($this->deletePermission == 'all'));
        $status = RecruitApplicationStatus::findOrFail($id);
        $statuses = RecruitApplicationStatus::select('id', 'position')->where('position', '>', $status->position)->get();

        foreach ($statuses as $stat) {
            $stat->position = $stat->position - 1;

            $stat->save();
        }

        $status->delete();

        $allStatuses = RecruitApplicationStatus::pluck('status', 'id')->toArray();

        $recruitSetting = RecruitSetting::first();
        $mail = [];
        $arr = [];

        foreach ($allStatuses as $key => $value) {
            $arr['id'] = $key;
            $arr['name'] = $value;
            $arr['status'] = true;
            $mail[$key] = $arr;
        }

        $recruitSetting->mail_setting = $mail;
        $recruitSetting->save();


        return Reply::successWithData(__('recruit::modules.message.deleteSuccess'), ['redirectUrl' => route('job-applications.index')]);
    }

    public function applicationRemark($id, $board = null)
    {
        $this->board = $board;
        $this->applicationID = $id;
        $this->categories = RecruitApplicationStatus::select('recruit_application_status_category_id')->get();

        return view('recruit::job-applications.add_remark', $this->data);
    }

    public function applicationRemarkStore(StoreRemark $request)
    {
        $remark = RecruitJobApplication::where('id', $request->applicationID)->first();
        $remark->remark = $request->remark;
        $remark->save();

        return Reply::successWithData(__('recruit::messages.remarkAdded'), ['board' => (int)$request->board]);
    }

    public function interview($id, $board = null)
    {
        $this->addPermission = user()->permission('add_interview_schedule');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $this->applicationID = $id;
        $this->board = $board;
        $this->applications = RecruitJobApplication::where('company_id', '=', company()->id)->get();
        $this->employees = User::allEmployees();
        $this->stages = RecruitInterviewStage::all();

        return view('recruit::job-applications.create_interview', $this->data);
    }

    public function interviewStore(StoreInterview $request)
    {
        $this->addPermission = user()->permission('add_interview_schedule');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $interview = new RecruitInterviewSchedule();

        $date = Carbon::createFromFormat(company()->date_format, $request->start_date)->format('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d ' . $this->company->time_format, $date . ' ' . $request->start_time, $this->company->timezone)->setTimezone('UTC');

        if (isset($request->video_type) && $request->video_type == 'zoom') {
            $this->setZoomConfigs();

            $data = $request->all();
            $meeting = new ZoomMeeting();
            $data['meeting_name'] = $request->meeting_title;

            $end = Carbon::createFromFormat(company()->date_format.' '.company()->time_format, $request->end_date . ' ' . $request->end_time)->setTimezone('UTC');
            $data['start_date_time'] = $start->toDateTimeString();
            $data['end_date_time'] = $end->toDateTimeString();
            $meeting = $meeting->create($data);
            $host = User::findOrFail($request->created_by);
            $user = Zoom::user()->find('me');
            $meetings = $this->createMeeting($user, $meeting, null, $host);
        }
        else {
            $meetings = '';
        }

        // Store Schedule
        $interview->recruit_interview_stage_id = $request->jobStage;
        $interview->recruit_job_application_id = $request->applicationID;
        $interview->interview_type = $request->interview_type;
        $interview->video_type = ($request->has('video_type')) ? $request->video_type : 'other';
        $interview->meeting_id = ($meetings != '') ? $meetings->id : null;
        $interview->schedule_date = Carbon::createFromFormat('Y-m-d ' . $this->company->time_format, $date . ' ' . $request->start_time, $this->company->timezone)->setTimezone('UTC');
        $interview->phone = $request->phone;
        $interview->other_link = $request->other_link;
        $interview->send_reminder_all = $request->send_reminder_all ? $request->send_reminder_all : '0';
        $interview->remind_time_all = $request->remind_time_all;
        $interview->remind_type_all = $request->remind_type_all;
        $interview->added_by = user()->id;
        $interview->last_updated_by = user()->id;
        $interview->notify_c = ($request->has('notify_c')) ? $request->notify_c : '0';
        $interview->save();

        $employees = $request->employee_id;

        if (!is_null($employees)) {
            if (!empty($request->employee_id)) {
                foreach ($employees as $employee) {
                    $interviewEmployee = new RecruitInterviewEmployees();
                    $interviewEmployee->recruit_interview_schedule_id = $interview->id;
                    $interviewEmployee->user_id = $employee;
                    $interviewEmployee->save();
                }

                $employeeInfo = User::whereIn('id', $employees)->get();

                if ($employeeInfo) {
                    // Mail to employee for inform interview schedule
                    event(new InterviewScheduleEvent($interview, $employeeInfo));
                }
            }
        }

        if ($interview->save()) {
            $interview->employees()->sync($request->employee_id);
        }

        if ($request->comment || $request->candidate_comment) {
            $scheduleComment = [
                'interview_schedule_id' => $interview->id,
                'user_id' => $this->user->id,
                'comment' => $request->comment ?? null,
                'candidate_comment' => $request->candidate_comment ?? null
            ];

            $interview->comments()->create($scheduleComment);
        }

        $candidateComment = RecruitInterviewComments::where('recruit_interview_schedule_id', $interview->id)->where('candidate_comment', '!=', 'null')->first() ?? null;

        if ($request->notify_c == '1' && $interview->jobApplication->email != null) {
            event(new CandidateInterviewScheduleEvent($interview, $interview->jobApplication, $candidateComment));
        }

        if ($interview->meeting_id != '') {
            // mail to meeting Host
            event(new HostInterviewEvent($interview));
        }

        return Reply::successWithData(__('recruit::messages.interviewScheduleAdded'), ['board' => (int)$request->board]);
    }

    public function createMeeting($user, ZoomMeeting $meeting, $id, $host = null)
    {
        $this->setZoomConfigs();
        // create meeting using zoom API
        $commonSettings = [
            'type' => 2,
            'topic' => $meeting->meeting_name,
            'start_time' => $meeting->start_date_time,
            'duration' => $meeting->end_date_time->diffInMinutes($meeting->start_date_time),
            'timezone' => company()->timezone,
            'agenda' => $meeting->description,
            'alternative_host' => [],
            'settings' => [
                'host_video' => $meeting->host_video == 1,
                'participant_video' => $meeting->participant_video == 1,
            ]
        ];

        if ($host) {
            $commonSettings['alternative_host'] = [$host->email];
        }

        if (is_null($id)) {
            $zoomMeeting = $user->meetings()->make($commonSettings);
            $savedMeeting = $user->meetings()->save($zoomMeeting);

            $meeting->meeting_id = strval($savedMeeting->id);
            $meeting->start_link = $savedMeeting->start_url;
            $meeting->join_link = $savedMeeting->join_url;
            $meeting->password = $savedMeeting->password;

            $meeting->save();
        }
        else {
            $user->meetings()->find($meeting->meeting_id)->update($commonSettings);
        }

        return $meeting;
    }

    public function offerLetter($id, $board = null)
    {
        $addPermission = user()->permission('add_offer_letter');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->applicationId = $id;
        $this->board = $board;
        $this->jobs = RecruitJob::where('company_id', '=', company()->id)->get();
        $this->applications = RecruitJobApplication::where('company_id', '=', company()->id)->get();
        $this->jobApplications = RecruitJobApplication::with('job')->find($id);
        $this->jobOffer = RecruitJob::where('id', $this->jobApplications->recruit_job_id)->first();
        $this->jobId = $this->jobApplications->job->id;

        return view('recruit::job-applications.create_offer_letter', $this->data);
    }

    public function offerLetterStore(StoreJobLetter $request)
    {
        $addPermission = user()->permission('add_offer_letter');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $jobOffer = new RecruitJobOfferLetter();

        $jobOffer->recruit_job_application_id = $request->jobApplicant;
        $jobOffer->recruit_job_id = $request->jobId;

        $jobOffer->job_expire = Carbon::createFromFormat($this->company->date_format, $request->jobExpireDate)->format('Y-m-d');
        $jobOffer->expected_joining_date = Carbon::createFromFormat($this->company->date_format, $request->expJoinDate)->format('Y-m-d');

        $jobOffer->comp_amount = $request->comp_amount;
        $jobOffer->pay_according = $request->pay_according;
        $jobOffer->sign_require = $request->signature;
        $jobOffer->hash = md5(microtime());

        if ($request->sendEmail == 'on' || $request->sendEmail == 'off') {
            $jobOffer->status = 'pending';
        }
        else {
            $jobOffer->status = 'draft';
        }

        $jobOffer->save();

        // Send offer Letter mail if selected save and send

        if ($request->sendEmail == 'on') {
            event(new OfferLetterEvent($jobOffer, 'send'));
        }

        return Reply::successWithData(__('recruit::messages.offerAdded'), ['board' => (int)$request->board]);
    }

    public function rejectedRemark($id, $board = null)
    {
        $this->applicationID = $id;
        $this->board = $board;
        $this->categories = RecruitApplicationStatus::select('recruit_application_status_category_id')->get();

        return view('recruit::job-applications.add_rejection_remark', $this->data);
    }

    public function rejectedRemarkStore(StoreRemark $request)
    {
        $remark = RecruitJobApplication::where('id', $request->applicationID)->first();
        $remark->rejection_remark = $request->remark;
        $remark->saveQuietly();

        return Reply::successWithData(__('recruit::messages.remarkAdded'), ['board' => (int)$request->board]);
    }

    public function fetchStatusModel(Request $request)
    {
        $categories = RecruitApplicationStatusCategory::where('company_id', '=', company()->id)->findOrFail($request->category_id);


        if($categories->name == 'applied' || $categories->name == 'others'){
            $data = '';
        }
        else{
            $data = '<div class="form-check"><input class="form-check-input" type="checkbox" name="action" id="action">
                <label
                    class="form-check-label form_custom_label text-dark-grey pl-2 mr-3 justify-content-start cursor-pointer checkmark-20 pt-1 text-wrap"
                    for="action">';

            if($categories->name == 'shortlist'){
                $data .= __('recruit::messages.shortlistLabel');
            }
            elseif($categories->name == 'interview'){
                $data .= __('recruit::messages.interviewLabel');
            }
            elseif($categories->name == 'hired'){
                $data .= __('recruit::messages.hiredLabel');
            }
            elseif($categories->name == 'rejected'){
                $data .= __('recruit::messages.rejectLabel');
            }
            else{
                $data .= __('recruit::modules.jobApplication.action');
            }

            $data .= '</label></div>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $data]);
    }

}
