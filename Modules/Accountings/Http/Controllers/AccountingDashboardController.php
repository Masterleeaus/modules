<?php
namespace Modules\Accountings\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Entities\Journald;
use Modules\Accountings\Services\CashflowForecastService;

class AccountingDashboardController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }

public function index()
    {
        $rows = Journald::with(['coa','journal'])->get();

        $cashIn = $cashOut = $revenue = $expenses = 0;

        foreach ($rows as $d) {
            $desc = strtolower((string)optional($d->coa)->coa_desc);
            $debit = (float)($d->debit ?? 0);
            $credit = (float)($d->credit ?? 0);

            if (str_contains($desc,'cash') || str_contains($desc,'bank')) {
                $cashIn += $debit;
                $cashOut += $credit;
            }
            if (str_contains($desc,'revenue') || str_contains($desc,'income')) {
                $revenue += $credit;
            }
            if (str_contains($desc,'expense')) {
                $expenses += $debit;
            }
        }

        return view('accountings::dashboard.index', compact(
            'cashIn','cashOut','revenue','expenses'
        ))->with('netCash', $cashIn - $cashOut);
    }
}
