<?php

namespace Modules\Asset\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\ProjectCategory;
use Modules\Asset\Entities\AssetType;
use Modules\Asset\Entities\AssetSetting;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\AccountBaseController;

class AssetSettingController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            abort_403(! in_array(AssetSetting::MODULE_NAME, $this->user->modules));
            $this->pageTitle = __('asset::app.menu.asset');

            return $next($request);
        });
        $this->activeSettingMenu = 'asset_settings';
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $this->projectCategory = ProjectCategory::all();
        $this->assetTypes = AssetType::all();
        $this->view = 'asset::asset-settings.type';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('asset::asset-settings.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('asset::asset-settings.create-asset-type-settings-modal', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->assetType = AssetType::findOrfail($id);
        return view('asset::asset-settings.edit-asset-type-settings-modal', $this->data);
    }
}
