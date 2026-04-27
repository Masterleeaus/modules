<?php

namespace Modules\Accountings\DataTables;

use App\DataTables\BaseDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\Accountings\Entities\Journal;

class JournalDataTable extends BaseDataTable
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
            ->editColumn('no_journal', function ($row) {
                return ('#' . $row->no_journal);
            })
            ->editColumn('journal_date', function ($row) {
                return ($row->journal_date);
            })
            ->editColumn('typejournal_id', function ($row) {
                return ($row->typejournal_id)  ? $row->type->type_journal : '--';
            })
            ->editColumn('reff_journal', function ($row) {
                return ($row->reff_journal);
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">

                <div class="dropdown">
                    <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                        id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-options-vertical icons"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= '<a href="' . route('journal.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                if ($this->editUnitPermission == 'all') {
                    $action .= 
                    '<a class="dropdown-item" href="' . route('journal.edit', $row->id) . '" >
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

                $action .= '<a class="dropdown-item" href="' . route('journal.download', [$row->id]) . '">
                <i class="fa fa-download mr-2"></i>
                ' . trans('app.download') . '
                </a>';


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
    public function query(Journal $model)
    {
        $request = $this->request();

        $units = $model->select('*');

        if (!is_null($request->type) && $request->type != 'all') {
            $units->where('typejournal_id', $request->type);
        }
        if ($request->searchText != '') {
            $units = $units->where(function ($query) {
                $query->where('acc_journalh.no_journal', 'like', '%' . request('searchText') . '%')
                    ->orWhere('acc_journalh.reff_journal', 'like', '%' . request('searchText') . '%')
                    ->orWhere(function ($query) {
                        $query->whereHas('type', function ($q) {
                            $q->where('type_journal', 'like', '%' . request('searchText') . '%');
                        });
                    });
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
        return $this->setBuilder('Journal-table')
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["Journal-table"].buttons().container()
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
            '#' => ['data' => 'id', 'orderable' => false, 'searchable' => false, 'visible' => false],
            __('accountings::app.menu.journalNo') => ['data' => 'no_journal', 'name' => 'no_journal', 'exportable' => true, 'title' => __('accountings::app.menu.journalNo')],
            __('accountings::app.menu.journalDate') => ['data' => 'journal_date', 'name' => 'journal_date', 'exportable' => true, 'title' => __('accountings::app.menu.journalDate')],
            __('accountings::app.menu.journalType') => ['data' => 'typejournal_id', 'name' => 'typejournal_id', 'exportable' => true, 'title' => __('accountings::app.menu.journalType')],
            __('accountings::app.menu.journalReff') => ['data' => 'reff_journal', 'name' => 'reff_journal', 'exportable' => true, 'title' => __('accountings::app.menu.journalReff')],
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
