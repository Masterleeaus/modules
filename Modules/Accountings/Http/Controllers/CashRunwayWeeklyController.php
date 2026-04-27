<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\CashRunwayWeeklyService;

class CashRunwayWeeklyController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }

public function index(Request $request)
    {
        $weeks = max(4, (int)$request->get('weeks', 8));
        $data = (new CashRunwayWeeklyService())->weekly($weeks);
        return view('accountings::cashflow.runway_weekly', $data);
    }
}
