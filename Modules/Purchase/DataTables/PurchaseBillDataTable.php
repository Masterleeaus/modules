<?php

namespace Modules\Purchase\DataTables;

use Carbon\Carbon;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Modules\Purchase\Entities\PurchaseBill;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class PurchaseBillDataTable extends BaseDataTable
{

    private $editBillPermission;
    private $viewBillPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editBillPermission = user()->permission('edit_bill');
        $this->viewBillPermission = user()->permission('view_bill');
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">

                <div class="dropdown">
                    <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                        id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-options-vertical icons"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                if ($this->viewBillPermission == 'all' || $this->viewBillPermission == 'added' && user()->id == $row->added_by) {
                    $action .= '<a href="' . route('bills.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';
                }

                if (($this->editBillPermission == 'all' || $this->editBillPermission == 'added' && user()->id == $row->added_by) && $row->status != 'paid') {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('bills.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                }

                $action .= '<a class="dropdown-item sendButton" href="javascript:;" data-toggle="tooltip"  data-bill-id="' . $row->id . '">
                                <i class="fa fa-paper-plane mr-2"></i>
                                ' . trans('app.send') . '
                            </a>';

                $action .= '<a class="dropdown-item" href="' . route('bills.download', [$row->id]) . '">
                                <i class="fa fa-download mr-2"></i>
                                ' . trans('app.download') . '
                            </a>';

                if ($row->status == 'paid' && $row->credit_note == 0) {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('vendor-credits.creates', [$row->id]) . '">
                    <i class="fa fa-edit mr-2"></i>
                        ' . trans('purchase::app.menu.addCredit') . '
                        </a>';
                }

                $action .= '<a class="dropdown-item" target="_blank" href="' . route('bills.download', [$row->id, 'view' => true]) . '">
                                <i class="fa fa-eye mr-2"></i>
                                ' . trans('app.viewPdf') . '
                            </a>';

                if ($row->status != 'paid' && $row->status != 'draft') {
                    $action .= '<a class="dropdown-item" href="' . route('vendor-payments.create') . '?bill=' . $row->id . '" data-toggle="tooltip"  data-bill-id="' . $row->id . '">
                    <i class="fa fa-plus mr-2"></i>
                    ' . trans('purchase::app.menu.addPayment') . '
                    </a>';
                }

                $action .= '</div>
                        </div>
                    </div>';

                return $action;
            })

            ->addColumn('bill_date', function ($row) {
                return $row->bill_date->timezone($this->company->timezone)->translatedFormat($this->company->date_format);
            })
            ->addColumn('vendor', function ($row) {
                return '<a href="' . route('vendors.show', [$row->purchaseVendor->id]) . '" style="color:black;">' . $row->purchaseVendor->primary_name . '</a>';
            })
            ->addColumn('purchase_bill_number', function ($row) {
                return '<h5 class="mb-0 f-13 text-darkest-grey"><a href="' . route('bills.show', [$row->id]) . '" class="dropdown-item">' . $row->bill_number . '</a></h5>';
            })
            ->addColumn('total', function ($row) {
                $currencyId = (isset($row->vendor->currency)) ? $row->vendor->currency->id : '';

                return '<div>' . __('app.total') . ': ' . currency_format($row->total, $currencyId) . '<p class="my-0"><span class="text-success mt-1">' . __('app.paid') . ':</span> ' . currency_format($row->amountPaid(), $currencyId) . '</p><span class="text-danger">' . __('app.unpaid') . ':</span> ' . currency_format($row->amountDue($row->purchaseVendor->id), $currencyId) . '</div>';
            })
            ->addColumn('status', function ($row) {
                $status = '';

                if ($row->credit_note) {
                    $status .= ' <i class="fa fa-circle mr-1 text-yellow f-10"></i>' . __('app.credit-note');
                }
                else {
                    if ($row->status == 'paid') {
                        $status .= ' <i class="fa fa-circle mr-1 text-dark-green f-10"></i>' . __('app.' . $row->status);
                    }
                    elseif ($row->status == 'partially_paid') {
                        $status .= ' <i class="fa fa-circle mr-1 text-blue f-10"></i>' . __('purchase::modules.purchaseBill.' . $row->status);
                    }
                    elseif ($row->status == 'open') {
                        $status .= ' <i class="fa fa-circle mr-1 text-red f-10"></i>' . __('app.' . $row->status);
                    }
                    else {
                        $status .= ' <i class="fa fa-circle mr-1 text-yellow f-10"></i>' . __('app.' . $row->status);
                    }
                }

                return $status;
            })
            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'vendor', 'status', 'total', 'purchase_bill_number'])
            ->orderColumns(['purchase_bill_number', 'bill_date', 'total', 'status'], '-:column $1');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(): QueryBuilder
    {
        $request = $this->request();
        $model = PurchaseBill::with('purchaseVendor', 'vendor.currency', 'paymentBill', 'purchasePaymentBills');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(purchase_bills.`bill_date`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(purchase_bills.`bill_date`)'), '<=', $endDate);
        }

        if ($request->status != 'all' && !is_null($request->status)) {
            $model = $model->where('purchase_bills.status', '=', $request->status);
        }

        if ($request->vendor_id != 'all' && !is_null($request->vendor_id)) {
            $model = $model->where('purchase_bills.purchase_vendor_id', '=', $request->vendor_id);
        }

        if ($request->searchText != '') {
            $model->where(function ($query) {
                $query->where('purchase_bills.purchase_bill_number', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_bills.bill_date', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_bills.id', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_bills.total', 'like', '%' . request('searchText') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('purchaseVendor', function ($q) {
                            $q->where('primary_name', 'like', '%' . request('searchText') . '%');
                        });
                    });
            });
        }

        if ($this->viewBillPermission == 'added') {
            $model = $model->where('purchase_bills.added_by', user()->id);
        }

        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return parent::setBuilder('purchasebills-table', 1)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["purchasebills-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                   $(".select-picker").selectpicker();
                 }',
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => true, 'title' => '#'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id'), 'visible' => false],
            __('purchase::app.menu.billNumber') => ['data' => 'purchase_bill_number', 'name' => 'purchase_bill_number', 'exportable' => false],
            __('purchase::app.menu.vendor') => ['data' => 'vendor', 'name' => 'purchaseVendor.primary_name', 'exportable' => false],
            __('purchase::app.menu.billDate') => ['data' => 'bill_date', 'name' => 'bill_date', 'exportable' => false],
            __('app.total') => ['data' => 'total', 'name' => 'total', 'exportable' => false],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'exportable' => false],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'PurchaseBills_' . date('YmdHis');
    }

}
