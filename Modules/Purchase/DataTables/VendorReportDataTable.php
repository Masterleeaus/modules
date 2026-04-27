<?php

namespace Modules\Purchase\DataTables;

use Carbon\Carbon;
use App\DataTables\BaseDataTable;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Entities\PurchaseVendorNote;

class VendorReportDataTable extends BaseDataTable
{

    private $editClientNotePermission;
    private $deleteClientNotePermission;

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
            ->addColumn('vendor_name', function ($row) {
                return $row->primary_name;
            })
            ->addColumn('billed_amount', function ($row) {
                return currency_format($row->billed_amount, $row->currency_id);
            })
            ->addColumn('amount_paid', function ($row) {
                return currency_format($row->amount_paid, $row->currency_id);
            })
            ->addColumn('closing_balance', function ($row) {
                return currency_format($row->opening_balance, $row->currency_id);

            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['billed_amount']);
    }

    /**
     * @param ClientNote $model
     * @return ClientNote|\Illuminate\Database\Eloquent\Builder
     */

    public function query(PurchaseVendor $model)
    {
        $request = $this->request();

        $vendor = $request->vendor;

        $model = $model
            ->select('purchase_vendors.primary_name', 'purchase_vendors.id', 'purchase_vendors.opening_balance', 'purchase_vendors.currency_id',
            DB::raw('( select sum(billedAmount.total) from purchase_bills as billedAmount where billedAmount.purchase_vendor_id = purchase_vendors.id) as billed_amount'),
            DB::raw("( select sum(purchase_bills.total) from purchase_bills where purchase_bills.purchase_vendor_id = purchase_vendors.id and purchase_bills.status = 'paid') as amount_paid"),
        )
            ->join('purchase_bills', 'purchase_bills.purchase_vendor_id', 'purchase_vendors.id');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString();
            if (!is_null($startDate)) {

                $model = $model->where(DB::raw('DATE(purchase_vendors.`created_at`)'), '>=', $startDate);
            }
        }
        
        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString();
            
            if (!is_null($endDate)) {
                $model = $model->where(function ($query) use ($endDate) {
                    $query->where(DB::raw('DATE(purchase_vendors.`created_at`)'), '<=', $endDate);
                });
            }
        }

        if (!is_null($vendor) && $vendor !== 'all') {
            $model = $model->where('purchase_vendors.id', $vendor);
        }

        $model = $model->groupBy('purchase_vendors.id');

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */

    public function html()
    {
        return $this->setBuilder('vendor-report-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["vendor-report-table"].buttons().container()
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
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'title' => __('app.id')],
            __('purchase::app.vendor').' '.__('app.name') => ['data' => 'vendor_name', 'name' => 'vendor_name', 'title' => __('purchase::app.vendor').' '.__('app.name')],
            __('purchase::modules.vendor.billedAmount') => ['data' => 'billed_amount', 'name' => 'billed_amount', 'title' => __('purchase::modules.vendor.billedAmount')],
            __('modules.invoices.amountPaid') => ['data' => 'amount_paid', 'name' => 'amount_paid', 'title' => __('modules.invoices.amountPaid')],
            __('purchase::modules.vendor.closingBalance') => ['data' => 'closing_balance', 'name' => 'closing_balance', 'title' => __('purchase::modules.vendor.closingBalance')],
        ];
    }

}
