<?php

namespace Modules\Accountings\Services;

use Modules\Accountings\Entities\BankReconciliation;
use Modules\Accountings\Entities\BankReconciliationLine;
use Modules\Accountings\Entities\BankTransaction;

class ReconciliationService
{
    public static function recalc(BankReconciliation $rec): BankReconciliation
    {
        $txnIds = BankReconciliationLine::where('reconciliation_id', $rec->id)->pluck('bank_transaction_id')->toArray();
        $matchedTotal = BankTransaction::whereIn('id', $txnIds)->sum('amount');

        $rec->matched_total = (float)$matchedTotal;
        $rec->difference = (float)$rec->closing_balance - ((float)$rec->opening_balance + (float)$matchedTotal);
        $rec->save();

        return $rec;
    }
}
