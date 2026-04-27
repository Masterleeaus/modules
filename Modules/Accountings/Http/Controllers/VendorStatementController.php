<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\VendorStatementService;

class VendorStatementController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Vendor Statement';
        $this->pageIcon = 'ti-report-money';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $rows = (new VendorStatementService())->summary($from, $to);

        return view('accountings::reports.vendor_statement', compact('rows', 'from', 'to'));
    }
}
