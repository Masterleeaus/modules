<?php

namespace Modules\Purchase\DataTables;

use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Purchase\Entities\PurchaseOrder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;

class PurchaseOrderDataTable extends BaseDataTable
{
    private $viewOrderPermission;
    private $deleteOrderPermission;
    private $editOrderPermission;

    public function __construct()
    {
        parent::__construct();
        $this->viewOrderPermission = user()->permission('view_purchase_order');
        $this->deleteOrderPermission = user()->permission('delete_purchase_order');
        $this->editOrderPermission = user()->permission('edit_purchase_order');
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
            ->addColumn('action', function ($row) {

                $action = '<div class="task_view">';

                $action .= '<div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= '<a href="' . route('purchase-order.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                if (
                    $this->viewOrderPermission == 'all'
                    || ($this->viewOrderPermission == 'added' && user()->id == $row->added_by)
                ) {
                    $action .= '<a class="dropdown-item" href="' . route('purchase_order.download', [$row->id]) . '">
                                    <i class="fa fa-download mr-2"></i>
                                    ' . trans('app.download') . '
                                </a>';
                    $action .= '<a class="dropdown-item" target="_blank" href="' . route('purchase_order.download', [$row->id, 'view' => true]) . '">
                                    <i class="fa fa-eye mr-2"></i>
                                    ' . trans('app.viewPdf') . '
                                </a>';
                }

                if ($row->purchase_status != 'canceled') {
                    $action .= '<a class="dropdown-item sendButton" href="javascript:;" data-toggle="tooltip"  data-order-id="' . $row->id . '">
                                    <i class="fa fa-paper-plane mr-2"></i>
                                    ' . trans('app.send') . '
                                </a>';
                }

                if ($row->status != 'canceled' && $row->send_status == 0) {
                    $action .= '<a class="dropdown-item sendButton d-flex justify-content-between align-items-center" data-type="mark_as_send" href="javascript:;"  data-order-id="' . $row->id . '">
                                    <div><i class="fa fa-check-double mr-2"></i>
                                    ' . trans('app.markSent') . '
                                    </div>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-original-title="'.__('messages.markSentInfo').'"></i>
                                </a>';
                }

                $edit = '<a class="dropdown-item openRightModal" href="' . route('purchase-order.edit', $row->id) . '" >
                            <i class="fa fa-edit mr-2"></i>
                            ' . trans('app.edit') . '
                        </a>';

                if ($row->status != 'paid' && $row->status != 'canceled'
                    && ($this->editOrderPermission == 'all' || ($this->editOrderPermission == 'added' && $row->added_by == user()->id))
                    && $row->billed_status != 'billed' && $row->delivery_status != "delivered") {
                            $action .= $edit;
                }

                if (($row->status == 'unpaid' || $row->status == 'draft')) {
                    $action .= '<a class="dropdown-item cancel-invoice" href="javascript:;"  data-invoice-id="' . $row->id . '">
                        <i class="fa fa-times mr-2"></i>
                        ' . trans('app.cancel') . '
                    </a>';
                }

                if($row->billed_status != 'billed' )
                {
                    $action .= '<a class="dropdown-item" href="' . route('bills.create') . '?order='.$row->id.'">
                    <i class="far fa-money-bill-alt"></i>
                    ' . trans('purchase::modules.purchaseOrder.convertToBill') . '
                    </a>';
                }

                if (
                    ($this->deleteOrderPermission == 'all'
                    || ($this->deleteOrderPermission == 'added' && $row->added_by == user()->id))
                    && $row->billed_status != 'billed' && $row->delivery_status != "delivered"
                ) {
                        $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-toggle="tooltip"  data-order-id="' . $row->id . '">
                            <i class="fa fa-trash mr-2"></i>
                            ' . trans('app.delete') . '
                        </a>';
                }

                    $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('purchase_order_number', function ($row) {
                return '<div class="media align-items-center">
                            <div class="media-body">
                        <h5 class="mb-0 f-13 text-darkest-grey"><a href="' . route('purchase-order.show', [$row->id]) . '">' . ($row->purchase_order_number) . '</a></h5>
                        </div>
                      </div>';
            })
            ->editColumn('primary_name', function ($row) {
                return '<a href="' . route('vendors.show', [$row->vendor_id]) . '" style="color:black;">' . $row->primary_name . '</a>';
            })
            ->editColumn('purchase_date', function ($row) {
                    return !is_null($row->purchase_date) ? $row->purchase_date->translatedFormat($this->company->date_format) : '----';
            })
            ->editColumn('expected_delivery_date', function ($row) {
                return !is_null($row->expected_delivery_date) ? $row->expected_delivery_date->translatedFormat($this->company->date_format) : '----';
            })
            ->editColumn('total', function ($row) {
                $currencyId = (isset($row->vendor)) ? $row->vendor->currency_id : '';

                return currency_format($row->total, $currencyId);
            })
            ->editColumn('billed_status', function ($row) {
                $status = '';

                if ($row->billed_status == 'unbilled') {
                    $status .= ' <i class="fa fa-circle mr-1 text-red f-10"></i>' . __('purchase::modules.purchaseOrder.' . $row->billed_status);
                }
                else {
                    $status .= ' <i class="fa fa-circle mr-1 text-dark-green f-10"></i>' . __('purchase::modules.purchaseOrder.' . $row->billed_status);
                }

                return $status;

            })
            ->editColumn('id', function ($row) {
                return $row->id;
            })
            ->editColumn('del_status', function ($row) {
                $status = '';

                if ($row->delivery_status == 'delivered') {
                    $status .= __('purchase::modules.purchaseOrder.delivered');
                }

                if ($row->delivery_status == 'delivery_failed') {
                    $status .= __('purchase::modules.purchaseOrder.deliveryFailed');
                }

                if ($row->delivery_status == 'in_transaction') {
                    $status .= __('purchase::modules.purchaseOrder.inTransaction');
                }

                if ($row->delivery_status == 'not_started') {
                    $status .= __('purchase::modules.purchaseOrder.notStarted');
                }

                return $status;

            })
            ->editColumn('delivery_status', function ($row) {
                $status = '';

                if ($this->editOrderPermission == 'all' || ($this->editOrderPermission == 'added' && $row->added_by == user()->id))
                {
                    if ($row->delivery_status == 'delivered') {
                        $status .= '<i class="fa fa-circle mr-1 text-dark-green f-10"></i>' . __('purchase::modules.purchaseOrder.' . $row->delivery_status);
                    }
                    else{
                        $status = '<select class="form-control select-picker change-delivery-status" id="delivery-status" data-order-id="' . $row->id . '">';
                        $status .= '<option ';

                        $status .= ' value="delivered" data-content="<i class=\'fa fa-circle mr-2 text-light-green\'></i> ' . __('purchase::modules.purchaseOrder.delivered') . '">' . __('purchase::modules.purchaseOrder.delivered') . '</option>';
                        $status .= '<option ';

                        if ($row->delivery_status == 'delivery_failed') {
                            $status .= 'selected';
                        }

                        $status .= ' value="delivery_failed" data-content="<i class=\'fa fa-circle mr-2 text-red\'></i> ' . __('purchase::modules.purchaseOrder.deliveryFailed') . '">' . __('purchase::modules.purchaseOrder.deliveryFailed') . '</option>';
                        $status .= '<option ';

                        if ($row->delivery_status == 'in_transaction') {
                            $status .= 'selected';
                        }

                        $status .= ' value="in_transaction" data-content="<i class=\'fa fa-circle mr-2 text-yellow\'></i> ' . __('purchase::modules.purchaseOrder.inTransaction') . '">' . __('purchase::modules.purchaseOrder.inTransaction') . '</option>';
                        $status .= '<option ';

                        if ($row->delivery_status == 'not_started') {
                            $status .= 'selected';
                        }

                        $status .= ' value="not_started" data-content="<i class=\'fa fa-circle mr-2 text-dark\'></i> ' . __('purchase::modules.purchaseOrder.notStarted') . '">' . __('purchase::modules.purchaseOrder.notStarted') . '</option>';

                        $status .= '</select>';
                    }

                }
                else {

                    if ($row->delivery_status == 'delivered') {
                        $class = 'text-light-green';
                        $status = __('purchase::modules.purchaseOrder.delivered');

                    }
                    else if ($row->delivery_status == 'delivery_failed') {
                        $class = 'text-red';
                        $status = __('purchase::modules.purchaseOrder.deliveryFailed');

                    }
                    else if ($row->delivery_status == 'in_transaction') {
                        $class = 'text-yellow';
                        $status = __('purchase::modules.purchaseOrder.inTransaction');

                    }
                    else if ($row->delivery_status == 'not_started') {
                        $class = 'text-dark';
                        $status = __('purchase::modules.purchaseOrder.notStarted');

                    }

                    $status = '<i class="fa fa-circle mr-1 ' . $class . ' f-10"></i> ' . $status;
                }

                return $status;

            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['delivery_status', 'action', 'purchase_order_number', 'billed_status', 'primary_name', 'total','del_status']);
    }

    /**
     * @param BankAccount $model
     * @return BankAccount|\Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseOrder $model)
    {
        $request = $this->request();

        $vendorId = $request->vendorId;

        $model = $model->with('vendor')->select('purchase_vendors.primary_name', 'purchase_orders.purchase_date', 'purchase_orders.billed_status', 'purchase_orders.delivery_status', 'purchase_orders.expected_delivery_date', 'purchase_orders.id', 'purchase_orders.purchase_order_number', 'purchase_orders.vendor_id', 'purchase_orders.total', 'purchase_orders.send_status')
            ->join('purchase_vendors', 'purchase_vendors.id', 'purchase_orders.vendor_id');

        if ($request->searchText != '') {
            $model = $model->where(function ($query) {
                $query->where('purchase_orders.purchase_order_number', 'like', '%' . request('searchText') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('vendor', function ($q) {
                            $q->where('primary_name', 'like', '%' . request('searchText') . '%');
                        });
                    });
            });
        }

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(purchase_orders.`purchase_date`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(purchase_orders.`purchase_date`)'), '<=', $endDate);
        }

        if ($request->billedStatus != 'all' && !is_null($request->billedStatus)) {
            $model = $model->where('purchase_orders.billed_status', '=', $request->billedStatus);
        }

        if ($vendorId != 0 && $vendorId != null && $vendorId != 'all') {
            $model->where('purchase_orders.vendor_id', '=', $vendorId);
        }

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('purchase-order-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["purchase-order-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $(".select-picker").selectpicker();
                }',
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
        $data = [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'exportable' => false, 'title' => __('app.id')],
            __('app.orderNumber') => ['data' => 'purchase_order_number', 'name' => 'purchase_order_number', 'exportable' => true, 'title' => __('app.orderNumber')],
            __('purchase::app.menu.vendor') => ['data' => 'primary_name', 'name' => 'primary_name', 'title' => __('purchase::app.menu.vendor')],
            __('app.purchaseDate') => ['data' => 'purchase_date', 'name' => 'purchase_date', 'title' => __('app.purchaseDate')],
            __('purchase::modules.purchaseOrder.expectedDate') => ['data' => 'expected_delivery_date', 'name' => 'expected_delivery_date', 'title' => __('purchase::modules.purchaseOrder.expectedDate')],
            __('modules.payments.totalAmount') => ['data' => 'total', 'name' => 'total', 'title' => __('modules.payments.totalAmount')],
            __('purchase::modules.purchaseOrder.billedStatus') => ['data' => 'billed_status', 'name' => 'billed_status', 'title' => __('purchase::modules.purchaseOrder.billedStatus')],
            __('purchase::modules.purchaseOrder.deliveryStatus') => ['data' => 'delivery_status', 'name' => 'delivery_status', 'exportable' => false, 'title' => __('purchase::modules.purchaseOrder.deliveryStatus')],
            __('purchase::modules.purchaseOrder.del_status') => ['data' => 'del_status','visible' => false, 'name' => 'del_status', 'exportable' => true, 'title' => __('purchase::modules.purchaseOrder.deliveryStatus')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];

        return $data;
    }

}
