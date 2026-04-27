<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Validation\ValidationException;
use Modules\Accountings\Entities\BankTransaction;
use Modules\Accountings\Entities\BankTransactionMatch;

class BankTransactionMatchController extends AccountBaseController
{
    public function store(Request $request, $id)
    {
        $txn = BankTransaction::findOrFail($id);

        $data = $request->validate([
            'match_type' => 'required|in:bill,expense,invoice',
            'match_id' => 'required|integer|min:1',
            'matched_amount' => 'nullable|numeric',
        ]);

        $exists = BankTransactionMatch::where('bank_transaction_id', $txn->id)
            ->where('match_type', $data['match_type'])
            ->where('match_id', $data['match_id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages(['match_id' => 'This match already exists.']);
        }

        BankTransactionMatch::create([
            'company_id' => $txn->company_id,
            'user_id' => $txn->user_id,
            'bank_transaction_id' => $txn->id,
            'match_type' => $data['match_type'],
            'match_id' => $data['match_id'],
            'matched_amount' => $data['matched_amount'] ?? null,
        ]);

        return back()->with('status', 'Transaction matched.');
    }

    public function destroy(Request $request, $id, $matchId)
    {
        $txn = BankTransaction::findOrFail($id);

        $match = BankTransactionMatch::where('bank_transaction_id', $txn->id)
            ->where('id', $matchId)
            ->firstOrFail();

        $match->delete();

        return back()->with('status', 'Match removed.');
    }
}
