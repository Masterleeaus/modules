<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Accountings\Services\CashRunwayWeeklyService;

class WeeklyPlannerController extends Controller
{
    public function index(Request $request)
    {
        $weeks = (int)$request->get('weeks', 8);
        if ($weeks < 4) $weeks = 4;
        if ($weeks > 16) $weeks = 16;

        $scenario = (string)$request->get('scenario', 'expected');
        $opening = (float)$request->get('opening_cash', 0);

        $data = (new CashRunwayWeeklyService())->weekly($weeks, $opening, $scenario);

        return view('accountings::cashflow.weekly_planner', $data);
    }
}
