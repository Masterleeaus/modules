<?php

namespace Modules\Purchase\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\BankAccount;
use App\Models\CompanyAddress;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Tax;
use App\Models\UnitType;
use App\Traits\IconTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Purchase\DataTables\PurchaseOrderDataTable;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseOrderFile;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseVendor;

class PurchaseOrderFileController extends AccountBaseController
{

    use IconTrait;

    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = __('icon-people');
        $this->pageTitle = 'app.menu.purchaseOrder';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            foreach ($request->file as $fileData) {
                $file = new PurchaseOrderFile();
                $file->purchase_order_id = $request->order_id;

                $filename = Files::uploadLocalOrS3($fileData, PurchaseOrderFile::FILE_PATH);
                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();
                $file->save();
            }

        }

        $this->files = PurchaseOrderFile::where('purchase_order_id', $file->purchase_order_id)->orderByDesc('id')->get();
        $count = $this->files->count();

        $view = view('purchase::purchase-order.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view, 'count' => $count]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $file = PurchaseOrderFile::findOrFail($id);
        $this->deletePermission = user()->permission('delete_purchase_order');
        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $file->added_by == user()->id)));

        Files::deleteFile($file->hashname, 'purchase-order/' . $file->purchase_order_id);

        PurchaseOrderFile::destroy($id);

        $this->files = PurchaseOrderFile::where('purchase_order_id', $file->purchase_order_id)->orderByDesc('id')->get();
        $count = $this->files->count();
        $view = view('purchase::purchase-order.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view, 'count' => $count]);
    }

    public function download($id)
    {
        $file = PurchaseOrderFile::whereRaw('md5(id) = ?', $id)->firstOrFail();

        $this->viewPermission = user()->permission('view_purchase_order');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $file->added_by == user()->id)));

        return download_local_s3($file, 'purchase-order/' . $file->hashname);
    }

}
