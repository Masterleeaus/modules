<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\ReceivablesService;

class ReceivablesController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }

public function index(Request $request)
    {
        $bucket = $request->get('bucket', 'overdue');
        $data = (new ReceivablesService())->list($bucket);
        return view('accountings::cashflow.receivables', $data);
    }
}
