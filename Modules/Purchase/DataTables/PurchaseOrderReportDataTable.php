<?php

namespace Modules\Purchase\DataTables;

use App\DataTables\BaseDataTable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseVendor;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class PurchaseOrderReportDataTable extends BaseDataTable
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */

    public function dataTable($query)
    {

        return datatables()
            ->eloquent($query)
            ->editColumn('primary_name', function ($row) {
                return $row->primary_name;
            })
            ->editColumn('count_total_order', function ($row) {
                return $row->count_total_order;
            })
            ->editColumn('order_amount', function ($row) {
                return currency_format($row->order_amount, $row->currency_id);
            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['purchase_order_count']);
    }

    /**
     * @param PurchaseVendor $model
     * @return \Illuminate\Database\Query\Builder
     */
    public function query(PurchaseOrder $model)
    {
        $request = $this->request();

        $vendor = $request->vendor;

        $model = $model
            ->select('purchase_vendors.primary_name', 'purchase_vendors.id', 'purchase_vendors.opening_balance', 'purchase_vendors.currency_id',
            DB::raw('( select count(purchase_orders.vendor_id) from purchase_orders where purchase_orders.vendor_id = purchase_vendors.id) as count_total_order'),
            DB::raw('( select sum(orderAmount.total) from purchase_orders as orderAmount where orderAmount.vendor_id = purchase_vendors.id) as order_amount'),
        )
            ->join('purchase_vendors', 'purchase_vendors.id', 'purchase_orders.vendor_id');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString();

            if (!is_null($startDate)) {
                $model = $model->where(DB::raw('DATE(purchase_orders.`purchase_date`)'), '>=', $startDate);

            }
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString();

            if (!is_null($endDate)) {
                $model = $model->where(function ($query) use ($endDate) {
                    $query->where(DB::raw('DATE(purchase_orders.`purchase_date`)'), '<=', $endDate);
                });
            }
        }

        if (!is_null($vendor) && $vendor !== 'all') {
            $model->where('purchase_vendors.id', $vendor);
        }

        $model->groupBy('purchase_vendors.id');

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('purchase-order-report-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["purchase-order-report-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }'
            ])
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'exportable' => false, 'title' => __('app.id')],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            __('purchase::app.vendor').' '.__('app.name') => ['data' => 'primary_name', 'name' => 'primary_name', 'orderable' => true,  'title' => __('purchase::app.vendor').' '.__('app.name')],
            __('purchase::app.menu.purchaseOrder').' '.__('purchase::modules.purchaseOrder.count') => ['data' => 'count_total_order', 'name' => 'count_total_order',  'orderable' => true, 'title' => __('purchase::app.menu.purchaseOrder').' '.__('purchase::modules.purchaseOrder.count')],
            __('app.amount') => ['data' => 'order_amount', 'name' => 'order_amount', 'orderable' => true, 'title' => __('app.amount')],
        ];
    }

}
