<?php

namespace Modules\Purchase\DataTables;

use App\Models\User;
use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Modules\Purchase\Entities\PurchaseVendorPayment;
use Yajra\DataTables\Html\Column;

class VendorPaymentDataTable extends BaseDataTable
{

    private $editPermission;
    private $deletePermission;
    private $viewPermission;
    protected $firstPayment;

    public function __construct()
    {
        parent::__construct();
        $this->editPermission = user()->permission('edit_vendor_payment');
        $this->deletePermission = user()->permission('delete_vendor_payment');
        $this->viewPermission = user()->permission('view_vendor_payment');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $firstPayment = $this->firstPayment;
        return datatables()
            ->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="select-table-row" id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($row) use ($firstPayment) {
                $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                if ($this->viewPermission == 'all' ||
                ($this->viewPermission == 'added' && $row->added_by == user()->id)) {
                    $action .= '<a class="dropdown-item" href="' . route('vendor-payments.show', [$row->id]) . '">
                                <i class="fa fa-eye mr-2"></i>
                                ' . trans('app.view') . '
                            </a>';
                }

                if (($this->deletePermission == 'all' ||
                ($this->deletePermission == 'added' && $row->added_by == user()->id)) && $row->status != 'complete') {
                        $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-vendor-payment-id="' . $row->id . '">
                                    <i class="fa fa-trash mr-2"></i>
                                    ' . trans('app.delete') . '
                                </a>';
                }

                $action .= '<a class="dropdown-item" href="' . route('vendor-payments.download', [$row->id]) . '">
                                    <i class="fa fa-download mr-2"></i>
                                    ' . trans('app.download') . '
                            </a>';

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('primary_name', function ($row) {
                return '<a href="' . route('vendors.show', [$row->purchase_vendor_id]) . '" style="color:black;">' . $row->primary_name . '</a>';
            })
            ->editColumn('payment_date', function ($row) {
                return $row->payment_date->format($this->company->date_format);
            })
            ->editColumn('received_payment', function ($row) {
                $currencyId = (isset($row->vendor->currency)) ? $row->vendor->currency->id : '';

                return currency_format($row->received_payment, $currencyId);
            })
            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['primary_name', 'payment_date', 'received_payment', 'action', 'check']);
    }

    /**
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseVendorPayment $model)
    {
        $request = $this->request();
        $this->firstPayment = PurchaseVendorPayment::orderBy('id', 'desc')->first();

        $model = PurchaseVendorPayment::with('vendor', 'vendor.currency')->select('purchase_vendor_payments.id', 'purchase_vendor_payments.purchase_vendor_id', 'purchase_vendor_payments.bank_account_id', 'purchase_vendor_payments.payment_date', 'purchase_vendor_payments.received_payment', 'purchase_vendors.primary_name', 'purchase_vendor_payments.status', 'bank_accounts.bank_name')
            ->leftjoin('purchase_vendors', 'purchase_vendors.id', 'purchase_vendor_payments.purchase_vendor_id')
            ->leftjoin('bank_accounts', 'bank_accounts.id', 'purchase_vendor_payments.bank_account_id');

        $startDate = null;
        $endDate = null;

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString();
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString();
        }

        if ($request->searchText != '') {
            $model = $model->where(function ($query) {
                $query->where('purchase_vendors.primary_name', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_vendor_payments.received_payment', 'like', '%' . request('searchText') . '%');
            });
        }

        if ($request->startDate != null && $request->startDate != '') {
            $model = $model->whereDate('purchase_vendors.created_at', '>=', $startDate);
        }

        if ($request->endDate != null && $request->endDate != '') {
            $model = $model->whereDate('purchase_vendors.created_at', '<=', $endDate);
        }

        if ($this->viewPermission == 'added') {
            $model = $model->where('purchase_vendor_payments.added_by', user()->id);
        }

        if ($request->vendor_id != 'all' && !is_null($request->vendor_id)) {
            $model = $model->where('purchase_vendor_payments.purchase_vendor_id', '=', $request->vendor_id);
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
        return parent::setBuilder('vendor-payments-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["vendor-payments-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                   //
                   $(".select-picker").selectpicker();
                 }',
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => true],
            __('app.name') => ['data' => 'primary_name', 'name' => 'primary_name', 'title' => __('purchase::app.purchaseOrder.vendorName')],
            __('purchase::modules.vendorPayment.paymentDate') => ['data' => 'payment_date', 'name' => 'payment_date', 'title' => __('modules.payments.paidOn')],
            __('purchase::modules.vendorPayment.paymentMade') => ['data' => 'received_payment', 'name' => 'received_payment', 'title' => __('modules.invoices.amount')],

            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-right pr-20')
        ];
    }

}
