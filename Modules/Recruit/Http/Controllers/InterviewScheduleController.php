<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\AccountBaseController;
use App\Models\User;
use Carbon\Carbon;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Traits\ZoomSettings;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\DataTables\InterviewScheduleDataTable;
use Modules\Recruit\Entities\JobInterviewStage;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\Recruiter;
use Modules\Recruit\Entities\RecruitInterviewComments;
use Modules\Recruit\Entities\RecruitInterviewEmployees;
use Modules\Recruit\Entities\RecruitInterviewEvaluation;
use Modules\Recruit\Entities\RecruitInterviewHistory;
use Modules\Recruit\Entities\RecruitInterviewStage;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Events\CandidateInterviewRescheduleEvent;
use Modules\Recruit\Events\CandidateInterviewScheduleEvent;
use Modules\Recruit\Events\HostInterviewEvent;
use Modules\Recruit\Events\InterviewRescheduleEvent;
use Modules\Recruit\Events\InterviewScheduleEvent;
use Modules\Recruit\Events\UpdateInterviewScheduleEvent;
use Modules\Recruit\Http\Requests\ZoomMeeting\StoreMeeting;
use Modules\Zoom\Entities\ZoomMeeting;
use Modules\Zoom\Entities\ZoomSetting;
use Zoom;
use Modules\Recruit\Http\Requests\ZoomMeeting\UpdateMeeting;
use Modules\Recruit\Notifications\EmployeeResponse;
use Notification;

class InterviewScheduleController extends AccountBaseController
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    use ZoomSettings;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.interviewSchedule');
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function tableView(InterviewScheduleDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_interview_schedule');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));
        $this->employees = User::allEmployees();

        if ($viewPermission == 'all' || $viewPermission == 'added' || $viewPermission == 'owned' || $viewPermission == 'both') {
            return $dataTable->render('recruit::interview-schedule.table', $this->data);
        }

        abort(403, __('messages.permissionDenied'));
    }

    public function index(Request $request)
    {
        $viewPermission = user()->permission('view_interview_schedule');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));
        $this->loggedEmployee = user();
        $currentDate = now()->format('Y-m-d');
        $this->employees = User::allEmployees();
        $this->candidates = User::allClients();
        $this->recruiter = Recruiter::with('user')->get();

        $this->employeeStatus = RecruitInterviewEmployees::select('recruit_interview_employees.user_accept_status', 'recruiters.user_id')
            ->join('recruiters', 'recruiters.id', 'recruit_interview_employees.user_id')
            ->join('users', 'users.id', 'recruiters.user_id')
            ->where('recruiters.user_id', user()->id)->get();



        $model = RecruitInterviewSchedule::select('recruit_interview_schedules.id', 'recruit_interview_schedules.added_by', 'recruit_interview_schedules.recruit_job_application_id', 'recruit_interview_schedules.schedule_date', 'recruit_interview_schedules.status', 'recruit_interview_schedules.parent_id')
            ->with(['employees', 'employeesData', 'employeesData.user', 'jobApplication:id,recruit_job_id,full_name', 'jobApplication.job:id,title'])
            ->join('recruit_job_applications', 'recruit_job_applications.id', 'recruit_interview_schedules.recruit_job_application_id')
            ->leftjoin('recruit_interview_employees', 'recruit_interview_employees.recruit_interview_schedule_id', 'recruit_interview_schedules.id')
            ->where('status', 'pending')
            ->whereNull('recruit_job_applications.deleted_at')
            ->orderBy('schedule_date')
            ->groupBy('recruit_interview_schedules.id');

        if (request()->has('employee') && $request->employee != 0) {
            $model->whereHas('attendees', function ($query) use ($request) {
                return $query->where('user_id', $request->employee);
            });
        }

        if ($request->searchText != '') {
            $model = $model->where('recruit_job_applications.full_name', 'like', '%' . request('searchText') . '%');
        }

        if ($viewPermission == 'added') {
            $model->where(function ($query) {
                return $query->where('recruit_interview_schedules.added_by', user()->id);
            });
        }

        if ($viewPermission == 'owned') {
            $model->where(function ($query) {
                return $query->where('recruit_interview_employees.user_id', user()->id);
            });
        }

        if ($viewPermission == 'both') {
            $model->where(function ($query) {
                $query->orWhere('recruit_interview_schedules.added_by', '=', user()->id);
                $query->orWhere('recruit_interview_employees.user_id', '=', user()->id);
            });
        }

        if (request()->has('status') && $request->status != 'all') {
            if ($request->status == 'not finished') {
                $model->where('status', '<>', 'hired');
            }
            else {
                $model->where('status', $request->status);
            }
        }

        $scheduleData = $this->events = $model->get();

        $upComingSchedules = $this->events->filter(function ($value) use ($currentDate) {
            return $value->schedule_date >= $currentDate;
        });
        $this->upComingSchedules = $upComingSchedules;

        if (module_enabled('Zoom')) {
            $model->with('meeting');
        }

        if (request('start') && request('end')) {
            $eventData = array();

            foreach ($this->events as $key => $event) {
                $eventData[] = [
                    'id' => $event->id,
                    'title' => $event->jobApplication->full_name,
                    'start' => $event->schedule_date,
                    'end' => $event->schedule_date,
                    'extendedProps' => ['bg_color' => '#3788d8', 'color' => '#fff']
                ];
            }

            return $eventData;
        }

        if ($request->ajax()) {
            $html = view('recruit::interview-schedule.ajax.activity-detail', $this->data)->render();
            return response()->json([ 'html' => $html]);

        } else {
            return view('recruit::interview-schedule.index', $this->data);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->addPermission = user()->permission('add_interview_schedule');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('recruit::modules.interviewSchedule.addInterviewSchedule');
        $this->jobId = request()->id;
        $this->jobCandidates = RecruitJobApplication::where('recruit_job_id', $this->jobId)->get();
        $this->employees = User::allEmployees(active:true);
        $this->candidates = RecruitJobApplication::all();
        $this->stages = RecruitInterviewStage::all();
        $this->selectedStages = JobInterviewStage::with('stage')->where('recruit_job_id', $this->jobId)->get();
        $this->jobs = RecruitJob::all();

        if (request()->ajax()) {
            $html = view('recruit::interview-schedule.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::interview-schedule.ajax.create';

        return view('recruit::interview-schedule.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return array
     */
    public function store(StoreMeeting $request)
    {
        $this->addPermission = user()->permission('add_interview_schedule');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $storeInterview = RecruitInterviewSchedule::where('recruit_job_application_id', $request->candidate_id)->first();

        $date = Carbon::createFromFormat(company()->date_format, $request->start_date)->format('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d ' . $this->company->time_format, $date . ' ' . $request->start_time, $this->company->timezone)->setTimezone('UTC');

        $existInterviewSchedule = RecruitInterviewSchedule::where('recruit_job_application_id', $request->candidate_id)->where('schedule_date', $start)->first();

        if (!$existInterviewSchedule) {
            $interview = new RecruitInterviewSchedule();
        } else {
            return Reply::error(__('recruit::messages.interviewScheduleExist'));
        }

        if (isset($request->video_type) && $request->video_type == 'zoom') {
            $this->setZoomConfigs();

            $data = $request->all();
            $meeting = new ZoomMeeting();
            $data['meeting_name'] = $request->meeting_title;

            $end = Carbon::createFromFormat(company()->date_format.' '.company()->time_format, $request->end_date . ' ' . $request->end_time)->setTimezone('UTC');

            $data['start_date_time'] = $start->toDateTimeString();
            $data['end_date_time'] = $end->toDateTimeString();

            $meeting = $meeting->create($data);

            $host = User::findOrFail($data['created_by']);
            $user = Zoom::user()->find('me');
            $meetings = $this->createMeeting($user, $meeting, null, $host);
        }
        else {
            $meetings = '';
        }

        // Store Schedule
        $interview->recruit_job_application_id = $request->candidate_id;

        if ($storeInterview) {
            if ($request->candidate_id == $storeInterview->recruit_job_application_id) {
                $interview->parent_id = $storeInterview->id;
            }
        }

        $interview->recruit_interview_stage_id = $request->jobStage;
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

        // Update Schedule Status
        $jobApplication = $interview->jobApplication;
        $status = RecruitApplicationStatus::where('slug', 'interview')->first();
        $jobApplication->recruit_application_status_id = $status->id;
        $jobApplication->save();
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

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('interview-schedule.table_view');
        }

        if (request()->add_more == 'true') {
            $html = $this->create();

            return Reply::successWithData(__('recruit::messages.interviewScheduleAdded'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('recruit::messages.interviewScheduleAdded'), ['redirectUrl' => $redirectUrl]);
    }

    // phpcs:ignore
    public function createMeeting($user, ZoomMeeting $meeting, $id,$meetingId = null, $host = null)
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

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $this->viewPermission = user()->permission('view_interview_schedule');
        $this->loggedEmployee = user();

        $this->interview = RecruitInterviewSchedule::with('employeesData.user.employeeDetail.designation:id,name')->find($id);

        $this->recruit_employees = RecruitInterviewEmployees::where('recruit_interview_schedule_id', $id)->get();
        $this->selected_employees = $this->recruit_employees->pluck('user_id')->toArray();

        $parentId = $this->interview->parent_id;

        if ($parentId != null) {
            $this->interview = RecruitInterviewSchedule::with('stage')->where('id', $parentId)->first();
        }

        if ($parentId == null) {
            $this->childInterviews = RecruitInterviewSchedule::with('stage', 'employeesData.user.session', 'employeesData.user.employeeDetail.designation:id,name')->where('parent_id', $id)->get();
        }
        else {
            $this->childInterviews = RecruitInterviewSchedule::with('stage', 'employeesData.user.employeeDetail.designation:id,name')->where('parent_id', $parentId)->get();
        }

        abort_403(!(
            $this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && $this->interview->added_by == user()->id)
            || ($this->viewPermission == 'owned' && in_array(user()->id, $this->selected_employees))
            || ($this->viewPermission == 'both' && (in_array(user()->id, $this->selected_employees) || $this->interview->added_by == user()->id))
        ));
        $this->pageTitle = __('recruit::modules.interviewSchedule.interviewDetails');
        $this->comments = RecruitInterviewComments::where('recruit_interview_schedule_id', $id)->first();

        if (in_array('Zoom', worksuite_plugins()) && ($this->interview->video_type == 'zoom')) {
            $this->meeting = ZoomMeeting::with('attendees', 'host')->findOrFail($this->interview->meeting_id);
            $attendeesIds = $this->meeting->attendees->pluck('id')->toArray();
            $this->zoom_setting = ZoomSetting::first();
        }

        $tab = request('view');

        $this->activeTab = $tab ?: 'details';

        switch ($tab) {
        case 'file':
            $this->tab = 'recruit::interview-schedule.ajax.file';
            break;
        case 'history':
            $this->activity = RecruitInterviewHistory::where('recruit_interview_schedule_id', '=', $id)->orderByDesc('updated_at')->get();
            $this->tab = 'recruit::interview-schedule.ajax.history';
            break;
        case 'evaluations':
            $this->evaluations = RecruitInterviewEvaluation::where('recruit_job_application_id', '=', $this->interview->jobApplication->id)
                ->with(['status', 'user', 'interview.jobApplication', 'stage'])
                ->get();
            $this->tab = 'recruit::interview-schedule.ajax.evaluation';
            break;
        default:
            $this->tab = 'recruit::interview-schedule.ajax.file';
            break;
        }

        $this->view = 'recruit::interview-schedule.ajax.details';

        if (request()->ajax()) {
            $view = request('json') ? $this->tab : $this->view;

            return $this->returnAjax($view);
        }

        return view('recruit::interview-schedule.ajax.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->editPermission = user()->permission('edit_interview_schedule');
        $this->interview = RecruitInterviewSchedule::findOrFail($id);

        $this->recruit_employees = RecruitInterviewEmployees::where('recruit_interview_schedule_id', $id)->get();
        $this->selected_employees = $this->recruit_employees->pluck('user_id')->toArray();

        abort_403(!(
            $this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->interview->added_by == user()->id)
            || ($this->editPermission == 'owned' && in_array(user()->id, $this->selected_employees))
            || ($this->editPermission == 'both' && (in_array(user()->id, $this->selected_employees)
                    || $this->interview->added_by == user()->id))
        ));

        $this->candidates = RecruitJobApplication::all();
        $this->employees = User::allEmployees();
        $this->stages = RecruitInterviewStage::all();

        if (module_enabled('Zoom')) {
            $this->meeting = ZoomMeeting::where('meeting_id', $id)->get();
        }

        $this->comment = RecruitInterviewComments::where('recruit_interview_schedule_id', $this->interview->id)
            ->where('user_id', $this->user->id)->first();

        if (request()->ajax()) {
            $html = view('recruit::interview-schedule.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::interview-schedule.ajax.edit';

        return view('recruit::interview-schedule.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateMeeting $request, $id)
    {
        $interviewid = $id;
        $this->editPermission = user()->permission('edit_interview_schedule');
        $this->interview = RecruitInterviewSchedule::findOrFail($interviewid);
        $this->recruit_employees = RecruitInterviewEmployees::where('recruit_interview_schedule_id', $interviewid)->get();
        $this->selected_employees = $this->recruit_employees->pluck('user_id')->toArray();

        abort_403(!(
            $this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->interview->added_by == user()->id)
            || ($this->editPermission == 'owned' && in_array(user()->id, $this->selected_employees))
            || ($this->editPermission == 'both' && (in_array(user()->id, $this->selected_employees)
            || $this->interview->added_by == user()->id))
        ));

        if (module_enabled('Zoom')) {
            $this->setZoomConfigs();
        }

        // Update interview Schedule
        if ($request->meeting_id != '') {
            $interviewSchedule = RecruitInterviewSchedule::select('id', 'meeting_id', 'recruit_job_application_id', 'interview_type', 'video_type', 'phone', 'other_link', 'schedule_date', 'status')
                ->with([
                    'jobApplication:id,full_name,email,recruit_job_id,recruit_application_status_id',
                    'employees',
                    'comments',
                    'meeting'
                ])
                ->where('id', $interviewid)->first();
        }
        else {
            $interviewSchedule = RecruitInterviewSchedule::select('id', 'recruit_job_application_id', 'interview_type', 'video_type', 'phone', 'other_link', 'schedule_date', 'status')
                ->with([
                    'jobApplication:id,full_name,email,recruit_job_id,recruit_application_status_id',
                    'employees',
                    'comments',
                ])
                ->where('id', $interviewid)->first();
        }

        $interviewSchedule->schedule_date = Carbon::createFromFormat($this->company->date_format . ' ' . $this->company->time_format, $request->scheduleDate . ' ' . $request->scheduleTime, $this->company->timezone)->setTimezone('UTC');

        if ($request->interview_type == 'in person') {
            $interviewSchedule->interview_type = $request->interview_type;
            $interviewSchedule->video_type = 'other';
            $interviewSchedule->phone = null;
            $interviewSchedule->other_link = null;
            $interviewSchedule->meeting_id = null;

            if (module_enabled('Zoom')) {
                ZoomMeeting::where('id', $interviewSchedule->meeting_id)->delete();
            }

        }

        if ($request->interview_type == 'phone') {
            $interviewSchedule->interview_type = $request->interview_type;
            $interviewSchedule->phone = $request->phone;
            $interviewSchedule->video_type = 'other';
            $interviewSchedule->other_link = null;
            $interviewSchedule->meeting_id = null;

            if (module_enabled('Zoom')) {
                ZoomMeeting::where('id', $interviewSchedule->meeting_id)->delete();
            }

            $meeting = '';
        }

        if ($request->interview_type == 'video') {
            if ($request->video_type == 'other') {
                $interviewSchedule->interview_type = $request->interview_type;
                $interviewSchedule->video_type = $request->video_type;
                $interviewSchedule->other_link = $request->other_link;
                $interviewSchedule->phone = null;
                $interviewSchedule->meeting_id = null;

                if (module_enabled('Zoom')) {
                    ZoomMeeting::where('id', $interviewSchedule->meeting_id)->delete();
                }

                $meeting = '';
            }

            if ($request->video_type == 'zoom') {
                $interviewSchedule->interview_type = $request->interview_type;
                $interviewSchedule->video_type = $request->video_type;
                $interviewSchedule->phone = null;
                $interviewSchedule->other_link = null;
            }
        }

        $interviewSchedule->recruit_interview_stage_id = $request->stage_id;
        $interviewSchedule->last_updated_by = user()->id;
        $interviewSchedule->status = $request->status;
        $interviewSchedule->send_reminder_all = $request->send_reminder_all ? $request->send_reminder_all : '0';
        $interviewSchedule->remind_time_all = $request->remind_time_all;
        $interviewSchedule->remind_type_all = $request->remind_type_all;
        $interviewSchedule->notify_c = ($request->has('notify_c')) ? $request->notify_c : '0';
        $interviewSchedule->save();
        $employees = $request->employee_id;

        if ($request->comment || $request->candidate_comment) {
            $scheduleComment = [
                'comment' => $request->comment ?? null,
                'candidate_comment' => $request->candidate_comment ?? null
            ];

            $interviewSchedule->comments()->updateOrCreate([
                'recruit_interview_schedule_id' => $interviewSchedule->id,
                'user_id' => $this->user->id
            ], $scheduleComment);
        }

        $jobApplication = $interviewSchedule->jobApplication;
        // zoom meeting update

        if (module_enabled('Zoom')) {
            $host = User::findOrFail($this->interview->added_by);

            if ($request->video_type == 'zoom') {
                $user = Zoom::user()->find('me');

                $meeting = is_null($interviewSchedule->meeting_id) ? new ZoomMeeting() : ZoomMeeting::findOrFail($interviewSchedule->meeting_id);
                $data = $request->all();
                $data['meeting_name'] = $request->meeting_title;
                $start = Carbon::createFromFormat(company()->date_format.' '.company()->time_format, $request->scheduleDate . ' ' . $request->scheduleTime)->setTimezone('UTC');
                $end = Carbon::createFromFormat(company()->date_format.' '.company()->time_format, $request->end_date . ' ' . $request->end_time)->setTimezone('UTC');
                $data['start_date_time'] = $start->toDateTimeString();
                $data['end_date_time'] = $end->toDateTimeString();
                $data['status'] = 'waiting';

                if (is_null($interviewSchedule->meeting_id)) {
                    $meeting = $meeting->create($data);
                }
                else {
                    $meeting->update($data);
                }

                $meetings = $this->createMeeting($user, $meeting, $interviewSchedule->meeting_id, null, $host);
                $interviewSchedule->meeting_id = $meetings->id;
                $interviewSchedule->save();
            }
        }

        if (!is_null($employees)) {
            if (!empty($request->employee_id)) {
                $oldEmp = RecruitInterviewEmployees::where('recruit_interview_schedule_id', $interviewSchedule->id)->pluck('user_id')->toArray();
                $selectedEmp = array_map('intval', $request->employee_id);
                $toRemove = array_diff($oldEmp, $selectedEmp);
                $toAdd = array_diff($selectedEmp, $oldEmp);

                foreach($toRemove as $diff){
                    RecruitInterviewEmployees::where('user_id', $diff)->delete();
                }

                foreach($toAdd as $item){
                    $interviewEmployee = new RecruitInterviewEmployees();
                    $interviewEmployee->recruit_interview_schedule_id = $interviewSchedule->id;
                    $interviewEmployee->user_id = $item;
                    $interviewEmployee->save();
                }

                $employeeInfo = User::whereIn('id', $employees)->get();

                if ($employeeInfo) {
                    // Mail to employee for inform interview schedule
                    event(new UpdateInterviewScheduleEvent($interviewSchedule, $employeeInfo));
                }
            }
        }

        if ($interviewSchedule->save()) {
            $interviewSchedule->employees()->sync($request->employee_id);
        }

        $candidateComment = RecruitInterviewComments::where('recruit_interview_schedule_id', $interviewSchedule->id)->where('candidate_comment', '!=', 'null')->first() ?? null;

        if ($request->notify_c == '1' && $interviewSchedule->jobApplication->email != null) {
            event(new CandidateInterviewScheduleEvent($interviewSchedule, $interviewSchedule->jobApplication, $candidateComment));
        }

        if ($interviewSchedule->meeting_id != '') {
            // mail to meeting Host
            event(new HostInterviewEvent($interviewSchedule));
        }

        return Reply::successWithData(__('recruit::modules.message.updateSuccess'), ['redirectUrl' => route('interview-schedule.show', $interviewSchedule->id) . '?tab=details']);
    }

    public function reschedule()
    {
        $reschedulePermission = user()->permission('reschedule_interview');
        $id = request()->id;
        $this->recruit_employees = RecruitInterviewEmployees::where('recruit_interview_schedule_id', $id)->get();
        $this->selected_employees = $this->recruit_employees->pluck('user_id')->toArray();
        $this->interview = RecruitInterviewSchedule::findOrFail($id);
        $this->comment = RecruitInterviewComments::where('recruit_interview_schedule_id', $this->interview->id)
            ->where('user_id', $this->user->id)->first();

        abort_403(!($reschedulePermission == 'all' ||
            ($reschedulePermission == 'added' && $this->interview->added_by == user()->id) ||
            ($reschedulePermission == 'owned' && in_array(user()->id, $this->selected_employees)) ||
            ($reschedulePermission == 'both' && (in_array(user()->id, $this->selected_employees) ||
                    $this->interview->added_by == user()->id))));

        return view('recruit::interview-schedule.reschedule.create', $this->data);
    }

    public function rescheduleStore(Request $request)
    {
        $reschedulePermission = user()->permission('reschedule_interview');
        $id = $request->interview_schedule_id;
        $this->recruit_employees = RecruitInterviewEmployees::where('recruit_interview_schedule_id', $id)->get();
        $this->selected_employees = $this->recruit_employees->pluck('user_id')->toArray();
        $interview = RecruitInterviewSchedule::findOrFail($id);

        abort_403(!($reschedulePermission == 'all' ||
            ($reschedulePermission == 'added' && $this->interview->added_by == user()->id) ||
            ($reschedulePermission == 'owned' && in_array(user()->id, $this->selected_employees)) ||
            ($reschedulePermission == 'both' && (in_array(user()->id, $this->selected_employees) ||
                    $this->interview->added_by == user()->id))));



        if ($interview->meeting_id != '') {
            $interview = RecruitInterviewSchedule::select('id', 'meeting_id', 'recruit_job_application_id', 'interview_type', 'video_type', 'phone', 'other_link', 'schedule_date', 'status')
                ->with([
                    'jobApplication:id,full_name,email,recruit_job_id,recruit_application_status_id',
                    'employees',
                    'comments',
                    'meeting'
                ])
                ->where('id', $id)->first();
        }
        else {
            $interview = RecruitInterviewSchedule::select('id', 'recruit_job_application_id', 'interview_type', 'video_type', 'phone', 'other_link', 'schedule_date', 'status')
                ->with([
                    'jobApplication:id,full_name,email,recruit_job_id,recruit_application_status_id',
                    'employees',
                    'comments',
                ])
                ->where('id', $id)->first();
        }

        $date = Carbon::createFromFormat(company()->date_format, $request->scheduleDate)->format('Y-m-d');
        $interview->schedule_date = Carbon::createFromFormat('Y-m-d ' . $this->company->time_format, $date . ' ' . $request->scheduleTime, $this->company->timezone)->setTimezone('UTC');

        $interview->notify_c = ($request->has('notify_c')) ? $request->notify_c : '0';
        $interview->save();

        if (module_enabled('Zoom')) {
            $this->setZoomConfigs();

            // zoom meeting update
            if ($interview->video_type == 'zoom' && $request->end_date) {
                $meeting = is_null($interview->meeting->id) ? new ZoomMeeting() : ZoomMeeting::findOrFail($interview->meeting->id);
                $host = User::find($interview->meeting->create_by);
                $user = Zoom::user()->find('me');
                $data = $request->all();
                $start = Carbon::createFromFormat(company()->date_format.' '.company()->time_format, $request->scheduleDate . ' ' . $request->scheduleTime)->setTimezone('UTC');
                $end = Carbon::createFromFormat(company()->date_format.' '.company()->time_format, $request->end_date . ' ' . $request->end_time)->setTimezone('UTC');

                $data['meeting_name'] = $meeting->meeting_name;
                $data['start_date_time'] = $start->toDateTimeString();
                $data['end_date_time'] = $end->toDateTimeString();

                if (is_null($interview->meeting_id)) {
                    $meeting = $meeting->create($data);
                }
                else {
                    $meeting->update($data);
                }

                $meetings = $this->createMeeting($user, $meeting, $interview->meeting_id, null, $host);
                $interview->meeting_id = $meetings->id;
                $interview->save();
            }
        }

        $this->history = InterviewScheduleController::historyTrack(user()->id, 'reSchedule', $id);

        if ($request->candidate_comment) {
            $scheduleComment = [
                'candidate_comment' => $request->candidate_comment ?? null
            ];

            $interview->comments()->updateOrCreate([
                'recruit_interview_schedule_id' => $interview->id,
                'user_id' => $this->user->id
            ], $scheduleComment);
        }

        $candidateComment = RecruitInterviewComments::where('recruit_interview_schedule_id', $interview->id)->where('candidate_comment', '!=', 'null')->first() ?? null;

        // Mail to cansdidate
        if ($request->notify_c == '1' && $interview->jobApplication->email != null) {
            event(new CandidateInterviewRescheduleEvent($interview, $interview->jobApplication, $candidateComment));
        }

        if (!empty($request->employee_id)) {
            $interview->employees()->sync($request->employee_id);
            // Mail to employee for inform interview schedule
            event(new InterviewRescheduleEvent($interview, $interview->employees));
        }

        if ($interview->meeting_id != '') {
            // mail to meeting Host
            event(new HostInterviewEvent($interview));
        }

        return Reply::successWithData(__('recruit::messages.interviewReschedule'), ['redirectUrl' => route('interview-schedule.index')]);
    }

    public function historyTrack($userID, $text, $interviewID)
    {
        $activity = new RecruitInterviewHistory();

        if (!is_null($interviewID)) {
            $activity->recruit_interview_schedule_id = $interviewID;
        }

        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->save();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $interview = RecruitInterviewSchedule::findOrFail($id);

        if(is_null($interview->parent_id))
        {
            $interviews = RecruitInterviewSchedule::where('parent_id', $id)->get();
            $parentId = null;

            foreach ($interviews as $key => $record) {
                if ($key == 0) {
                    $record->parent_id = null;
                    $record->save();
                    $parentId = $record->id;
                }
                else {
                    $record->parent_id = $parentId;
                    $record->save();
                }
            }
        }

        $employees = RecruitInterviewEmployees::where('recruit_interview_schedule_id', $interview->id)->pluck('user_id')->toArray();
        $this->deletePermission = user()->permission('delete_interview_schedule');
        abort_403(!(
            $this->deletePermission == 'all'
            || ($this->deletePermission == 'added' && $interview->added_by == user()->id)
            || ($this->deletePermission == 'owned' && in_array(user()->id, $employees))
            || ($this->deletePermission == 'both' && (in_array(user()->id, $employees) || $interview->added_by == user()->id))
        ));

        $meeting_id = RecruitInterviewSchedule::select('meeting_id')->where('id', $id)->get();
        RecruitInterviewSchedule::destroy($interview->id);

        if (module_enabled('Zoom')) {
            $this->setZoomConfigs();
            ZoomMeeting::destroy($meeting_id[0]->meeting_id);
        }

        return Reply::successWithData(__('recruit::modules.message.deleteSuccess'), ['redirectUrl' => route('interview-schedule.index')]);
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
        abort_403(user()->permission('delete_interview_schedule') != 'all');

        RecruitInterviewSchedule::whereIn('id', explode(',', $request->row_ids))->delete();

        return true;
    }

    protected function changeStatus($request)
    {
        abort_403(user()->permission('edit_interview_schedule') != 'all');
        RecruitInterviewSchedule::whereIn('id', explode(',', $request->row_ids))->update(['status' => $request->status]);

        return true;
    }

    public function employeeResponse($id, $res)
    {
        $scheduleEmployee = RecruitInterviewEmployees::with('schedule', 'schedule.jobApplication')->find($id);

        // $users = User::allAdmins($scheduleEmployee->user->company->id); // Get All admins for mail
        $emp = User::find($scheduleEmployee->user_id);

        $type = 'refused';

        if ($res == 'accept') {
            $type = 'accepted';
        }

        $scheduleEmployee->user_accept_status = $res;

        Notification::send($emp, new EmployeeResponse($scheduleEmployee, $type, $this->user));
        $scheduleEmployee->save();

        return Reply::success(__('recruit::messages.responseAppliedSuccess'));
    }

    public function response(Request $request)
    {
        if($request->userId){
            $scheduleEmployee = RecruitInterviewEmployees::with('schedule', 'schedule.jobApplication')->where('recruit_interview_schedule_id', $request->interviewId)->where('user_id', $request->userId)->first();
        }
        else{
            $scheduleEmployee = RecruitInterviewEmployees::with('schedule', 'schedule.jobApplication')->find($request->responseId);
        }

        $emp = User::find($scheduleEmployee->user_id);
        $res = $request->action;
        $type = 'refused';

        if ($res == 'accept') {
            $type = 'accepted';
        }

        $scheduleEmployee->user_accept_status = $res;
        // mail to admin for employee response on refuse or accept
        Notification::send($emp, new EmployeeResponse($scheduleEmployee, $type, $this->user));

        $scheduleEmployee->save();

        return Reply::success(__('recruit::messages.responseAppliedSuccess'));
    }

    public function changeInterviewStatus(Request $request)
    {
        abort_403(user()->permission('edit_interview_schedule') != 'all');
        $interviewId = $request->interviewId;
        $status = $request->status;
        $interviewStatus = RecruitInterviewSchedule::findOrFail($interviewId);
        $interviewStatus->status = $status;
        $interviewStatus->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function fetchInterview(Request $request)
    {
        $this->interviewId = $request->interview_id;
        $this->interview = RecruitInterviewSchedule::findOrFail($this->interviewId);
        $this->interviewParent = RecruitInterviewSchedule::with('stage')->where('recruit_job_application_id', $this->interview->recruit_job_application_id)->get();
        $this->recruit_employees = RecruitInterviewEmployees::where('recruit_interview_schedule_id', $this->interviewId)->get();
        $this->selected_employees = $this->recruit_employees->pluck('user_id')->toArray();
        $this->candidates = RecruitJobApplication::all();
        $this->employees = User::allEmployees();
        $this->stages = RecruitInterviewStage::all();

        if (module_enabled('Zoom')) {
            $this->meeting = ZoomMeeting::where('meeting_id', $this->interviewId)->get();
        }

        $this->comment = RecruitInterviewComments::where('recruit_interview_schedule_id', $this->interview->id)
            ->where('user_id', $this->user->id)->first();
        $html = view('recruit::interview-schedule.ajax.edit-stage', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
    }

}
