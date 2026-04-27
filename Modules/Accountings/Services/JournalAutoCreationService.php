<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\Accountings\Entities\Account;
use Modules\Accountings\Entities\JournalEntry;
use Modules\Accountings\Entities\JournalLine;

/**
 * Automatically creates double-entry journal entries for key business events.
 *
 * Supported triggers:
 *  - PaymentReceived:  DR Cash/Bank (1000)  CR Accounts Receivable (1100)
 *  - SupplyPurchased:  DR Cleaning Supplies (5200)  CR Accounts Payable (2000)
 */
class JournalAutoCreationService
{
    /**
     * Create a journal entry when a payment is received.
     *
     * @param  \App\Models\Payment  $payment
     */
    public function onPaymentReceived(mixed $payment): void
    {
        if (! Schema::hasTable('accounts') || ! Schema::hasTable('journal_entries')) {
            return;
        }

        try {
            $orgId = $payment->organization_id ?? null;
            if (! $orgId) {
                return;
            }

            $amount  = (float) ($payment->amount ?? 0);
            if ($amount <= 0) {
                return;
            }

            // Resolve system accounts for this organization
            $cashAccount = $this->findAccount($orgId, '1000');
            $arAccount   = $this->findAccount($orgId, '1100');

            if (! $cashAccount || ! $arAccount) {
                Log::debug('[JournalAutoCreation] Cash/AR account not found — skipping', ['org_id' => $orgId]);
                return;
            }

            $entry = JournalEntry::create([
                'organization_id' => $orgId,
                'reference_type'  => 'Payment',
                'reference_id'    => $payment->id,
                'description'     => 'Payment received — ' . ($payment->reference ?? "Payment #{$payment->id}"),
                'entry_date'      => ($payment->paid_at ?? now())->toDateString(),
            ]);

            // DR: Cash/Bank
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $cashAccount->id,
                'debit'            => $amount,
                'credit'           => 0,
                'description'      => 'Cash received',
            ]);

            // CR: Accounts Receivable
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $arAccount->id,
                'debit'            => 0,
                'credit'           => $amount,
                'description'      => 'Revenue realised',
            ]);
        } catch (\Throwable $e) {
            Log::warning('[JournalAutoCreation] onPaymentReceived failed', [
                'payment_id' => $payment->id ?? null,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create a journal entry when a supply purchase is recorded.
     *
     * @param  object  $purchase  Any model with organization_id, amount, id
     */
    public function onSupplyPurchased(mixed $purchase): void
    {
        if (! Schema::hasTable('accounts') || ! Schema::hasTable('journal_entries')) {
            return;
        }

        try {
            $orgId = $purchase->organization_id ?? null;
            if (! $orgId) {
                return;
            }

            $amount = (float) ($purchase->amount ?? $purchase->total ?? 0);
            if ($amount <= 0) {
                return;
            }

            $suppliesAccount = $this->findAccount($orgId, '5200');
            $apAccount       = $this->findAccount($orgId, '2000');

            if (! $suppliesAccount || ! $apAccount) {
                Log::debug('[JournalAutoCreation] Supplies/AP account not found — skipping', ['org_id' => $orgId]);
                return;
            }

            $entry = JournalEntry::create([
                'organization_id' => $orgId,
                'reference_type'  => get_class($purchase),
                'reference_id'    => $purchase->id,
                'description'     => 'Supply purchased — ' . (property_exists($purchase, 'description') ? $purchase->description : "#{$purchase->id}"),
                'entry_date'      => (property_exists($purchase, 'purchased_at') && $purchase->purchased_at ? $purchase->purchased_at->toDateString() : now()->toDateString()),
            ]);

            // DR: Cleaning Supplies Expense
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $suppliesAccount->id,
                'debit'            => $amount,
                'credit'           => 0,
                'description'      => 'Supplies expensed',
            ]);

            // CR: Accounts Payable
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $apAccount->id,
                'debit'            => 0,
                'credit'           => $amount,
                'description'      => 'Payable recorded',
            ]);
        } catch (\Throwable $e) {
            Log::warning('[JournalAutoCreation] onSupplyPurchased failed', [
                'purchase_id' => $purchase->id ?? null,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    protected function findAccount(int $orgId, string $code): ?Account
    {
        return Account::where('organization_id', $orgId)
            ->where('code', $code)
            ->first();
    }
}
