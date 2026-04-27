<?php

namespace App\Actions\Invoices;

use App\Models\Invoice;
use App\Models\Payment;

class RecordPaymentAction
{
    /**
     * Record a payment against an invoice and update the invoice balance / status.
     *
     * @param  array{amount: float|string, method: string, reference?: string|null, notes?: string|null, paid_at: string}  $data
     */
    public function execute(Invoice $invoice, array $data, ?int $recordedById = null): Payment
    {
        $payment = Payment::create([
            'organization_id' => $invoice->organization_id,
            'invoice_id'      => $invoice->id,
            'recorded_by'     => $recordedById,
            'amount'          => $data['amount'],
            'method'          => $data['method'],
            'reference'       => $data['reference'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'status'          => 'completed',
            'paid_at'         => $data['paid_at'],
        ]);

        $newAmountPaid = round((float) $invoice->amount_paid + (float) $data['amount'], 2);
        $balanceDue    = max(0, round((float) $invoice->total - $newAmountPaid, 2));

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'balance_due' => $balanceDue,
            'status'      => $balanceDue <= 0 ? Invoice::STATUS_PAID : Invoice::STATUS_PARTIAL,
            'paid_at'     => $balanceDue <= 0 ? now() : $invoice->paid_at,
        ]);

        return $payment;
    }
}
