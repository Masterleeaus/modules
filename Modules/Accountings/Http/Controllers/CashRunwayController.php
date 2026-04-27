<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\CashRunwayService;

class CashRunwayController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }

public function index(Request $request)
    {
        $days = max(7, (int) $request->get('days', 14));
        $data = (new CashRunwayService())->runway($days);

        return view('accountings::cashflow.runway', $data);
    }
}
