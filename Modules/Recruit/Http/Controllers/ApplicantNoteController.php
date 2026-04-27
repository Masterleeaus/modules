<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Modules\Asset\Entities\AssetSetting;
use Modules\Recruit\Entities\RecruitApplicantNote;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Http\Requests\ApplicantNote\StoreJobApplicant;

class ApplicantNoteController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function store(StoreJobApplicant $request)
    {
        $addPermission = user()->permission('add_notes');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $note = new RecruitApplicantNote();
        $note->note_text = $request->note;
        $note->user_id = $this->user->id;
        $note->recruit_job_application_id = $request->applicationId;
        $note->save();

        $this->comments = RecruitApplicantNote::with('user')->where('recruit_job_application_id', $request->applicationId)->orderByDesc('id')->get();
        $view = view('recruit::job-applications.notes.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function edit($id)
    {
        $this->note = RecruitApplicantNote::with('jobApplication', 'user')->findOrFail($id);

        $this->editPermission = user()->permission('edit_notes');
        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->note->user_id == user()->id)
            || ($this->editPermission == 'owned' && user()->id == $this->note->jobApplication->job->recruiter_id)
            || ($this->editPermission == 'both' && user()->id == $this->note->jobApplication->job->recruiter_id)
            || $this->note->user_id == user()->id));

        return view('recruit::job-applications.notes.edit', $this->data);
    }

    public function update(StoreJobApplicant $request, $id)
    {
        $note = RecruitApplicantNote::with('jobApplication', 'jobApplication.job', 'user')->findOrFail($id);

        $this->editPermission = user()->permission('edit_notes');
        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->note->user_id == user()->id)
            || ($this->editPermission == 'owned' && user()->id == $note->jobApplication->job->recruiter_id)
            || ($this->editPermission == 'both' && user()->id == $note->jobApplication->job->recruiter_id)
            || $this->note->user_id == user()->id));

        $note->note_text = $request->note;
        $note->user_id = $this->user->id;
        $note->recruit_job_application_id = $request->applicationId;
        $note->save();

        $this->comments = RecruitApplicantNote::with('user')->where('recruit_job_application_id', $request->applicationId)->orderByDesc('id')->get();
        $view = view('recruit::job-applications.notes.show', $this->data)->render();

        return Reply::successWithData(__('recruit::messages.noteUpdateSuccess'), ['view' => $view]);
    }

    public function destroy($id)
    {
        $notes = RecruitApplicantNote::with('jobApplication', 'jobApplication.job')->findOrFail($id);

        $this->deletePermission = user()->permission('delete_notes');

        abort_403(!($this->deletePermission == 'all'
            || ($this->deletePermission == 'added' && $notes->user_id == user()->id)
            || ($this->deletePermission == 'owned' && user()->id == $notes->jobApplication->job->recruiter_id)
            || ($this->deletePermission == 'both' && user()->id == $notes->jobApplication->job->recruiter_id)
            || $notes->user_id == user()->id));

        $comment_task_id = $notes->recruit_job_application_id;
        $notes->delete();

        $this->comments = RecruitApplicantNote::with('user')->where('recruit_job_application_id', $comment_task_id)->orderByDesc('id')->get();
        $view = view('recruit::job-applications.notes.show', $this->data)->render();

        return Reply::successWithData(__('recruit::messages.noteDestroy'), ['view' => $view]);
    }

}
