<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\CashflowPlannerService;
use Modules\Accountings\Services\BankBalanceService;

class CashflowPlannerController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }

public function index(Request $request)
    {
        $weeks = (int)$request->get('weeks', 8);
        if ($weeks < 4) $weeks = 4;
        if ($weeks > 26) $weeks = 26;

        $scenario = (string)$request->get('scenario', 'expected');

        $gstBuffer = (bool)$request->get('gst_buffer', false);

        $startingCash = (float)$request->get('starting_cash', 0);
        $bankMeta = null;

        if ((int)$request->get('use_last_bank', 0) === 1) {
            $bankMeta = (new BankBalanceService())->latestBalance();
            if ($bankMeta['balance'] !== null) $startingCash = (float)$bankMeta['balance'];
        }

        $data = (new CashflowPlannerService())->plan($weeks, $scenario, $startingCash, $gstBuffer);
        $data['bank_meta'] = $bankMeta;

        return view('accountings::cashflow.planner', $data);
    }
}
