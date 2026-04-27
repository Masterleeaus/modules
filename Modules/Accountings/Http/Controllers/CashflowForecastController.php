<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\CashflowService;
use Modules\Accountings\Services\CashflowForecastService;

class CashflowForecastController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }

public function index(Request $request)
    {
        $months = max(1, (int)($request->get('months', 6)));

        $series = (new CashflowService())->monthlySeries($request->get('from'), $request->get('to'));
        $forecast = (new CashflowForecastService())->forecast($months);

        return view('accountings::cashflow.forecast', [
            'series' => $series,
            'forecast' => $forecast,
            'months' => $months
        ]);
    }
}
