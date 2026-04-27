<?php

namespace Modules\Recruit\DataTables;

use App\DataTables\BaseDataTable;
use App\Models\User;
use Modules\Recruit\Entities\RecruitSkill;
use Yajra\DataTables\Html\Column;

class SkillDataTable extends BaseDataTable
{
    private $Permission;

    public function __construct()
    {
        parent::__construct();
        $this->Permission = user()->permission('edit_offer_letter');
    }

    /**
     * Build DataTable class.
     *
     * @param  mixed  $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="select-table-row" id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"  data-boundary="window" 
                            id="dropdownMenuLink-'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-'.$row->id.'" tabindex="0">';

                if ($this->Permission == 'all') {
                    $action .= '<a class="dropdown-item openRightModal" href="'.route('job-skills.edit', [$row->id]).'">
                                <i class="fa fa-edit mr-2"></i>
                                '.trans('app.edit').'
                            </a>';

                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-user-id="'.$row->id.'">
                                <i class="fa fa-trash mr-2"></i>
                                '.trans('app.delete').'
                            </a>';
                }

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('name', function ($row) {
                return $row->name;
            })
            ->addIndexColumn()
            ->setRowId(function ($row) {
                return 'row-'.$row->id;
            })
            ->rawColumns(['name', 'action', 'check']);
    }

    /**
     * @param  User  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RecruitSkill $model)
    {
        $model = $model->select('id', 'name');

        if ($this->request()->searchText != '') {
            $model = $model->where(function ($query) {
                $query->where('recruit_skills.name', 'like', '%'.request('searchText').'%');
            });
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
        return parent::setBuilder('skills-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["skills-table"].buttons().container()
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
                'searchable' => false,
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false],
            __('app.name') => ['data' => 'name', 'name' => 'name', 'exportable' => false, 'width' => '40%', 'title' => __('app.name')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-right pr-20'),
        ];
    }
}
