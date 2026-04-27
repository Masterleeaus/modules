<?php

namespace Modules\Asset\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\PermissionType;
use App\Models\User;
use Illuminate\Http\Response;
use Modules\Asset\DataTables\AssetDataTable;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetSetting;
use Modules\Asset\Entities\AssetType;
use Modules\Asset\Http\Requests\StoreRequest;
use Modules\Asset\Http\Requests\UpdateRequest;

class AssetController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(AssetSetting::MODULE_NAME, $this->user->modules));
            $this->pageTitle = __('asset::app.menu.asset');

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(AssetDataTable $dataTable)
    {
        $this->viewAssetPermission = user()->permission('view_asset');

        abort_403($this->viewAssetPermission == 'none');

        $this->assetType = AssetType::all();
        $this->employees = User::allEmployees();
        $this->status = array_keys(Asset::STATUSES);

        return $dataTable->render('asset::asset.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->addPermission = user()->permission('add_asset');
        abort_403($this->addPermission !== 'all');

        $this->assets = new Asset;
        $this->assetType = AssetType::all();

        $this->view = 'asset::asset.ajax.create';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('asset::asset.create', $this->data);

    }

    public function store(StoreRequest $request)
    {
        $asset = new Asset;
        $this->storeUpdate($asset, $request);

        return Reply::successWithData(__('asset::app.storeSuccess'), ['redirectUrl' => route('assets.index')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        $this->editPermission = user()->permission('edit_asset');
        abort_403($this->editPermission !== 'all');

        $this->asset = Asset::findOrFail($id);
        $this->assetType = AssetType::all();

        $this->view = 'asset::asset.ajax.edit';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('asset::asset.create', $this->data);
    }

    public function update(UpdateRequest $request, $id)
    {
        $asset = Asset::findOrFail($id);
        $this->storeUpdate($asset, $request);

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('assets.index')]);
    }

    private function storeUpdate($asset, $request)
    {
        $asset->name = $request->name;
        $asset->serial_number = $request->serial_number;
        $asset->asset_type_id = $request->asset_type_id;
        $asset->value = $request->value;
        $asset->location = $request->location;

        if ($request->has('description')) {
            $asset->description = $request->description;
        }

        if ($asset->status != 'lent') {
            $asset->status = $request->status;
        }

        if ($request->image_delete == 'yes') {
            Files::deleteFile($asset->image, 'assets');
            $asset->image = null;
        }

        if ($request->hasFile('image')) {
            Files::deleteFile($asset->image, 'assets');
            $asset->image = Files::uploadLocalOrS3($request->image, 'assets');
        }

        $asset->save();
    }

    public function show($id)
    {
        $viewPermission = user()->permission('view_asset');
        abort_403($viewPermission == 'none');

        $this->asset = Asset::with(
            ['history' => function ($query) use ($viewPermission) {

                if (in_array($viewPermission, ['owned', 'both'])) {
                    $query->where('user_id', user()->id);
                }

                return $query->orderByDesc('id');

            }, 'assetType'])
            ->findOrFail($id);

        $this->viewPermission = $viewPermission;
        $this->history = 'asset::asset.ajax.history';
        $this->view = 'asset::asset.ajax.show';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('asset::asset.create', $this->data);
    }

    public function destroy($id)
    {
        $deletePermission = user()->permission('delete_asset');
        abort_403(!in_array($deletePermission, ['all', 'added']));

        Asset::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));

    }

}
