<?php

namespace Modules\Purchase\DataTables;

use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseStockAdjustment;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;

class InventoryValuationSummaryDatatable extends BaseDataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('item_name', fn($row) => $row->product ? $row->product->name : '----')
            ->addColumn('sku', fn($row) => $row->product && !is_null($row->product->sku) ? $row->product->sku : '----')
            ->editColumn('net_quantity', fn($row) => $row->net_quantity ?? '----')
            ->editColumn('changed_value', fn($row) => currency_format($row->changed_value, company()->currency->id))
            ->editColumn('id', fn($row) => $row->id)
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['changed_value',]);
    }

    public function query(PurchaseStockAdjustment $model)
    {
        $request = $this->request();

        $model = $model->with('product', 'product.tax');

        if ($request->searchText != '') {
            $model = $model->Where(function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . request('searchText') . '%');
                });
            });
        }

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(purchase_stock_adjustments.`created_at`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(purchase_stock_adjustments.`created_at`)'), '<=', $endDate);
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
        return $this->setBuilder('inventory-summary-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["inventory-summary-table"].buttons().container()
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
            __('app.menu.itemName') => ['data' => 'item_name', 'name' => 'item_name', 'title' => __('app.menu.itemName')],
            __('purchase::app.sku') => ['data' => 'sku', 'name' => 'sku', 'title' => __('purchase::app.sku')],
            __('purchase::modules.product.stockOnHand') => ['data' => 'net_quantity', 'name' => 'net_quantity', 'title' => __('purchase::modules.product.stockOnHand')],
            __('purchase::modules.inventory.inventoryAssetValue') => ['data' => 'changed_value', 'name' => 'changed_value', 'title' => __('purchase::modules.inventory.inventoryAssetValue')],
        ];

        return $data;
    }

}
