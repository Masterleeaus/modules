<?php
namespace Modules\CleanQuality\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\CleanQuality\Support\ModuleAccess;
use Modules\CleanQuality\Entities\ScheduleFile;
use Modules\CleanQuality\Entities\ScheduleReply;

class ScheduleFileController extends AccountBaseController
{

    public function store(Request $request)
    {
        abort_403(ModuleAccess::permissionLevel('add_quality_control') !== 'all');

        if ($request->hasFile('file')) {
            $replyId = $request->schedule_reply_id;

            if ($request->schedule_reply_id == '') {
                $reply = new ScheduleReply();
                $reply->schedule_id = $request->schedule_id;
                $reply->user_id = $this->user->id; // Current logged in user
                $reply->save();
                $replyId = $reply->id;
            }

            foreach ((array) $request->file('file') as $fileData) {
                $file = new ScheduleFile();
                $file->schedule_reply_id = $replyId;
                $filename = Files::uploadLocalOrS3($fileData, ScheduleFile::FILE_PATH . '/' . $replyId);
                $file->user_id = $this->user->id;
                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();
                $file->save();
            }
        }

        return Reply::dataOnly(['status' => 'success']);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        abort_403(ModuleAccess::permissionLevel('delete_quality_control') !== 'all');

        $file = ScheduleFile::findOrFail($id);

        Files::deleteFile($file->hashname, 'schedule-files/' . $file->schedule_reply_id);
        ScheduleFile::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function show($id)
    {
        $file = ScheduleFile::whereRaw('md5(id) = ?', [$id])->firstOrFail();
        $this->filepath = $file->file_url;
        return view('quality_control::files.view', $this->data);
    }

    /**
     * @param mixed $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        $file = ScheduleFile::whereRaw('md5(id) = ?', [$id])->firstOrFail();
        return download_local_s3($file, 'schedule-files/' . $file->schedule_reply_id . '/' . $file->hashname);
    }

}
