<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitSetting;

class ReportController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.report');
        $this->middleware(function ($request, $next) {
            abort_403(! in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index()
    {
        $viewPermission = user()->permission('view_report');
        abort_403(! in_array($viewPermission, ['all']));

        $this->jobApplication = RecruitJobApplication::count();
        $this->job = RecruitJob::count();
        $this->candidatesHired = RecruitJobApplication::join('recruit_application_status', 'recruit_application_status.id', '=', 'recruit_job_applications.recruit_application_status_id')
            ->where('recruit_application_status.status', 'hired')
            ->count();
        $this->interviewScheduled = RecruitInterviewSchedule::count();

        return view('recruit::report.index', $this->data);
    }

    public function reportChartData(Request $request)
    {
        $viewPermission = user()->permission('view_report');
        abort_403(! in_array($viewPermission, ['all']));

        $fromDate = now($this->company->timezone)->startOfMonth()->toDateString();
        $toDate = now($this->company->timezone)->toDateString();

        if (request()->startDate !== null && request()->startDate != 'null' && request()->startDate != '') {
            $fromDate = Carbon::createFromFormat($this->company->date_format, request()->startDate)->toDateString();
        }

        if (request()->endDate !== null && request()->endDate != 'null' && request()->endDate != '') {
            $toDate = Carbon::createFromFormat($this->company->date_format, request()->endDate)->toDateString();
        }

        $this->jobApplication = RecruitJobApplication::where(DB::raw('DATE(`created_at`)'), '>=', $fromDate)
            ->where(DB::raw('DATE(`created_at`)'), '<=', $toDate)
            ->count();
        $this->job = RecruitJob::where(DB::raw('DATE(`created_at`)'), '>=', $fromDate)
            ->where(DB::raw('DATE(`created_at`)'), '<=', $toDate)->count();

        $this->candidatesHired = RecruitJobApplication::join('recruit_application_status', 'recruit_application_status.id', '=', 'recruit_job_applications.recruit_application_status_id')
            ->where(DB::raw('DATE(recruit_job_applications.created_at)'), '>=', $fromDate)
            ->where(DB::raw('DATE(recruit_job_applications.created_at)'), '<=', $toDate)
            ->where('recruit_application_status.status', 'hired')
            ->count();

        $this->interviewScheduled = RecruitInterviewSchedule::where(DB::raw('DATE(`created_at`)'), '>=', $fromDate)
            ->where(DB::raw('DATE(`created_at`)'), '<=', $toDate)->count();

        $data = [];
        $data['labels'] = [__('recruit::app.menu.jobApplication'), __('recruit::app.report.jobposted'), __('recruit::app.report.candidatehired'), __('recruit::app.report.interviews')];
        $data['colors'] = ['orange', 'grey', 'green', 'blue'];
        $data['chart_data'] = [$this->jobApplication, $this->job, $this->candidatesHired, $this->interviewScheduled];

        $this->chart = $data;

        $html = view('recruit::report.chart', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html, 'jobApp' => $this->jobApplication, 'jobPosted' => $this->job, 'candidateHired' => $this->candidatesHired, 'interview' => $this->interviewScheduled, 'title' => $this->pageTitle]);
    }
}
