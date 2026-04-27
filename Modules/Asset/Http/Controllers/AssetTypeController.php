<?php

namespace Modules\Asset\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Response;
use Modules\Asset\Entities\AssetSetting;
use Modules\Asset\Entities\AssetType;
use Modules\Asset\Http\Requests\AssetType\StoreRequest;

class AssetTypeController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            abort_403(! in_array(AssetSetting::MODULE_NAME, $this->user->modules));
            $this->pageTitle = __('asset::app.menu.asset');

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->assetTypes = AssetType::all();

        return view('asset::asset-type.create', $this->data);
    }

    public function store(StoreRequest $request)
    {
        $assetType = AssetType::create($request->all());

        $assetTypes = AssetType::allAssetTypes();
        $options = '<option value="">--</option>';

        foreach ($assetTypes as $item) {
            $selected = '';

            if ($item->id == $assetType->id) {
                $selected = 'selected';
            }

            $options .= '<option '.$selected.' value="'.$item->id.'"> '.$item->name.' </option>';
        }

        return Reply::successWithData(__('asset::app.typeStoreSuccess'), ['data' => $options]);
    }

    public function update(StoreRequest $request, $id)
    {
        AssetType::where('id', $id)->update(['name' => $request->name]);

        $assetTypes = AssetType::allAssetTypes();
        $options = '<option value="">--</option>';

        foreach ($assetTypes as $item) {
            $selected = '';

            if ($item->id == $id) {
                $selected = 'selected';
            }

            $options .= '<option '.$selected.' value="'.$item->id.'"> '.$item->name.' </option>';
        }

        return Reply::successWithData(__('asset::app.typeUpdateSuccess'), ['data' => $options]);
    }

    public function destroy($id)
    {
        AssetType::destroy($id);
        $assetTypes = AssetType::allAssetTypes();

        $options = '<option value="">--</option>';

        foreach ($assetTypes as $item) {
            $options .= '<option value="'.$item->id.'"> '.$item->name.' </option>';
        }

        return Reply::successWithData(__('asset::app.typeDeleteSuccess'), ['data' => $options]);
    }
}
