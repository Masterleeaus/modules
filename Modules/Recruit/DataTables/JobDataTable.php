<?php

namespace Modules\Recruit\DataTables;

use App\DataTables\BaseDataTable;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Html\Column;
use Modules\Recruit\Entities\RecruitJob;

class JobDataTable extends BaseDataTable
{

    private $editJobPermission;
    private $deleteJobPermission;
    private $viewJobPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editJobPermission = user()->permission('edit_job');
        $this->deleteJobPermission = user()->permission('delete_job');
        $this->viewJobPermission = user()->permission('view_job');
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
            ->editColumn('title', function ($row) {
                return '<a href="' . route('jobs.show', [$row->id]) . '" class=" text-darkest-grey" >' . ucwords($row->title) . '</a>';
            })
            ->editColumn('start_date', function ($row) {
                return $row->start_date->format($this->company->date_format);
            })
            ->editColumn('end_date', function ($row) {
                if ($row->end_date != null) {
                    return $row->end_date->format($this->company->date_format);
                }
                else {
                    return __('recruit::modules.job.noEndDate');
                }
            })
            ->editColumn('status', function ($row) {
                if ($this->editJobPermission != 'none'
                    && (
                        $this->editJobPermission == 'all'
                        || ($this->editJobPermission == 'added' && $row->added_by == user()->id)
                        || ($this->editJobPermission == 'owned' && $row->recruiter_id == user()->id)
                        || ($this->editJobPermission == 'both' && ($row->user_id == user()->id || $row->recruiter_id == user()->id))
                    )
                ) {
                    $status = '<select class="form-control select-picker change-job-status" data-job-id="' . $row->id . '">';
                    $status .= '<option ';

                    if ($row->status == 'open') {
                        $status .= 'selected';
                    }

                    $status .= ' value="open" data-content="<i class=\'fa fa-circle mr-2 text-light-green\'></i> ' . __('app.open') . '">' . __('app.open') . '</option>';
                    $status .= '<option ';

                    if ($row->status == 'closed') {
                        $status .= 'selected';
                    }

                    $status .= ' value="closed" data-content="<i class=\'fa fa-circle mr-2 text-red\'></i> ' . __('app.closed') . '"' . __('app.closed') . '</option>';

                    $status .= '</select>';
                }
                else {
                    if ($row->status == 'open') {
                        $class = 'text-light-green';
                        $status = __('app.open');
                    }
                    else {
                        $class = 'text-red';
                        $status = __('app.closed');
                    }

                    $status = '<i class="fa fa-circle mr-1 ' . $class . ' f-10"></i> ' . $status;
                }

                return $status;
            })
            ->editColumn('recruiter_id', function($row) {
                if (!is_null($row->recruiter_id)) {
                    return view('components.employee', [
                        'user' => $row->recruiter
                    ]);
                }

                return '--';
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"  data-boundary="window" 
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                if ($this->viewJobPermission == 'all' ||
                    ($this->viewJobPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->viewJobPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->viewJobPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a href="' . route('jobs.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';
                }

                if ($this->editJobPermission == 'all' ||
                    ($this->editJobPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editJobPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editJobPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('jobs.edit', [$row->id]) . '">
                                    <i class="fa fa-edit mr-2"></i>
                                    ' . trans('app.edit') . '
                                </a>';
                }

                if ($this->deleteJobPermission == 'all' ||
                    ($this->deleteJobPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->deleteJobPermission == 'owned' && $row->recruiter_id == user()->id) ||
                    ($this->deleteJobPermission == 'both' && $row->recruiter_id == user()->id ||
                        $row->added_by == user()->id)) {
                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-job-id="' . $row->id . '">
                                    <i class="fa fa-trash mr-2"></i>
                                    ' . trans('app.delete') . '
                                </a>';
                }

                if ($this->editJobPermission == 'all' ||
                    ($this->editJobPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editJobPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editJobPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('jobs.create') . '?duplicate_job=' . $row->id . '">
                                <i class="fa fa-clone"></i>
                                ' . trans('app.duplicate') . '
                            </a>';
                }

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'title','recruiter_id', 'start_date', 'end_date', 'status', 'check'])
            ->removeColumn('updated_at')
            ->removeColumn('created_at');
    }

    /**
     * Get query source of dataTable.
     *
     * @param  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RecruitJob $jobs)
    {
        $startDate = null;
        $endDate = null;

        $jobs = $jobs->with('recruiter', 'address')->select('recruit_jobs.id', 'recruit_jobs.title', 'recruit_jobs.start_date', 'recruit_jobs.end_date', 'recruit_jobs.status', 'recruit_jobs.recruiter_id', 'recruit_jobs.remote_job');

        if ($this->request()->startDate !== null && $this->request()->startDate != 'null' && $this->request()->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $this->request()->startDate)->toDateString();
        }

        if ($this->request()->endDate !== null && $this->request()->endDate != 'null' && $this->request()->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $this->request()->endDate)->toDateString();
        }

        if ($this->viewJobPermission == 'added') {
            $jobs->where(function ($query) {
                return $query->where('added_by', user()->id);
            });
        }

        if ($this->viewJobPermission == 'owned') {
            $jobs->where(function ($query) {
                return $query->where('recruiter_id', user()->id);
            });
        }

        if ($this->viewJobPermission == 'both') {
            $jobs->where(function ($query) {
                return $query->where('recruiter_id', user()->id)
                    ->orWhere('added_by', user()->id);
            });
        }

        if ($startDate !== null && $endDate !== null) {
            $jobs->where(function ($q) use ($startDate, $endDate) {
                if (request()->date_filter_on == 'due_date') {
                    $q->whereBetween(DB::raw('DATE(recruit_jobs.`end_date`)'), [$startDate, $endDate]);
                }
                elseif (request()->date_filter_on == 'start_date') {
                    $q->whereBetween(DB::raw('DATE(recruit_jobs.`start_date`)'), [$startDate, $endDate]);
                }
            });
        }

        if ($this->request()->searchText != '') {
            $jobs = $jobs->where(function ($query) {
                $query->where('recruit_jobs.title', 'like', '%' . request('searchText') . '%');
            });
        }

        if($this->request()->recruiter != 'all' && $this->request()->recruiter != null && $this->request()->recruiter != ''){
            $jobs->where('recruit_jobs.recruiter_id', $this->request()->recruiter);
        }

        if ($this->request()->status != 'all' && $this->request()->status != '') {
            $jobs = $jobs->where('recruit_jobs.status', $this->request()->status);
        }

        if ($this->request()->location != 'all' && $this->request()->location != null && $this->request()->location != '') {
            $jobs->whereHas('address', function ($q) {
                $q->where('company_address_id', $this->request()->location);
            });
        }

        if ($this->request()->workType != 'all' && $this->request()->workType != null && $this->request()->workType != '') {
            $jobs->where('recruit_jobs.remote_job', $this->request()->workType);
        }

        if ($this->request()->department_id != 'all' && $this->request()->department_id != '') {
            $jobs = $jobs->where('recruit_jobs.department_id', $this->request()->department_id);
        }

        return $jobs;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return parent::setBuilder('job-table')
            ->parameters([
                'order' => [3, 'desc'],
                'initComplete' => 'function () {
                    window.LaravelDataTables["job-table"].buttons().container()
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
            __('recruit::modules.job.jobTitle') => ['data' => 'title', 'name' => 'title', 'title' => __('recruit::modules.job.jobTitle')],
            __('recruit::app.job.recruiter') => ['data' => 'recruiter_id','name' => 'recruit_jobs.recruiter_id', 'title' => __('recruit::app.job.recruiter')],
            __('recruit::modules.job.startDate') => ['data' => 'start_date', 'name' => 'start_date', 'title' => __('recruit::modules.job.startDate')],
            __('recruit::modules.job.endDate') => ['data' => 'end_date', 'name' => 'end_date', 'title' => __('recruit::modules.job.endDate')],
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
