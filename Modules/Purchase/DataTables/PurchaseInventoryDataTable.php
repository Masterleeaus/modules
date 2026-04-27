<?php

namespace Modules\Purchase\DataTables;

use Carbon\Carbon;
use App\DataTables\BaseDataTable;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\Purchase\Entities\PurchaseInventory;

class PurchaseInventoryDataTable extends BaseDataTable
{

    private $deleteInventoryPermission;
    private $editInventoryPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editInventoryPermission = user()->permission('edit_inventory');
        $this->deleteInventoryPermission = user()->permission('delete_inventory');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);

        $datatables->editColumn('id', function ($row) {
            return $row->id;
        });

        $datatables->editColumn('date', function ($row) {
            return Carbon::parse($row->date)->translatedFormat($this->company->date_format);
        });

        $datatables->editColumn('reason_id', function ($row) {
            return ($row->reason) ? $row->reason->name : '--';
        });

        $datatables->editColumn('type', function ($row) {
            return strip_tags($row->type);
        });

        $datatables->editColumn('status', function ($row) {

            if ($this->editInventoryPermission == 'all' || ($this->editInventoryPermission == 'added' && user()->id == $row->added_by)) {

                $status = '<select class="form-control select-picker change-inventory-status" id="change-status" data-id="' . $row->id . '">';
                $status .= '<option ';

                if ($row->status == 'active') {
                    $status .= 'selected';
                }

                $status .= ' value="active" data-content="<i class=\'fa fa-circle mr-2 text-light-green\'></i> ' . __('app.active') . '">' . __('app.active') . '</option>';
                $status .= '<option ';

                if ($row->status == 'inactive') {
                    $status .= 'selected';
                }

                $status .= ' value="inactive" data-content="<i class=\'fa fa-circle mr-2 text-red\'></i> ' . __('app.inactive') . '">' . __('app.inactive') . '</option>';

                $status .= '</select>';
            } else {
                if ($row->status == 'active') {
                    $class = 'text-light-green';
                    $status = __('app.active');
                } else {
                    $class = 'text-red';
                    $status = __('app.inactive');
                }

                $status = '<i class="fa fa-circle mr-1 ' . $class . ' f-10"></i> ' . $status;
            }

            return $status;
        });

        $datatables->editColumn('status', function ($row) {
            return $row->status;
        });

        $datatables->addColumn('action', function ($row) {

            $action = '<div class="task_view">
                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

            if ($this->editInventoryPermission == 'all' || ($this->deleteInventoryPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a href="' . route('purchase-inventory.show', [$row->id]) . '" class="dropdown-item" data-inventory-id="' . $row->id . '"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';
            }

            if ($this->deleteInventoryPermission == 'all' || ($this->deleteInventoryPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-inventory-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';
            }

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });

        $datatables->addIndexColumn();
        $datatables->smart(false);
        $datatables->setRowId(fn($row) => 'row-' . $row->id);

        $datatables->rawColumns(['products', 'date', 'reason', 'status', 'default_image', 'action']);

        return $datatables;
    }

    /**
     * @param PurchaseInventory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseInventory $model)
    {
        $request = $this->request();

        $model = $model->select('purchase_inventory_adjustment.*')
            ->with('stocks', 'files', 'reason', 'stocks.product','stocks.product.tax');

        if ($request->status != 'all' && !is_null($request->status)) {
            $model = $model->where('purchase_inventory_adjustment.status', '=', $request->status);
        }

        if ($request->searchText != '') {

            $model->join('purchase_stock_adjustments', 'purchase_inventory_adjustment.id', '=', 'purchase_stock_adjustments.inventory_id')
                ->join('products', 'purchase_stock_adjustments.product_id', '=', 'products.id');

            $model->where(function ($query) {
                $query->where('purchase_stock_adjustments.type', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_stock_adjustments.net_quantity', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_stock_adjustments.reference_number', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_stock_adjustments.description', 'like', '%' . request('searchText') . '%')
                    ->orWhere('products.name', 'like', '%' . request('searchText') . '%');
            });
        }

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(purchase_inventory_adjustment.`created_at`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(purchase_inventory_adjustment.`created_at`)'), '<=', $endDate);
        }

        if ($request->inventoryStatus != 'all' && !is_null($request->inventoryStatus)) {
            $model = $model->where('purchase_inventory_adjustment.status', '=', $request->inventoryStatus);
        }

        return $model->groupBy('purchase_inventory_adjustment.id');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('inventory-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["inventory-table"].buttons().container()
                    .appendTo( "#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    });
                    $(".change-inventory-status").selectpicker();
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
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id'), 'visible' => showId()],
            __('app.date') => ['data' => 'date', 'name' => 'date', 'title' => __('app.date')],
            __('purchase::modules.product.reason') => ['data' => 'reason_id', 'name' => 'reason_id', 'title' => __('purchase::modules.product.reason')],
            __('purchase::modules.product.modeOfAdjustment') => ['data' => 'type', 'name' => 'type', 'title' => __('purchase::modules.product.modeOfAdjustment')],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'exportable' => false, 'title' => __('app.status')],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'searchable' => false, 'visible' => false, 'title' => __('app.status')],
        ];

        $action = [
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];

        return array_merge($data, $action);
    }
}
