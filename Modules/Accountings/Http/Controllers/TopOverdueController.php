<?php

namespace Modules\Accountings\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\ReceivablesService;

class TopOverdueController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }

public function index()
    {
        $data = (new ReceivablesService())->topOverdue(20);
        return view('accountings::cashflow.top_overdue', $data);
    }
}
