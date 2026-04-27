<?php

namespace App\Actions\Invoices;

use App\Models\Invoice;

class VoidInvoiceAction
{
    /**
     * Void an invoice.
     *
     * Preconditions (caller is responsible for enforcing):
     *  - $invoice->status !== Invoice::STATUS_VOID
     *  - $invoice->status !== Invoice::STATUS_PAID
     */
    public function execute(Invoice $invoice): Invoice
    {
        $invoice->update(['status' => Invoice::STATUS_VOID]);

        return $invoice->fresh();
    }
}
