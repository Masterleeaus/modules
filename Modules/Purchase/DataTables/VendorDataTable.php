<?php

namespace Modules\Purchase\DataTables;

use App\Models\User;
use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Modules\Purchase\Entities\PurchaseVendor;
use Yajra\DataTables\Html\Column;

class VendorDataTable extends BaseDataTable
{

    private $editPermission;
    private $deletePermission;
    private $viewPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editPermission = user()->permission('edit_vendor');
        $this->deletePermission = user()->permission('delete_vendor');
        $this->viewPermission = user()->permission('view_vendor');
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
                $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                if ($this->viewPermission == 'all' ||
                ($this->viewPermission == 'added' && $row->added_by == user()->id)) {
                    $action .= '<a class="dropdown-item" href="' . route('vendors.show', [$row->id]) . '">
                                <i class="fa fa-eye mr-2"></i>
                                ' . trans('app.view') . '
                            </a>';
                }

                if ($this->editPermission == 'all' ||
                ($this->editPermission == 'added' && $row->added_by == user()->id)) {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('vendors.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                }

                if ($this->deletePermission == 'all' ||
                ($this->deletePermission == 'added' && $row->added_by == user()->id)) {
                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-vendor-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';
                }

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('primary_name', function ($row) {
                return '<a href="' . route('vendors.show', [$row->id]) . '" style="color:black;">' . $row->primary_name . '</a>';
            })
            ->editColumn('company_name', function ($row) {
                return $row->company_name ?? '--';
            })
            ->editColumn('email', function ($row) {
                return $row->email ?? '--';
            })
            ->editColumn('phone', function ($row) {
                return $row->phone ?? '--';
            })
            ->addColumn('category_name', function ($row) {
                return !is_null($row->category_id) ? $row->category->category_name : '--';
            })
            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['primary_name', 'company_name', 'email', 'phone', 'action', 'check']);
    }

    /**
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseVendor $model)
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

        $model = $model->select('id','category_id', 'primary_name', 'company_name', 'email', 'phone', 'added_by');

        if ($request->searchText != '') {
            $model = $model->where(function ($query) {
                $query->where('purchase_vendors.primary_name', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_vendors.company_name', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_vendors.email', 'like', '%' . request('searchText') . '%')
                    ->orWhere('purchase_vendors.phone', 'like', '%' . request('searchText') . '%');
            });
        }

        if ($request->startDate != null && $request->startDate != '') {
            $model = $model->whereDate('purchase_vendors.created_at', '>=', $startDate);
        }

        if ($request->endDate != null && $request->endDate != '') {
            $model = $model->whereDate('purchase_vendors.created_at', '<=', $endDate);
        }

        if (!is_null($request->category_id) && $request->category_id != 'all') {
            $model = $model->where('purchase_vendors.category_id', $request->category_id);
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
        return parent::setBuilder('vendors-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["vendors-table"].buttons().container()
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
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false],
            __('app.name') => ['data' => 'primary_name', 'name' => 'primary_name'],
            __('purchase::modules.vendor.companyName') => ['data' => 'company_name', 'name' => 'company_name'],
             __('app.category') => ['data' => 'category_name', 'name' => 'category_name', 'title' => __('app.category')],
            __('app.email') => ['data' => 'email', 'name' => 'email'],
            __('app.phone') => ['data' => 'phone', 'name' => 'phone'],
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
