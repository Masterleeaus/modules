<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitInterviewFile;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Entities\RecruitSetting;

class InterviewFileController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.interviewSchedule');
        $this->middleware(function ($request, $next) {
            abort_403(! in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(Request $request)
    {
        $this->addPermission = user()->permission('add_interview_schedule');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        if ($request->hasFile('file')) {
            RecruitInterviewSchedule::findOrFail($request->interview_id);
            
            foreach ($request->file as $fileData) {
                $file = new RecruitInterviewFile;
                $file->user_id = $this->user->id;
                $file->recruit_interview_schedule_id = $request->interview_id;

                $filename = Files::uploadLocalOrS3($fileData, 'interview-files/'.$request->interview_id);

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;

                $file->size = $fileData->getSize();
                $file->save();
            }

            $this->files = RecruitInterviewFile::where('recruit_interview_schedule_id', $request->interview_id)->orderByDesc('id');

            $this->files = $this->files->where('added_by', user()->id);

            $this->files = $this->files->get();
            $view = view('tasks.files.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $file = RecruitInterviewFile::findOrFail($id);
        $this->deletePermission = user()->permission('delete_interview_schedule');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $file->added_by == user()->id)));

        Files::deleteFile($file->hashname, 'interview-files/'.$file->recruit_job_id);

        RecruitInterviewFile::destroy($id);

        $this->files = RecruitInterviewFile::where('recruit_interview_schedule_id', $file->recruit_interview_schedule_id)->orderByDesc('id')->get();
        $view = view('tasks.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view]);
    }

    /**
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        $file = RecruitInterviewFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->viewPermission = user()->permission('view_job');
        abort_403(! ($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $file->added_by == user()->id)));

        return download_local_s3($file, 'interview-files/'.$file->recruit_interview_schedule_id.'/'.$file->hashname);
    }
}
