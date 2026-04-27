<?php

namespace Modules\Accountings\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\ReceivablesService;

class ARAgingController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }

public function index()
    {
        $data = (new ReceivablesService())->aging(500);
        return view('accountings::cashflow.ar_aging', $data);
    }
}
