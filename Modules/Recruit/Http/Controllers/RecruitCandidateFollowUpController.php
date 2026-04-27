<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitCandidateFollowUp;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Http\Requests\JobApplication\StoreFollowUpRequest;
use Modules\Recruit\Http\Requests\JobApplication\UpdateFollowUpRequest;

class RecruitCandidateFollowUpController extends AccountBaseController
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create(Request $request)
    {
        $this->requestFromDatatable = $request->datatable;
        $this->followUps = RecruitCandidateFollowUp::all();
        $this->application = RecruitJobApplication::where('id', $request->id)->first();

        return view('recruit::job-applications.follow-up.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(StoreFollowUpRequest $request)
    {
        $followUp = new RecruitCandidateFollowUp;
        $followUp->recruit_job_application_id = $request->candidate_id;

        $followUp->next_follow_up_date = Carbon::createFromFormat($this->company->date_format.' '.$this->company->time_format, $request->next_follow_up_date.' '.$request->start_time)->format('Y-m-d H:i:s');

        $followUp->status = 'incomplete';
        $followUp->remark = $request->remark;
        $followUp->send_reminder = $request->send_reminder;
        $followUp->remind_time = $request->remind_time;
        $followUp->remind_type = $request->remind_type;
        $followUp->save();

        $this->followUps = RecruitCandidateFollowUp::where('recruit_job_application_id', $request->candidate_id)->get();
        $this->application = RecruitJobApplication::where('id', $request->candidate_id)->first();
        $requestFromDatatable = $request->request_from_datatable;

        $view = view('recruit::job-applications.ajax.follow-up', $this->data)->render();

        return Reply::successWithData(__('messages.recordSaved'), ['view' => $view, 'requestFromDatatable' => $requestFromDatatable]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->follow = RecruitCandidateFollowUp::findOrFail($id);

        $this->editPermission = user()->permission('edit_job_application');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->follow->added_by == user()->id)));

        return view('recruit::job-applications.follow-up.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(UpdateFollowUpRequest $request)
    {
        $this->application = RecruitJobApplication::findOrFail($request->recruit_job_application_id);
        $followUp = RecruitCandidateFollowUp::findOrFail($request->id);

        $this->editPermission = user()->permission('edit_job_application');

        abort_403(! ($this->editPermission == 'all'
        || ($this->editPermission == 'added' && $followUp->added_by == user()->id)
        ));

        $followUp->recruit_job_application_id = $request->recruit_job_application_id;

        $followUp->next_follow_up_date = Carbon::createFromFormat($this->company->date_format.' '.$this->company->time_format, $request->next_follow_up_date.' '.$request->start_time)->format('Y-m-d H:i:s');

        $followUp->remark = $request->remark;
        $followUp->send_reminder = $request->send_reminder;
        $followUp->remind_time = $request->remind_time;
        $followUp->remind_type = $request->remind_type;

        $followUp->save();

        $this->followUps = RecruitCandidateFollowUp::where('recruit_job_application_id', $request->recruit_job_application_id)->get();
        $view = view('recruit::job-applications.ajax.follow-up', $this->data)->render();

        return Reply::successWithData(__('messages.updateSuccess'), ['view' => $view]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $followUp = RecruitCandidateFollowUp::findOrFail($id);
        $this->deletePermission = user()->permission('delete_job_application');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $followUp->added_by == user()->id)));

        RecruitCandidateFollowUp::destroy($id);

        $this->followUps = RecruitCandidateFollowUp::where('recruit_job_application_id', $followUp->recruit_job_application_id)->get();
        $this->application = RecruitJobApplication::where('id', $followUp->recruit_job_application_id)->first();
        $view = view('recruit::job-applications.ajax.follow-up', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view]);
    }

    public function changefollowUpStatus(Request $request)
    {
        $followUp = RecruitCandidateFollowUp::find($request->id);
        $followUp->status = lcfirst($request->status);
        $followUp->update();

        return Reply::success(__('messages.updateSuccess'));
    }
}
