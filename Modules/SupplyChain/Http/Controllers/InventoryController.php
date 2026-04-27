<?php

namespace Modules\SupplyChain\Http\Controllers;

use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function index()
    {
        return view('supplychain::index');
    }
}
