<?php

namespace Modules\Accountings\Http\Controllers;

use App\Helper\Reply;
use Modules\Accountings\Entities\JournalType;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Http\Requests\StoreType;

class JournalTypeController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle         = 'accountings::modules.acc.journalType';
        $this->activeSettingMenu = 'journalType';
    }

      /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->journaltype = JournalType::all();
        return view('accountings::accounting-settings.create-journaltype-modal', $this->data);
    }

      /**
     * @param StoreFloor $request
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreType $request)
    {
        $jt                    = new JournalType();
        $jt->type_journal_code = $request->type_journal_code;
        $jt->type_journal      = $request->type_journal;
        $jt->save();

        $alljt  = JournalType::all();
        $select = '<option value="">--</option>';
        foreach ($alljt as $type) {
            $select .= '<option value="' . $type->id . '">' . mb_ucwords($type->type_journal) . '</option>';
        }

        return Reply::successWithData(__('accountings::messages.addJournalType'), ['optionData' => $select]);
    }

      /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $this->floor = JournalType::findOrFail($id);
        return view('accountings::accounting-settings.edit-floor-modal', $this->data);
    }

      /**
     * @param UpdateFloor $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(StoreType $request, $id)
    {
        $jt                    = JournalType::findOrFail($id);
        $jt->type_journal_code = $request->type_journal_code ? strip_tags($request->type_journal_code) : $jt->type_journal_code;
        $jt->type_journal      = $request->type_journal ? strip_tags($request->type_journal) : $jt->type_journal;
        $jt->save();

        return Reply::success(__('accountings::messages.updateJournalType'));
    }

      /**
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        JournalType::destroy($id);
        return Reply::success(__('accountings::messages.deleteJournalType'));
    }
}
