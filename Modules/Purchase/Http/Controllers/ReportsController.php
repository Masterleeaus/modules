<?php

namespace Modules\Purchase\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\AccountBaseController;
use Modules\Purchase\DataTables\InventorySummaryDataTable;
use Modules\Purchase\DataTables\InventoryValuationSummaryDatatable;
use Modules\Purchase\DataTables\PurchaseOrderReportDataTable;
use Modules\Purchase\DataTables\VendorReportDataTable;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseVendor;

class ReportsController extends AccountBaseController
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.reports';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index()
    {
        $this->pageTitle = 'app.menu.reports';

        $tab = request('tab');

        switch ($tab) {
        case 'order-report':
            return $this->orderReport();
        case 'inventory-summary':
            return $this->inventorySummary();
        case 'inventory-valuation-summary':
            return $this->inventoryValuationSummary();
        default:
            return $this->vendorReport();
        }

        $this->activeTab = $tab ?: 'general';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('purchase::reports.index', $this->data);
    }

    public function inventorySummary()
    {
        $this->viewInventoryPermission = user()->permission('view_inventory_summary');
        abort_403(!in_array($this->viewInventoryPermission, ['all']));

        $dataTable = new InventorySummaryDataTable();
        $tab = request('tab');
        $this->activeTab = $tab ?: 'inventory-summary';
        $this->view = 'purchase::reports.ajax.inventory-summary';

        return $dataTable->render('purchase::reports.index', $this->data);
    }

    public function vendorReport()
    {
        $viewPermission = user()->permission('view_vendor_report');
        abort_403(!in_array($viewPermission, ['all']));

        $tab = request('tab');
        $this->activeTab = $tab ?: 'vendor-balance';
        $this->view = 'purchase::reports.ajax.vendor-balance-report';

        $this->vendors = PurchaseVendor::all();
        $dataTable = new VendorReportDataTable();

        return $dataTable->render('purchase::reports.index', $this->data);
    }

    public function orderReport()
    {
        $viewPermission = user()->permission('view_order_report');
        abort_403(!in_array($viewPermission, ['all']));

        $tab = request('tab');
        $this->activeTab = $tab ?: 'order-report';
        $this->view = 'purchase::reports.ajax.purchase-order-report';

        $this->vendors = PurchaseVendor::all();
        $dataTable = new PurchaseOrderReportDataTable();

        return $dataTable->render('purchase::reports.index', $this->data);
    }

    public function inventoryValuationSummary()
    {
        $viewPermission = user()->permission('view_inventory_valuation_summary');
        abort_403(!in_array($viewPermission, ['all']));

        $tab = request('tab');
        $this->activeTab = $tab ?: 'inventory-valuation-summary';
        $this->view = 'purchase::reports.ajax.inventory-valuation-summary';

        $dataTable = new InventoryValuationSummaryDatatable();

        return $dataTable->render('purchase::reports.index', $this->data);
    }

}
