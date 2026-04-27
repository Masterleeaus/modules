<?php

namespace Modules\Accountings\Http\Controllers;

use App\Helper\Reply;
use App\Models\BaseModel;
use Illuminate\Http\Request;
use Modules\Accountings\Entities\Accounting;
use Modules\Accountings\Entities\BalanceSheet;
use Modules\Accountings\Entities\Pnl;
use Modules\Accountings\DataTables\AccountingDataTable;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Http\Requests\StoreAcc;

class AccountingController extends AccountBaseController
{
    public $arr = [];

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'accountings::modules.acc.accountings';
        $this->pageIcon  = 'ti-settings';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(AccountingDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_acc');
        abort_403(!in_array($viewPermission, ['all']));

        $this->acc_coa    = Accounting::all();
        $this->bs         = BalanceSheet::all();
        $this->pnl        = Pnl::all();
        $this->totalUnits = count($this->acc_coa);
        return $dataTable->render('accountings::accounting.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $viewPermission = user()->permission('add_acc');
        abort_403(!in_array($viewPermission, ['all']));

        $this->pageTitle = __('accountings::app.acc.addAcc');
        $this->bs        = BalanceSheet::all();
        $this->pnl       = Pnl::all();

        if (request()->ajax()) {
            $html = view('accountings::accounting.ajax.create', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'accountings::accounting.ajax.create';
        return view('accountings::accounting.create', $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreAcc $request)
    {
        $redirectUrl   = route('accountings.index');
        $acc           = new Accounting();
        $acc->bs_id    = $request->input('bs_id');
        $acc->pnl_id   = $request->input('pnl_id');
        $acc->coa      = $request->input('coa');
        $acc->coa_desc = $request->input('coa_desc');
        $acc->save();

        return Reply::successWithData(__('accountings::messages.addAcc'), ['redirectUrl' => $redirectUrl]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $viewPermission = user()->permission('view_acc');
        abort_403(!in_array($viewPermission, ['all']));

        $this->pageTitle = __('accountings::app.acc.showAcc');
        $this->acc       = Accounting::findOrFail($id);

        if (request()->ajax()) {
            $html = view('accountings::accounting.ajax.show', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'accountings::accounting.ajax.show';
        return view('accountings::accounting.create', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $editPermission = user()->permission('edit_acc');
        abort_403(!in_array($editPermission, ['all']));

        $this->pageTitle = __('accountings::app.acc.editAcc');
        $this->acc       = Accounting::findOrFail($id);
        $this->bs        = BalanceSheet::all();
        $this->pnl       = Pnl::all();

        if (request()->ajax()) {
            $html = view('accountings::accounting.ajax.edit', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'accountings::accounting.ajax.edit';
        return view('accountings::accounting.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(StoreAcc $request, $id)
    {
        $editUnit = user()->permission('edit_acc');
        abort_403($editUnit != 'all');

        $redirectUrl   = route('accountings.index');
        $acc           = Accounting::find($id);
        $acc->bs_id    = $request->input('bs_id');
        $acc->pnl_id   = $request->input('pnl_id');
        $acc->coa      = $request->input('coa');
        $acc->coa_desc = $request->input('coa_desc');
        $acc->save();

        return Reply::successWithData(__('accountings::messages.updateAcc'), ['redirectUrl' => $redirectUrl]);
    }

    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
            case 'delete':
                $this->deleteRecords($request);
                return Reply::success(__('accountings::messages.deleteAcc'));
            default:
                return Reply::error(__('accountings::messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        Accounting::whereIn('id', explode(',', $request->row_ids))->forceDelete();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $deletePermission = user()->permission('delete_acc');
        abort_403($deletePermission != 'all');

        Accounting::destroy($id);
        $redirectUrl = route('accountings.index');
        return Reply::successWithData(__('accountings::messages.deleteAcc'), ['redirectUrl' => $redirectUrl]);
    }
}
