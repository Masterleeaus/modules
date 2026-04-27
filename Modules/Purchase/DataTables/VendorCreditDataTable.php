<?php

namespace Modules\Purchase\DataTables;

use Carbon\Carbon;
use App\Models\User;
use App\DataTables\BaseDataTable;
use Modules\Purchase\Entities\PurchaseVendorCredit;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class VendorCreditDataTable extends BaseDataTable
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
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="select-table-row" id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($row) {

                $action = '<div class="task_view">';

                $action .= '<div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';


                $action .= '<a href="' . route('vendor-credits.show', $row->id) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';


                $action .= '<a class="dropdown-item openRightModal" href="' . route('vendor-credits.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                if ($row->status == 'open') {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('vendor-credits.apply_to_bill', [$row->id]) . '">
                        <i class="fa fa-edit mr-2"></i>
                        ' . trans('purchase::app.applyToBill') . '
                        </a>';
                }

                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-vendor-credit-id ="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';


                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->addColumn('prefix', function ($row) {
                return '<a href="' . route('vendor-credits.show', $row->id) . '" class="openRightModal " style="color:black;">' . $row->vendor_credit_number . '</a>';
            })
            ->editColumn('primary_name', function ($row) {
                return '<a href="' . route('vendors.show', [$row->vendor_id]) . '" style="color:black;">' . $row->primary_name . '</a>';
            })
            ->editColumn('credit_date', function ($row) {
                return '<a href="' . route('vendor-credits.show', $row->id) . '" class="openRightModal " style="color:black;">' . $row->credit_date . '</a>';
            })
            ->editColumn('total', function ($row) {
                $currencyId = $row->currency->id;

                return '<div>' . __('app.total') . ': ' . currency_format($row->total, $currencyId) . '<p class="my-0"><span class="text-warning mt-1">' . __('app.used') . ':</span> ' . currency_format($row->creditAmountUsed(), $currencyId) . ' </p><span class="text-danger">' . __('app.remaining') . ':</span> ' . currency_format($row->creditAmountRemaining(), $currencyId) . '</div>';
            })
            ->editColumn('status', function ($row) {
                $status = ' ';
                if ($row->status == 'closed') {
                    $status .= ' <i class="fa fa-circle mr-1 text-red f-10"></i>' . __('app.' . $row->status);
                }
                else {
                    $status .= ' <i class="fa fa-circle mr-1 text-dark-green f-10"></i>' . __('app.' . $row->status);
                }

                return $status;
            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check', 'prefix', 'primary_name', 'credit_date', 'total', 'status']);
    }


    public function query(PurchaseVendorCredit $model)
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString();
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString();
        }

        $model = $model->with('currency','purchasePaymentBill')->select('purchase_vendor_credits.*', 'purchase_vendors.primary_name')
            ->join('purchase_vendors', 'purchase_vendors.id', '=', 'purchase_vendor_credits.vendor_id');

        // Apply vendor filter if provided
        if (!is_null($request->vendor_id) && $request->vendor_id != 'all') {
            $model->where('vendor_id', $request->vendor_id);
        }

        // Apply date filters
        if ($request->startDate != null && $request->startDate != '') {
            $model = $model->whereDate('purchase_vendor_credits.credit_date', '>=', $startDate);
        }

        if ($request->endDate != null && $request->endDate != '') {
            $model = $model->whereDate('purchase_vendor_credits.credit_date', '<=', $endDate);
        }
        // Apply search text filter
        if ($request->searchText != '') {
            $searchText = $request->searchText;
            $model->where(function ($query) use ($searchText) {
                $query->where('purchase_vendors.primary_name', 'like', request('searchText') . '%')
                    ->orWhere('purchase_vendor_credits.total', 'like', '%' . request('searchText') . '%');
            });
        }

        return $model->groupBy('purchase_vendor_credits.id');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */

    public function html()
    {
        return $this->setBuilder('vendor-credits-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["vendor-credits-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                  //
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
        return [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'title' => __('app.id')],
            __('purchase::modules.vendor.prefix') => ['data' => 'vendor_credit_number', 'name' => 'credit_note_no', 'title' => __('purchase::modules.vendor.prefix')],
            __('purchase::modules.vendor.vendor_name') => ['data' => 'primary_name', 'name' => 'primary_name', 'title' => __('purchase::modules.vendor.vendorName')],
            __('purchase::modules.vendor.creditDate') => ['data' => 'credit_date', 'name' => 'credit_date', 'title' => __('purchase::modules.vendor.creditDate')],
            __('app.total') => ['data' => 'total', 'name' => 'total', 'title' => __('app.total')],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'title' => __('app.status')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

}
