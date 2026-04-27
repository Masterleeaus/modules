<?php

namespace Modules\Purchase\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\DataTables\PurchaseOrderReportDataTable;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseVendor;

/**
 * NOTE:
 * This controller is referenced by Purchase/Routes/web.php as a resource controller
 * for "order-report". Some upstream distributions ship the routes but omit this
 * file, which breaks artisan route:list and route caching.
 *
 * We keep this controller intentionally small and delegate to the same view/data
 * as ReportsController::orderReport().
 */
class PurchaseOrderReportController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = 'app.menu.reports';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display the purchase order report.
     *
     * @return Renderable
     */
    public function index()
    {
        $viewPermission = user()->permission('view_order_report');
        abort_403(!in_array($viewPermission, ['all']));

        // Align with the reports tab implementation
        $this->activeTab = 'order-report';
        $this->view = 'purchase::reports.ajax.purchase-order-report';
        $this->vendors = PurchaseVendor::all();

        $dataTable = new PurchaseOrderReportDataTable();
        return $dataTable->render('purchase::reports.index', $this->data);
    }

    /*
     |--------------------------------------------------------------------------
     | Resource methods not used
     |--------------------------------------------------------------------------
     */

    public function create() { abort(404); }
    public function store() { abort(404); }
    public function show($id) { abort(404); }
    public function edit($id) { abort(404); }
    public function update($id) { abort(404); }
    public function destroy($id) { abort(404); }
}
