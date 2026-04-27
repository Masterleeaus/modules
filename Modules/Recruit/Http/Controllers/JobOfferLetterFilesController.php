<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitJobOfferLetterFiles;

class JobOfferLetterFilesController extends AccountBaseController
{
    /**
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $addPermission = user()->permission('add_offer_letter');
        abort_403(! in_array($addPermission, ['all', 'added']));

        if ($request->file) {
            foreach ($request->file as $fileData) {
                $file = new RecruitJobOfferLetterFiles;
                $file->recruit_job_offer_letter_id = $request->applicationID;
                $filename = Files::uploadLocalOrS3($fileData, 'application-files/'.$request->applicationID);

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;

                $file->save();
            }

            $this->files = RecruitJobOfferLetterFiles::where('recruit_job_offer_letter_id', $request->applicationID)->orderByDesc('id')->get();
            $view = view('projects.files.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $file = RecruitJobOfferLetterFiles::findOrFail($id);

        Files::deleteFile($file->hashname, 'application-files/');

        RecruitJobOfferLetterFiles::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function download($id)
    {
        $file = RecruitJobOfferLetterFiles::whereRaw('md5(id) = ?', $id)->firstOrFail();

        return download_local_s3($file, 'application-files/'.$file->recruit_job_offer_letter_id.'/'.$file->hashname);
    }
}
