<?php

namespace Modules\Accountings\Http\Controllers;

use App\Helper\Reply;
use Modules\Accountings\Entities\Pnl;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Http\Requests\StorePnl;

class PnlController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle         = 'accountings::modules.pnl.pnl';
        $this->activeSettingMenu = 'pnl';
    }

      /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->pnl = Pnl::all();
        return view('accountings::accounting-settings.create-pnl-modal', $this->data);
    }

      /**
     * @param StoreFloor $request
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StorePnl $request)
    {
        $pnl             = new Pnl();
        $pnl->company_id = 1;
        $pnl->seq        = $request->seq;
        $pnl->pnl_name   = $request->pnl_name;
        $pnl->pnl_type   = $request->pnl_type;
        $pnl->pnl_group  = $request->pnl_group;
        $pnl->save();
        $allpnl = Pnl::all();

        $select = '<option value="">--</option>';
        foreach ($allpnl as $balance) {
            $select .= '<option value="' . $balance->id . '">' . mb_ucwords($balance->pnl_name) . '</option>';
        }

        return Reply::successWithData(__('accountings::messages.addPnl'), ['optionData' => $select]);
    }

      /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $this->floor = Pnl::findOrFail($id);
        return view('accountings::accounting-settings.edit-floor-modal', $this->data);
    }

      /**
     * @param UpdateFloor $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(StorePnl $request, $id)
    {
        $pnl            = Pnl::findOrFail($id);
        $pnl->seq       = $request->seq ? strip_tags($request->seq) : $pnl->seq;
        $pnl->pnl_name  = $request->pnl_name ? strip_tags($request->pnl_name) : $pnl->pnl_name;
        $pnl->pnl_type  = $request->pnl_type ? $request->pnl_type : $pnl->pnl_type;
        $pnl->pnl_group = $request->pnl_group ? $request->pnl_group : $pnl->pnl_group;
        $pnl->save();

        return Reply::success(__('accountings::messages.updatePnl'));
    }

      /**
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        Pnl::destroy($id);
        return Reply::success(__('accountings::messages.deletePnl'));
    }
}
