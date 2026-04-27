<?php

namespace Modules\Recruit\DataTables;

use App\Models\CompanyAddress;
use Illuminate\Support\Carbon;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\Html\Button;
use Modules\Recruit\Entities\RecruitSkill;
use Modules\Recruit\Entities\RecruitCandidateDatabase;

class CandidateDatabaseDataTable extends BaseDataTable
{

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
            ->editColumn('name', function ($row) {
                return '<div class="media align-items-center">
            <div class="media-body">
            <h5 class="mb-0 f-13 text-darkest-grey"><a href="' . route('candidate-database.show', [$row->id]) . '" class="openRightModal">' . $row->name . '</a></h5>
            </div>
            </div>';
            })
            ->editColumn('job', function ($row) {
                return $row->job;
            })
            ->editColumn('location', function ($row) {
                $locationname = CompanyAddress::where('id', $row->location_id)->get('location');

                return $locationname[0]['location'];
            })
            ->editColumn(
                'job_applied_on',
                function ($row) {
                    return date('d-m-Y', strtotime($row->Job_applied_on));
                }
            )
            ->addColumn('candidate_name', function ($row) {
                return $row->name;
            })
            ->editColumn('skills', function ($row) {
                $applicant_skills = RecruitSkill::whereIn('id', $row->skills)->select('name')->get();
                $status = '<ul ';

                foreach ($applicant_skills as $item) {
                    $status .= '<li ';
                    $status .= '>' . $item['name'] . '</li>';
                }

                $status .= '</ul>';

                return $status;
            })
            ->addColumn('skill', function ($row) {
                $applicant_skills = RecruitSkill::whereIn('id', $row->skills)->pluck('name')->toArray();
                $skill = implode(',', $applicant_skills);

                return $skill;
            })
            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['name', 'job', 'location', 'skills', 'job_applied_on']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RecruitCandidateDatabase $model)
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

        $model = $model->select('recruit_candidate_database.*', 'recruit_jobs.title as job', 'company_addresses.location');
        $model = $model->leftJoin('recruit_jobs', 'recruit_jobs.id', '=', 'recruit_candidate_database.recruit_job_id')
            ->leftJoin('company_addresses', 'company_addresses.id', '=', 'recruit_candidate_database.location_id')
            ->groupBy('recruit_candidate_database.id');

        if ($this->request()->searchText != '') {
            $model = $model->where(function ($query) {
                $query->where('name', 'like', '%' . request('searchText') . '%');
            });
        }

        if ($request->job != 0 && $request->job != null && $request->job != 'all') {
            $model->where('recruit_jobs.id', '=', $request->job);
        }

        if ($request->skill != 0 && $request->skill != 'all') {
            $model->whereJsonContains('skills', (int)$request->skill);
        }

        if ($request->name != 0 && $request->name != 'all') {
            $model->where('recruit_candidate_database.id', $request->name);
        }

        if ($request->location != 0 && $request->location != null && $request->location != 'all') {
            $model = $model->where('company_addresses.id', '=', $request->location);
        }

        if ($request->startDate != null && $request->startDate != '') {
            $model = $model->whereDate('job_applied_on', '>=', $startDate);
        }

        if ($request->endDate != null && $request->endDate != '') {
            $model = $model->whereDate('job_applied_on', '<=', $endDate);
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
        return parent::setBuilder('candidate-database-table')
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["candidate-database-table"].buttons().container()
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
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'exportable' => false],
            __('recruit::modules.jobApplication.serialNo') => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false ,'title' => __('recruit::modules.jobApplication.serialNo')],
            __('recruit::modules.jobApplication.name') => ['data' => 'name', 'exportable' => false, 'name' => 'name', 'title' => __('recruit::modules.jobApplication.name')],
            __('recruit::modules.interviewSchedule.candidateName') => ['data' => 'candidate_name', 'visible' => false, 'name' => 'candidate_name', 'title' => __('recruit::modules.interviewSchedule.candidateName')],
            __('recruit::modules.job.job') => ['data' => 'job', 'name' => 'job', 'title' => __('recruit::modules.job.job')],
            __('recruit::modules.job.location') => ['data' => 'location', 'name' => 'location', 'title' => __('recruit::modules.job.location')],
            __('recruit::modules.jobApplication.jobapplied') => ['data' => 'job_applied_on', 'name' => 'job_applied_on', 'title' => __('recruit::modules.jobApplication.jobapplied')],
            __('recruit::modules.jobApplication.skills') => ['data' => 'skills', 'exportable' => false, 'name' => 'skills', 'title' => __('recruit::modules.jobApplication.skills')],
            __('recruit::modules.job.skillSet') => ['data' => 'skill', 'visible' => false, 'name' => 'skill', 'title' => __('recruit::modules.job.skillSet')],
        ];
    }

}
