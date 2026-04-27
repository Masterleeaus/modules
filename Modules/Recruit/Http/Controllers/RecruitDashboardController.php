<?php

namespace Modules\Recruit\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use Modules\Recruit\DataTables\DashboardPipelineWidgetDataTable;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitSetting;

class RecruitDashboardController extends AccountBaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.dashboard');
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index(DashboardPipelineWidgetDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_dashboard');
        abort_403(!in_array($viewPermission, ['all', 'added']));

        $this->loggedEmployee = user();

        $this->startDate = (request('startDate') != '') ? Carbon::createFromFormat($this->company->date_format, request('startDate')) : now($this->company->timezone)->startOfMonth();

        $this->endDate = (request('endDate') != '') ? Carbon::createFromFormat($this->company->date_format, request('endDate')) : now($this->company->timezone);

        $startDate = $this->startDate->toDateString();
        $endDate = $this->endDate->toDateString();

        $this->totalOpenings = RecruitJob::whereHas('team', function ($q) {
            $q->where('status', 'open')
                ->where(function ($query) {
                    return $query->where(DB::raw('DATE(`end_date`)'), '>=', now()->format('Y-m-d'))
                        ->orWhere('end_date', '=', null);
                });
        })->count();

        $this->totalApplications = RecruitJobApplication::count();

        $this->totalHired = RecruitJobApplication::join('recruit_application_status', 'recruit_application_status.id', '=', 'recruit_job_applications.recruit_application_status_id')
            ->where('recruit_application_status.status', 'hired')
            ->count();

        $this->totalRejected = RecruitJobApplication::join('recruit_application_status', 'recruit_application_status.id', '=', 'recruit_job_applications.recruit_application_status_id')
            ->where('recruit_application_status.status', 'rejected')
            ->count();

        $currentDate = now()->format('Y-m-d');

        $this->newApplications = RecruitJobApplication::where(DB::raw('DATE(`created_at`)'), $currentDate)->count();

        $this->shortlisted = RecruitJobApplication::join('recruit_application_status', 'recruit_application_status.id', '=', 'recruit_job_applications.recruit_application_status_id')
            ->where('recruit_application_status.status', 'phone screen')
            ->orWhere('recruit_application_status.status', 'interview')
            ->count();

        $this->totalTodayInterview = RecruitInterviewSchedule::where(DB::raw('DATE(`schedule_date`)'), $currentDate)
            ->count();

        $this->activeJobs = RecruitJob::with('recruiter')->whereHas('team', function ($q) {
            $q->where('status', 'open')->where(function ($query) {
                return $query->where(DB::raw('DATE(`end_date`)'), '>=', now()->format('Y-m-d'))
                    ->orWhere('end_date', '=', null);
            });
        })->get();

        $this->todaysInterview = RecruitInterviewSchedule::with('employees', 'employeesData', 'jobApplication', 'jobApplication.job')->where(DB::raw('DATE(`schedule_date`)'), $currentDate)->get();
        $this->applicationSourceWise = $this->applicationChartData($startDate, $endDate);
        $this->candidateStatusWise = $this->candidateStatusChartData($startDate, $endDate);

        return $dataTable->render('recruit::dashboard.index', $this->data);
    }

    public function applicationChartData()
    {
        $labels = ['1', '2', '3', '4', '5'];
        $data['labels'] = [
            __('recruit::app.jobApplication.linkedin'),
            __('recruit::app.jobApplication.facebook'),
            __('recruit::app.jobApplication.instagram'),
            __('recruit::app.jobApplication.twitter'),
            __('recruit::app.jobApplication.other')
        ];
        $data['colors'] = ['#0A66C2', '#1877f2', '#E4405F', '#1DA1F2', '#F57D00'];

        // Use a single query to get all counts
        $counts = RecruitJobApplication::select('recruit_application_status_id', DB::raw('count(*) as total'))
            ->whereIn('recruit_application_status_id', $labels)
            ->groupBy('recruit_application_status_id')
            ->pluck('total', 'recruit_application_status_id');

        $data['values'] = array_map(function ($label) use ($counts) {
            return $counts->get($label, 0);
        }, $labels);

        return $data;
    }


    public function candidateStatusChartData()
    {
        $allId = RecruitApplicationStatus::pluck('id')->toArray();
        $allStatus = RecruitApplicationStatus::pluck('status')->toArray();
        $allColors = RecruitApplicationStatus::pluck('color')->toArray();
        $labels = $allId;
        $data['colors'] = $allColors;
        $data['values'] = [];

        foreach ($allStatus as $key => $value) {
            $data['labels'][] = $value;
        }

        foreach ($labels as $label) {
            $data['values'][] = RecruitJobApplication::with('applicationStatus')->where('recruit_application_status_id', $label)->count();
        }

        return $data;
    }

}
