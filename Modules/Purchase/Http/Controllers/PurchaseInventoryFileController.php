<?php

namespace Modules\Purchase\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Traits\IconTrait;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Purchase\Entities\PurchaseInventory;
use Modules\Purchase\Entities\PurchaseInventoryFile;
use Modules\Purchase\Entities\PurchaseSetting;

class PurchaseInventoryFileController extends AccountBaseController
{

    use IconTrait;

    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = __('icon-people');
        $this->pageTitle = 'purchase::app.menu.inventory';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {

            $defaultImage = null;

            foreach ($request->file as $fileData) {
                $file = new PurchaseInventoryFile();
                $file->inventory_id = $request->inventory_id;

                $filename = Files::uploadLocalOrS3($fileData, PurchaseInventoryFile::FILE_PATH);

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();
                $file->save();

                if ($fileData->getClientOriginalName() == $request->default_image) {
                    $defaultImage = $filename;
                }

            }

            $inventoryFile = PurchaseInventory::findOrFail($request->inventory_id);
            $inventoryFile->default_image = $defaultImage;
            $inventoryFile->save();

        }

        return Reply::success(__('messages.fileUploaded'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        PurchaseInventoryFile::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function download($id)
    {
        $file = PurchaseInventoryFile::findOrFail($id);

        return download_local_s3($file, PurchaseInventoryFile::FILE_PATH . '/' . $file->hashname);
    }

}
