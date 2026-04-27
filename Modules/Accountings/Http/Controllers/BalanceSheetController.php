<?php

namespace Modules\Accountings\Http\Controllers;

use App\Helper\Reply;
use Modules\Accountings\Entities\BalanceSheet;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Http\Requests\StoreBs;

class BalanceSheetController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle         = 'accountings::modules.bs.balanceSheet';
        $this->activeSettingMenu = 'balanceSheet';
    }

      /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->bs = BalanceSheet::all();
        return view('accountings::accounting-settings.create-bs-modal', $this->data);
    }

      /**
     * @param StoreFloor $request
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreBs $request)
    {
        $bs           = new BalanceSheet();
        $bs->seq      = $request->seq;
        $bs->bs_name  = $request->bs_name;
        $bs->bs_type  = $request->bs_type;
        $bs->bs_group = $request->bs_group;
        $bs->save();

        $allBS  = BalanceSheet::all();
        $select = '<option value="">--</option>';
        foreach ($allBS as $balance) {
            $select .= '<option value="' . $balance->id . '">' . mb_ucwords($balance->bs_name) . '</option>';
        }

        return Reply::successWithData(__('accountings::messages.addBS'), ['optionData' => $select]);
    }

      /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $this->floor = BalanceSheet::findOrFail($id);
        return view('accountings::accounting-settings.edit-floor-modal', $this->data);
    }

      /**
     * @param UpdateFloor $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(StoreBs $request, $id)
    {
        $bs           = BalanceSheet::findOrFail($id);
        $bs->seq      = $request->seq ? strip_tags($request->seq) : $bs->seq;
        $bs->bs_name  = $request->bs_name ? strip_tags($request->bs_name) : $bs->bs_name;
        $bs->bs_type  = $request->bs_type ? $request->bs_type : $bs->bs_type;
        $bs->bs_group = $request->bs_group ? $request->bs_group : $bs->bs_group;
        $bs->save();

        return Reply::success(__('accountings::messages.updateBS'));
    }

      /**
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        BalanceSheet::destroy($id);
        return Reply::success(__('accountings::messages.deleteBS'));
    }
}
