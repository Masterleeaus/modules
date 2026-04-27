<?php

namespace Modules\Accountings\DataTables;

use App\DataTables\BaseDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\Accountings\Entities\Accounting;

class AccountingDataTable extends BaseDataTable
{
    private $editUnitPermission;
    private $deleteUnitPermission;
    private $viewUnitPermission;
    public $arr = [];

    public function __construct()
    {
        parent::__construct();
        $this->editUnitPermission = user()->permission('edit_acc');
        $this->deleteUnitPermission = user()->permission('delete_acc');
        $this->viewUnitPermission = user()->permission('view_acc');

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
            ->editColumn('bs_id', function ($row) {
                return ($row->bs_id)  ? $row->bs->bs_name : '--';
            })
            ->editColumn('pnl_id', function ($row) {
                return ($row->pnl_id)  ? $row->pnl->pnl_name : '--';
            })
            ->editColumn('coa', function ($row) {
                return ($row->coa);
            })
            ->editColumn('coa_desc', function ($row) {
                return ($row->coa_desc);
            })
            ->addColumn('action', function ($row) {

                $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= '<a href="' . route('accountings.show', [$row->id]) . '" class="dropdown-item openRightModal"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                if ($this->editUnitPermission == 'all') {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('accountings.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                }


                if ($this->deleteUnitPermission == 'all') {
                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-Unit-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';
                }

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->smart(false)
            ->setRowId(function ($row) {
                return 'row-' . $row->id;
            })
            ->rawColumns(['check', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Unit $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Accounting $model)
    {
        $request = $this->request();

        $units = $model->select('*');

        if (!is_null($request->bs) && $request->bs != 'all') {
            $units->where('bs_id', $request->bs);
        }
        if (!is_null($request->pnl) && $request->pnl != 'all') {
            $units->where('pnl_id', $request->pnl);
        }
        if ($request->searchText != '') {
            $units = $units->where(function ($query) {
                $query->where('acc_coa.coa_desc', 'like', '%' . request('searchText') . '%')
                    ->orWhere('acc_coa.coa', 'like', '%' . request('searchText') . '%');
            });
        }
        return $units;

    }

    public function child($child)
    {
        foreach ($child as $item) {
            $this->arr[] = $item->id;

            if ($item->childs) {
                $this->child($item->childs);
            }
        }
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('Unit-table')
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["Unit-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    });
                    $(".statusChange").selectpicker();
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
            '#' => ['data' => 'id', 'orderable' => false, 'searchable' => false, 'visible' => false],
            __('accountings::app.menu.bs') => ['data' => 'bs_id', 'name' => 'bs_id', 'exportable' => true, 'title' => __('accountings::app.menu.bs')],
            __('accountings::app.menu.pnl') => ['data' => 'pnl_id', 'name' => 'pnl_id', 'exportable' => true, 'title' => __('accountings::app.menu.pnl')],
            __('accountings::app.menu.coa') => ['data' => 'coa', 'name' => 'coa', 'exportable' => true, 'title' => __('accountings::app.menu.coa')],
            __('accountings::app.menu.coaDesc') => ['data' => 'coa_desc', 'name' => 'coa_desc', 'exportable' => true, 'title' => __('accountings::app.menu.coaDesc')],
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
