<?php

namespace Modules\Recruit\DataTables;

use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Yajra\DataTables\Html\Column;
use Modules\Recruit\Entities\RecruitJobOfferLetter;

class JobOfferLetterDataTable extends BaseDataTable
{

    private $editPermission;
    private $deletePermission;
    private $viewPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editPermission = user()->permission('edit_offer_letter');
        $this->deletePermission = user()->permission('delete_offer_letter');
        $this->viewPermission = user()->permission('view_offer_letter');
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
            ->addColumn('offer', function ($row) {
                return '<a href="' . route('job-offer-letter.show', [$row->id]) . '" class="text-darkest-grey" >Offer-' . $row->id . '</a>';
            })
            ->addColumn('job_name', function ($row) {
                return '<a href="' . route('jobs.show', [$row->recruit_job_id]) . '" class="text-darkest-grey" >' . $row->title . '</a>';
            })
            ->addColumn('job_application', function ($row) {
                return '<a href="' . route('job-applications.show', [$row->recruit_job_application_id]) . '" class=" text-darkest-grey openRightModal" >' . $row->full_name . '</a>';;
            })
            ->editColumn('added_by', function ($row) {
                if (!is_null($row->user)) {
                    return view('components.employee', [
                        'user' => $row->user
                    ]);
                }

                return '--';
            })
            ->editColumn('job_expire', function ($row) {
                return $row->job_expire ? $row->job_expire->translatedFormat($this->company->date_format) : '';

            })
            ->editColumn('expected_joining_date', function ($row) {
                return $row->expected_joining_date ? $row->expected_joining_date->translatedFormat($this->company->date_format) : '';

            })
            ->editColumn('status', function ($row) {
                if ($this->editPermission == 'all' ||
                    ($this->editPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $status = '<select class="form-control select-picker change-letter-status" data-letter-id="' . $row->id . '">';
                    $status .= '<option ';

                    if ($row->status == 'pending') {
                        $status .= 'selected';
                    }

                    $status .= ' value="pending" data-content="<i class=\'fa fa-circle mr-2 text-yellow\'></i> ' . __('app.pending') . '">' . __('app.pending') . '</option>';
                    $status .= '<option ';

                    if ($row->status == 'withdraw') {
                        $status .= 'selected';
                    }

                    $status .= ' value="withdraw" data-content="<i class=\'fa fa-circle mr-2 text-blue\'></i> ' . __('recruit::app.job.withdraw') . '"' . __('recruit::app.job.withdraw') . '</option>';
                    $status .= '<option ';

                    if ($row->status == 'accept') {
                        $status .= 'selected';
                    }

                    $status .= ' value="accept" data-content="<i class=\'fa fa-circle mr-2 text-light-green\'></i> ' . __('app.accept') . '"' . __('app.accept') . '</option>';
                    $status .= '<option ';

                    if ($row->status == 'expired') {
                        $status .= 'selected';
                    }

                    $status .= ' value="expired" data-content="<i class=\'fa fa-circle mr-2 text-black\'></i> ' . __('recruit::app.job.expired') . '">' . __('recruit::app.job.expired') . '</option>';

                    $status .= '<option ';

                    if ($row->status == 'decline') {
                        $status .= 'selected';
                    }

                    $status .= ' value="decline" data-content="<i class=\'fa fa-circle mr-2 text-red\'></i> ' . __('app.decline') . '">' . __('app.decline') . '</option>';

                    $status .= '<option ';


                    if ($row->status == 'draft') {
                        $status .= 'selected';
                    }

                    $status .= ' value="draft" data-content="<i class=\'fa fa-circle mr-2 text-brown\'></i> ' . __('recruit::app.job.draft') . '">' . __('recruit::app.job.draft') . '</option>';

                    $status .= '</select>';
                }
                else {
                    if ($row->status == 'pending') {
                        $class = 'text-yellow';
                        $status = __('app.pending');
                    }
                    elseif ($row->status == 'withdraw') {
                        $class = 'text-blue';
                        $status = __('recruit::app.interviewSchedule.hired');
                    }
                    elseif ($row->status == 'accept') {
                        $class = 'text-light-green';
                        $status = __('app.accepted');
                    }
                    elseif ($row->status == 'decline') {
                        $class = 'text-red';
                        $status = __('app.decline');
                    }
                    elseif ($row->status == 'expired') {
                        $class = 'text-black';
                        $status = __('recruit::app.job.expired');
                    }
                    else {
                        $class = 'text-brown';
                        $status = __('app.rejected');
                    }

                    $status = '<i class="fa fa-circle mr-1 ' . $class . ' f-10"></i> ' . $status;
                }

                return $status;
            })
            ->addColumn('action', function ($row) {
                $date1 = $row->job_expire;
                $date2 = now();

                $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"  data-boundary="window" 
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                if ($this->viewPermission == 'all' ||
                    ($this->viewPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->viewPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->viewPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a href="' . route('job-offer-letter.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';
                }

                if ($this->editPermission == 'all' ||
                    ($this->editPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    if ($row->employee_id == null && $row->status != 'accept' && $row->status != 'decline') {
                        $action .= '<a class="dropdown-item openRightModal" href="' . route('job-offer-letter.edit', [$row->id]) . '">
                                    <i class="fa fa-edit mr-2"></i>
                                    ' . trans('app.edit') . '
                                </a>';
                    }
                }

                if ($this->deletePermission == 'all' ||
                    ($this->deletePermission == 'added' && $row->added_by == user()->id) ||
                    ($this->deletePermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->deletePermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-offer-id="' . $row->id . '">
                                    <i class="fa fa-trash mr-2"></i>
                                    ' . trans('app.delete') . '
                                </a>';
                }

                if ($this->editPermission == 'all' ||
                    ($this->editPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    if ($row->status == 'pending' || $row->status == 'draft' || $row->status == 'withdraw' && $row->employee_id == null && $date1->greaterThanOrEqualTo($date2)) {
                        $action .= '<a class="dropdown-item send-offer-letter" href="javascript:;" data-send-id="' . $row->id . '">
                                    <i class="fa fa-paper-plane mr-2"></i>
                                    ' . __('recruit::modules.joboffer.sendoffer') . '
                                </a>';
                    }

                    if ($row->status == 'accept' && $row->employee_id == null) {
                        $action .= '<a class="dropdown-item create-employee" href="javascript:;" data-offer-id="' . $row->id . '">
                            <i class="fa fa-plus mr-2"></i>
                                ' . __('recruit::modules.joboffer.create_emp') . '
                            </a>';
                    }
                }

                if ($this->editPermission == 'all' ||
                    ($this->editPermission == 'added' && $row->added_by == user()->id) ||
                    ($this->editPermission == 'owned' && user()->id == $row->recruiter_id) ||
                    ($this->editPermission == 'both' && user()->id == $row->recruiter_id) ||
                    $row->added_by == user()->id) {
                    if ($row->status == 'pending' || $row->status == 'draft' && $row->employee_id == null && $date1->greaterThanOrEqualTo($date2)) {
                        $action .= '<a class="dropdown-item withdraw-offer-letter" href="javascript:;" data-withdraw-id="' . $row->id . '">
                                <i class="fa fa-backward mr-2"></i>
                                ' . __('recruit::modules.joboffer.withdraw') . '
                            </a>';
                    }
                }

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'job_name', 'job_application', 'added_by', 'job_expire', 'expected_joining_date', 'check', 'status', 'offer'])
            ->removeColumn('updated_at')
            ->removeColumn('created_at');
    }

    /**
     * Get query source of dataTable.
     *
     * @param  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RecruitJobOfferLetter $jobs)
    {
        $startDate = null;
        $endDate = null;

        if ($this->request()->startDate !== null && $this->request()->startDate != 'null' && $this->request()->startDate != '') {
            $startDate = Carbon::createFromFormat($this->company->date_format, $this->request()->startDate)->toDateString();
        }

        if ($this->request()->endDate !== null && $this->request()->endDate != 'null' && $this->request()->endDate != '') {
            $endDate = Carbon::createFromFormat($this->company->date_format, $this->request()->endDate)->toDateString();
        }

        $jobs = $jobs->with('user.employeeDetail.designation:id,name')->select('recruit_job_offer_letter.employee_id', 'recruit_job_offer_letter.status', 'recruit_job_offer_letter.id', 'recruit_job_offer_letter.recruit_job_id', 'recruit_job_offer_letter.recruit_job_application_id', 'recruit_job_offer_letter.job_expire', 'recruit_job_offer_letter.expected_joining_date', 'recruit_job_offer_letter.added_by', 'recruit_job_applications.full_name', 'recruit_jobs.title', 'recruit_jobs.recruiter_id');
        $jobs = $jobs->leftJoin('recruit_jobs', 'recruit_jobs.id', '=', 'recruit_job_offer_letter.recruit_job_id')
            ->leftJoin('recruit_job_applications', 'recruit_job_applications.id', '=', 'recruit_job_offer_letter.recruit_job_application_id')
            ->groupBy('recruit_job_offer_letter.id');

        if ($this->viewPermission == 'added') {
            $jobs->where(function ($query) {
                return $query->where('recruit_job_offer_letter.added_by', user()->id);
            });
        }

        if ($this->viewPermission == 'owned') {
            $jobs->where(function ($query) {
                return $query->where('recruit_jobs.recruiter_id', user()->id);
            });
        }

        if ($this->viewPermission == 'both') {
            $jobs->where(function ($query) {
                return $query->where('recruit_job_offer_letter.added_by', user()->id)
                    ->orWhere('recruit_jobs.recruiter_id', user()->id);
            });
        }

        if ($this->request()->job != 0 && $this->request()->job != null && $this->request()->job != 'all') {
            $jobs->where('recruit_jobs.id', '=', $this->request()->job);
        }

        if ($startDate != null && $startDate != '') {
            $jobs = $jobs->whereDate('recruit_job_offer_letter.created_at', '>=', $startDate);
        }

        if ($endDate != null && $endDate != '') {
            $jobs = $jobs->whereDate('recruit_job_offer_letter.created_at', '<=', $endDate);
        }

        if ($this->request()->searchText != '') {
            $jobs = $jobs->where(function ($query) {
                $query->where('recruit_job_applications.full_name', 'like', '%' . request('searchText') . '%');
            });
        }

        if ($this->request()->status != 'all' && $this->request()->status != '') {
            $jobs = $jobs->where('recruit_job_offer_letter.status', $this->request()->status);
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
        return parent::setBuilder('offer-table')
            ->parameters([
                'order' => [2, 'desc'],
                'initComplete' => 'function () {
                    window.LaravelDataTables["offer-table"].buttons().container()
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
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'exportable' => false, 'title' => __('app.id')],
            __('Offer') => ['data' => 'offer', 'name' => 'id', 'title' => __('Offer')],
            __('recruit::modules.joboffer.job') => ['data' => 'job_name', 'name' => 'recruit_jobs.title', 'title' => __('recruit::modules.joboffer.job')],
            __('recruit::app.jobOffer.jobApplicant') => ['data' => 'job_application', 'name' => 'recruit_job_applications.full_name', 'title' => __('recruit::app.jobOffer.jobApplicant')],
            __('app.addedBy') => ['data' => 'added_by', 'name' => 'added_by', 'title' => __('app.addedBy')],
            __('recruit::modules.joboffer.OfferExp') => ['data' => 'job_expire', 'name' => 'job_expire', 'title' => __('recruit::modules.joboffer.OfferExp')],
            __('recruit::app.jobOffer.expJoinDate') => ['data' => 'expected_joining_date', 'name' => 'expected_joining_date', 'title' => __('recruit::app.jobOffer.expJoinDate')],
            __('recruit::modules.joboffer.status') => ['data' => 'status', 'name' => 'status', 'title' => __('recruit::modules.joboffer.status')],

            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

}
