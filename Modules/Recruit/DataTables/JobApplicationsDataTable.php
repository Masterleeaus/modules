<?php

namespace Modules\Recruit\DataTables;

use Illuminate\Support\Carbon;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitApplicationStatus;

class JobApplicationsDataTable extends BaseDataTable
{

    private $editJobApplicationPermission;
    private $deleteJobApplicationPermission;
    private $viewJobApplicationPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editJobApplicationPermission = user()->permission('edit_job_application');
        $this->deleteJobApplicationPermission = user()->permission('delete_job_application');
        $this->viewJobApplicationPermission = user()->permission('view_job_application');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $jobBoardColumns = RecruitApplicationStatus::orderBy('position', 'asc')->get();

        return datatables()
            ->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="select-table-row" id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->editColumn('full_name', function ($row) {
                return '<div class="media align-items-center">
                <div class="media-body">
                <h5 class="mb-0 f-13 text-darkest-grey"><a href="' . route('job-applications.show', [$row->id]) . '" class="openRightModal">' . $row->full_name . '</a></h5>
                </div>
                </div>';
            })
            ->addColumn('recruit_job_id', function ($row) {
                return '<a href="' . route('jobs.show', [$row->recruit_job_id]) . '" class="text-darkest-grey" >' . $row->title . '</a>';
            })
            ->editColumn('location', function ($row) {
                return $row->location;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format($this->company->date_format);
            })
            ->editColumn('status', function ($row) use ($jobBoardColumns) {
                if ($this->editJobApplicationPermission == 'all' ||
                    ($this->editJobApplicationPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editJobApplicationPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editJobApplicationPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $status = '<select data-size="4" class="form-control select-picker change-status" data-status-id="' . $row->id . '">';

                    foreach ($jobBoardColumns as $item) {
                        $status .= '<option ';

                        if ($item->id == $row->recruit_application_status_id) {
                            $status .= 'selected';
                        }

                        $status .= '  data-content="<i class=\'fa fa-circle mr-2\' style=\'color: ' . $item->color . '\\'></i> ' . $item->status . '" value="' . $item->id . '">' . $item->status . '</option>';
                    }

                    $status .= '</select>';
                }
                else {
                    return ' <i class="fa fa-circle mr-1 text-light-green f-10" style=\'color: ' . $row->color . '\\'></i>' . $row->status;
                }

                return $status;
            })
            ->addColumn('name', function ($row) {
                return $row->full_name;
            })
            ->addColumn('phone', function ($row) {
                return $row->phone;
            })
            ->addColumn('email', function ($row) {
                return $row->email;
            })
            ->addColumn('current_ctc', function ($row) {
                return $row->current_ctc ?? '--';
            })
            ->addColumn('expected_ctc', function ($row) {
                return $row->expected_ctc ?? '--';
            })
            ->addColumn('total_experience', function ($row) {
                return $row->total_experience ?? '--';
            })
            ->addColumn('source', function ($row) {
                return $row->application_source ?? '--';
            })
            ->addColumn('gender', function ($row) {
                return $row->gender ?? '--';
            })
            ->addColumn('job_name', function ($row) {
                return $row->title;
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"  data-boundary="window" 
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                if ($this->viewJobApplicationPermission == 'all' ||
                    ($this->viewJobApplicationPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->viewJobApplicationPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->viewJobApplicationPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a href="' . route('job-applications.show', [$row->id]) . '" class="dropdown-item openRightModal"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';
                }

                if ($this->editJobApplicationPermission == 'all' ||
                    ($this->editJobApplicationPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editJobApplicationPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editJobApplicationPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('job-applications.edit', [$row->id]) . '">
                                    <i class="fa fa-edit mr-2"></i>
                                    ' . trans('app.edit') . '
                                </a>';
                }

                if ($this->editJobApplicationPermission == 'all' ||
                    ($this->editJobApplicationPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editJobApplicationPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editJobApplicationPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a class="dropdown-item archive-job" href="javascript:;" data-application-id="' . $row->id . '">
                                    <i class="fa fa-archive mr-2"></i>
                                    ' . trans('recruit::modules.jobApplication.archiveApplication') . '
                                </a>';
                }

                if ($this->editJobApplicationPermission == 'all' ||
                    ($this->editJobApplicationPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editJobApplicationPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editJobApplicationPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a class="dropdown-item follow-up" href="javascript:;" data-datatable="true" data-application-id="' . $row->id . '">
                    <i class="fa fa-thumbs-up mr-2"></i>
                    ' . trans('modules.lead.addFollowUp') . '
                    </a>';
                }

                if ($this->deleteJobApplicationPermission == 'all' ||
                    ($this->deleteJobApplicationPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->deleteJobApplicationPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->deleteJobApplicationPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-application-id="' . $row->id . '">
                                    <i class="fa fa-trash mr-2"></i>
                                    ' . trans('app.delete') . '
                                </a>';
                }

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'status', 'full_name', 'recruit_job_id', 'location', 'date', 'check','phone', 'email', 'current_ctc', 'expected_ctc', 'total_experience', 'source', 'gender']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RecruitJobApplication $model)
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

        $model = $model->select('recruit_job_applications.id', 'recruit_job_applications.recruit_application_status_id', 'recruit_job_applications.full_name', 'recruit_job_applications.expected_ctc', 'recruit_job_applications.created_at', 'recruit_job_applications.gender', 'recruit_job_applications.phone', 'recruit_job_applications.email', 'recruit_job_applications.total_experience', 'recruit_job_applications.current_location', 'recruit_job_applications.current_ctc', 'recruit_job_applications.added_by', 'recruit_jobs.title', 'recruit_jobs.id as recruit_job_id', 'recruit_jobs.recruiter_id', 'company_addresses.location', 'recruit_application_status.color', 'recruit_application_status.status', 'application_sources.application_source');
        $model = $model->leftJoin('recruit_application_status', 'recruit_application_status.id', '=', 'recruit_job_applications.recruit_application_status_id');
        $model = $model->leftJoin('recruit_jobs', 'recruit_jobs.id', '=', 'recruit_job_applications.recruit_job_id')
            ->leftJoin('company_addresses', 'company_addresses.id', '=', 'recruit_job_applications.location_id')
            ->leftJoin('application_sources', 'application_sources.id', '=', 'recruit_job_applications.application_source_id')
            ->groupBy('recruit_job_applications.id');

        if ($this->viewJobApplicationPermission == 'added') {
            $model->where(function ($query) {
                return $query->where('recruit_job_applications.added_by', user()->id);
            });
        }

        if ($this->viewJobApplicationPermission == 'owned') {
            $model->where(function ($query) {
                return $query->where('recruit_jobs.recruiter_id', user()->id);
            });
        }

        if ($this->viewJobApplicationPermission == 'both') {
            $model->where(function ($query) {
                return $query->where('recruit_job_applications.added_by', user()->id)
                    ->orWhere('recruit_jobs.recruiter_id', user()->id);
            });
        }

        if ($this->request()->searchText != '') {
            $model = $model->where(function ($query) {
                $query->where('recruit_job_applications.full_name', 'like', '%' . request('searchText') . '%')
                    ->orWhere('recruit_job_applications.phone', 'like', '%' . request('searchText') . '%')
                    ->orWhere('recruit_job_applications.email', 'like', '%' . request('searchText') . '%')
                    ->orWhere('recruit_jobs.title', 'like', '%' . request('searchText') . '%')
                    ->orWhere('company_addresses.location', 'like', '%' . request('searchText') . '%')
                    ->orWhere('recruit_application_status.status', 'like', '%' . request('searchText') . '%')
                    ->orWhere('application_sources.application_source', 'like', '%' . request('searchText') . '%');
            });
        }

        if ($request->job != 0 && $request->job != null && $request->job != 'all') {
            $model->where('recruit_jobs.id', '=', $request->job);
        }

        if ($request->location != 0 && $request->location != null && $request->location != 'all') {
            $model = $model->where('company_addresses.id', '=', $request->location);
        }

        if ($request->status != 0 && $request->status != null && $request->status != 'all') {
            $model = $model->where('recruit_job_applications.recruit_application_status_id', '=', $request->status);
        }

        if ($request->gender != null && $request->gender != 'all') {
            $model = $model->where('recruit_job_applications.gender', '=', $request->gender);
        }

        if ($request->total_experience != null && $request->total_experience != 'all') {
            $model = $model->where('recruit_job_applications.total_experience', '=', $request->total_experience);
        }

        if ($request->current_location != null && $request->current_location != 'all') {
            $model = $model->where('recruit_job_applications.current_location', '=', $request->current_location);
        }

        if ($request->current_ctc_min != null && $request->current_ctc_min != '') {
            $model = $model->where('recruit_job_applications.current_ctc', '>=', $request->current_ctc_min);
        }

        if ($request->current_ctc_max != null && $request->current_ctc_max != '') {
            $model = $model->where('recruit_job_applications.current_ctc', '<=', $request->current_ctc_max);
        }

        if ($request->expected_ctc_min != null && $request->expected_ctc_min != '') {
            $model = $model->where('recruit_job_applications.expected_ctc', '>=', $request->expected_ctc_min);
        }

        if ($request->expected_ctc_max != null && $request->expected_ctc_max != '') {
            $model = $model->where('recruit_job_applications.expected_ctc', '<=', $request->expected_ctc_max);
        }

        if ($request->startDate != null && $request->startDate != '') {
            $model = $model->whereDate('recruit_job_applications.created_at', '>=', $startDate);
        }

        if ($request->endDate != null && $request->endDate != '') {
            $model = $model->whereDate('recruit_job_applications.created_at', '<=', $endDate);
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
        return parent::setBuilder('job-applications-table')
            ->parameters([
                'order' => [7, 'desc'],
                'initComplete' => 'function () {
                    window.LaravelDataTables["job-applications-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                   //
                   $(".select-picker").selectpicker();
                 }'
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
            __('recruit::modules.jobApplication.name') => ['data' => 'full_name', 'exportable' => false, 'name' => 'full_name', 'title' => __('recruit::modules.jobApplication.name')],
            __('recruit::modules.jobApplication.phone') => ['data' => 'phone', 'name' => 'phone', 'visible' => false, 'title' => __('recruit::modules.jobApplication.phone')],
            __('recruit::modules.jobApplication.email') => ['data' => 'email', 'name' => 'email', 'visible' => false, 'title' => __('recruit::modules.jobApplication.email')],
            __('recruit::modules.front.fullName') => ['data' => 'name', 'visible' => false, 'name' => 'name', 'title' => __('recruit::modules.front.fullName')],
            __('recruit::modules.jobApplication.jobs') => ['data' => 'recruit_job_id', 'exportable' => false, 'name' => 'recruit_jobs.title', 'title' => __('recruit::modules.jobApplication.jobs')],
            __('recruit::app.jobOffer.job') => ['data' => 'job_name', 'visible' => false, 'name' => 'job_name', 'title' => __('recruit::app.jobOffer.job')],
            __('recruit::modules.job.location') => ['data' => 'location', 'name' => 'company_addresses.location', 'title' => __('recruit::modules.job.location')],
            __('recruit::modules.jobApplication.gender') => ['data' => 'gender', 'name' => 'gender','visible' => false, 'title' => __('recruit::modules.jobApplication.gender')],
            __('recruit::app.jobApplication.date') => ['data' => 'created_at', 'name' => 'created_at', 'title' => __('recruit::app.jobApplication.date')],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'exportable' => false, 'orderable' => false, 'title' => __('app.status')],
            __('recruit::modules.jobApplication.currentCtc') => ['data' => 'current_ctc', 'name' => 'current_ctc', 'visible' => false, 'title' => __('recruit::modules.jobApplication.currentCtc')],
            __('recruit::modules.jobApplication.expectedCtc') => ['data' => 'expected_ctc', 'name' => 'expected_ctc', 'visible' => false, 'title' => __('recruit::modules.jobApplication.expectedCtc')],
            __('recruit::modules.jobApplication.experience') => ['data' => 'total_experience', 'name' => 'total_experience', 'visible' => false, 'title' => __('recruit::modules.jobApplication.experience')],
            __('recruit::modules.sourceSetting.source') => ['data' => 'source', 'name' => 'source', 'visible' => false, 'title' => __('recruit::modules.sourceSetting.source')],


            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(200)
                ->addClass('text-right pr-20')
        ];
    }

}
