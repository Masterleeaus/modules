<?php

namespace Modules\Asset\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\User;
use Carbon\Carbon as Carbon;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetHistory;
use Modules\Asset\Entities\AssetSetting;
use Modules\Asset\Http\Requests\LendRequest;
use Modules\Asset\Http\Requests\ReturnRequest;

class AssetHistoryController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(AssetSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function create($id)
    {
        $this->asset = Asset::findOrFail($id);
        $this->employees = User::allEmployees();

        return view('asset::asset.ajax.lend', $this->data);
    }

    public function store(LendRequest $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $assetHistory = new AssetHistory;
        $assetHistory->asset_id = $id;
        $assetHistory->user_id = $request->employee_id;
        $assetHistory->lender_id = user()->id;
        //phpcs:ignore
        $assetHistory->date_given = Carbon::createFromFormat($this->company->date_format, $request->date_given)->format('Y-m-d H:i:s');

        if ($request->has('return_date') && $request->return_date != '') {
            //phpcs:ignore
            $assetHistory->return_date = Carbon::createFromFormat($this->company->date_format, $request->return_date)->format('Y-m-d H:i:s');
        }

        if ($request->has('notes')) {
            $assetHistory->notes = $request->notes;
        }

        $assetHistory->save();

        $asset->status = 'lent';
        $asset->save();

        return Reply::success(__('asset::app.lendAssetMessage'));
    }

    //phpcs:ignore
    public function edit($assetId, $historyId)
    {
        $this->history = AssetHistory::findOrFail($historyId);
        $this->employees = User::allEmployees();

        return view('asset::asset.ajax.history-edit', $this->data);
    }

    //phpcs:ignore
    public function returnAsset($assetId, $historyId)
    {
        $this->history = AssetHistory::findOrFail($historyId);

        return view('asset::asset.ajax.return', $this->data);
    }

    /**
     * @return array
     */
    public function update(ReturnRequest $request, $assetId, $id)
    {
        $asset = Asset::findOrFail($assetId);
        $assetHistory = AssetHistory::findOrFail($id);

        $assetHistory->asset_id = $asset->id;

        if ($request->has('employee_id')) {
            $assetHistory->user_id = $request->employee_id;
        }

        if ($request->has('date_given')) {
            //phpcs:ignore
            $assetHistory->date_given = Carbon::createFromFormat(company()->date_format, $request->date_given)->format('Y-m-d H:i:s');
        }

        if ($request->has('return_date') && $request->return_date != '') {
            //phpcs:ignore
            $assetHistory->return_date = Carbon::createFromFormat(company()->date_format, $request->return_date)->format('Y-m-d H:i:s');
        }

        if ($request->has('date_of_return') && $request->date_of_return != '') {
            //phpcs:ignore
            $assetHistory->date_of_return = Carbon::createFromFormat(company()->date_format, $request->date_of_return)->format('Y-m-d H:i:s');

            $asset->status = 'available';
            $asset->save();
        }

        if ($request->has('notes')) {
            $assetHistory->notes = $request->notes;
        }

        $assetHistory->save();

        if ($request->show_page) {
            $this->asset = Asset::with(['history' => function ($query) {
                return $query->orderByDesc('id');
            }, 'assetType'])->findOrFail($assetId);

            $view = view('asset::asset.ajax.history', $this->data)->render();

            return Reply::successWithData(__('asset::app.historyUpdateSuccess'), ['view' => $view]);
        }

        return Reply::success(__('asset::app.historyUpdateSuccess'));
    }

    public function destroy($assetId, $id)
    {
        AssetHistory::destroy($id);

        $this->asset = Asset::with(['history' => function ($query) {
            return $query->orderByDesc('id');
        }, 'assetType'])->findOrFail($assetId);

        $view = view('asset::asset.ajax.history', $this->data)->render();

        return Reply::successWithData(__('asset::app.historyDeleteSuccess'), ['view' => $view]);
    }

}
