<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\PayablesService;

class PayablesController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }

public function index(Request $request)
    {
        $bucket = $request->get('bucket', 'due_14');
        $data = (new PayablesService())->list($bucket);
        return view('accountings::cashflow.payables', $data);
    }
}
