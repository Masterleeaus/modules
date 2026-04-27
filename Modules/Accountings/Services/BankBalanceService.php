<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Best-effort "starting cash" helper.
 * Tries common Worksuite/finance table patterns, but never hard-fails.
 */
class BankBalanceService
{
    public function latestBalance(): array
    {
        $bal = $this->fromBankAccounts();
        if ($bal !== null) return ['balance' => $bal, 'source' => 'bank_accounts.balance'];

        $bal = $this->fromGenericBalanceTables();
        if ($bal !== null) return ['balance' => $bal, 'source' => 'generic_balance_table'];

        $bal = $this->fromTransactionsNet();
        if ($bal !== null) return ['balance' => $bal, 'source' => 'transactions_net'];

        return ['balance' => null, 'source' => 'none'];
    }

    private function fromBankAccounts(): ?float
    {
        if (!Schema::hasTable('bank_accounts')) return null;
        foreach (['balance','current_balance','available_balance'] as $c) {
            if (!Schema::hasColumn('bank_accounts', $c)) continue;

            $q = DB::table('bank_accounts');
            if (Schema::hasColumn('bank_accounts','is_active')) $q->where('is_active',1);
            $rows = $q->select($c)->get();
            if ($rows->count() === 0) return null;

            $sum = 0.0;
            foreach ($rows as $r) $sum += (float)$r->$c;
            return $sum;
        }
        return null;
    }

    private function fromGenericBalanceTables(): ?float
    {
        $candidates = [
            ['accounts','current_balance'],
            ['accounts','balance'],
            ['accounting_accounts','balance'],
            ['bank_account_details','balance'],
        ];
        foreach ($candidates as [$t,$c]) {
            if (!Schema::hasTable($t)) continue;
            if (!Schema::hasColumn($t,$c)) continue;
            $rows = DB::table($t)->select($c)->get();
            if ($rows->count() === 0) continue;

            $sum = 0.0;
            foreach ($rows as $r) $sum += (float)$r->$c;
            return $sum;
        }
        return null;
    }

    private function fromTransactionsNet(): ?float
    {
        $t = null;
        foreach (['bank_transactions','transactions'] as $cand) {
            if (Schema::hasTable($cand)) { $t = $cand; break; }
        }
        if (!$t) return null;

        $creditCol = Schema::hasColumn($t,'credit') ? 'credit' : (Schema::hasColumn($t,'amount') ? 'amount' : null);
        $debitCol  = Schema::hasColumn($t,'debit') ? 'debit' : null;
        $typeCol   = Schema::hasColumn($t,'type') ? 'type' : null;

        if (!$creditCol) return null;

        if ($debitCol) {
            $row = DB::table($t)->selectRaw("SUM($creditCol) as cr, SUM($debitCol) as dr")->first();
            if (!$row) return null;
            return (float)$row->cr - (float)$row->dr;
        }

        if ($typeCol) {
            $cr = DB::table($t)->whereIn($typeCol, ['credit','in','income'])->sum($creditCol);
            $dr = DB::table($t)->whereIn($typeCol, ['debit','out','expense'])->sum($creditCol);
            return (float)$cr - (float)$dr;
        }

        return null;
    }
}
