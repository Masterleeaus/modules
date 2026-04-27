<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\JobProfitabilityService;

class JobProfitabilityController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Job Profitability';
        $this->pageIcon = 'ti-bar-chart';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $includeRevenue = $request->get('include_revenue', '1') === '1';
        $paidOnly = $request->get('paid_only', '0') === '1';
        $rows = (new JobProfitabilityService())->summary($from, $to, $includeRevenue, $paidOnly);
        return view('accountings::reports.job_profitability', compact('rows', 'from', 'to', 'includeRevenue', 'paidOnly'));
    }
}
