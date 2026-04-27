<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitInterviewEvaluation;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitRecommendationStatus;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Http\Requests\Evaluation\StoreEvaluation;

class EvaluationController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.evaluation');
        $this->middleware(function ($request, $next) {
            abort_403(! in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        $this->addPermission = user()->permission('add_interview_schedule');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('app.add').' '.__('recruit::modules.interviewSchedule.evaluation');
        $this->candidates = RecruitJobApplication::all();
        $this->interviews = RecruitInterviewSchedule::all();
        $this->statuses = RecruitRecommendationStatus::all();
        $this->users = User::all();
        $this->interview_schedule_id = request()->id;
        $this->attendees = RecruitInterviewSchedule::with(['employees'])
            ->where('id', $this->interview_schedule_id)->first();

        $this->interview = RecruitInterviewSchedule::with(['jobApplication', 'jobApplication.job', 'jobApplication.job.stages'])->find(request()->id);
        $this->applicationId = $this->interview->jobApplication->id;
        $this->totalStages = $this->interview->jobApplication->job->stages->pluck('name', 'id')->toArray();

        if (request()->ajax()) {
            $html = view('recruit::interview-schedule.evaluation.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::interview-schedule.evaluation.create';

        return view('recruit::interview-schedule.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(StoreEvaluation $request)
    {
        $this->addPermission = user()->permission('add_interview_schedule');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $evaluation = new RecruitInterviewEvaluation;
        $evaluation->recruit_recommendation_status_id = $request->status_id;
        $evaluation->recruit_job_application_id = $request->job_application_id;
        $evaluation->recruit_interview_stage_id = $request->stage_id;
        $evaluation->details = $request->details;
        $evaluation->recruit_interview_schedule_id = $request->interview_schedule_id;
        $evaluation->submitted_by = $request->user()->id;
        $evaluation->save();

        return Reply::successWithData(__('messages.evaluationsAdded'), ['redirectUrl' => route('interview-schedule.show', $request->interview_schedule_id) . '?view=evaluations']);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->pageTitle = __('app.update').' '.__('recruit::modules.interviewSchedule.evaluation');
        $this->evaluation = RecruitInterviewEvaluation::findOrFail($id);
        abort_403(! ($this->evaluation->submitted_by == user()->id));

        $this->candidates = RecruitJobApplication::all();
        $this->interviews = RecruitInterviewSchedule::all();
        $this->statuses = RecruitRecommendationStatus::all();

        $this->users = User::all();
        $this->interview_schedule_id = request()->id;

        if (request()->ajax()) {
            $html = view('recruit::interview-schedule.evaluation.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::interview-schedule.evaluation.edit';

        return view('recruit::interview-schedule.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(StoreEvaluation $request, $id)
    {
        $evaluation = RecruitInterviewEvaluation::findOrFail($id);
        abort_403(! ($evaluation->submitted_by == user()->id));

        $evaluation->recruit_recommendation_status_id = $request->status_id;
        $evaluation->details = $request->details;
        $evaluation->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('interview-schedule.show', $evaluation->recruit_interview_schedule_id) . '?view=evaluations']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $evaluation = RecruitInterviewEvaluation::findOrFail($id);
        abort_403(! ($evaluation->submitted_by == user()->id));
        RecruitInterviewEvaluation::destroy($id);

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('interview-schedule.show', $evaluation->recruit_interview_schedule_id) . '?view=evaluations']);
    }
}
