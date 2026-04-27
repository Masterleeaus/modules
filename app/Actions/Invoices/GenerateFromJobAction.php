<?php

namespace App\Actions\Invoices;

use App\Models\Invoice;
use App\Models\Job;

class GenerateFromJobAction
{
    /**
     * Generate a draft invoice from a completed job, copying line items.
     *
     * Preconditions (caller is responsible for enforcing):
     *  - $job->isCompleted()
     *  - $job->invoice === null
     */
    public function execute(Job $job): Invoice
    {
        $job->load('lineItems');

        $invoice = Invoice::create([
            'organization_id' => $job->organization_id,
            'customer_id'     => $job->customer_id,
            'job_id'          => $job->id,
            'invoice_number'  => $this->nextInvoiceNumber($job->organization_id),
            'status'          => Invoice::STATUS_DRAFT,
            'tax_rate'        => 0,
            'discount_amount' => 0,
            'amount_paid'     => 0,
            'issued_at'       => today(),
            'due_at'          => today()->addDays(30),
        ]);

        foreach ($job->lineItems as $idx => $li) {
            $invoice->lineItems()->create([
                'item_id'     => $li->item_id,
                'name'        => $li->name,
                'description' => $li->description,
                'unit_price'  => $li->unit_price,
                'quantity'    => $li->quantity,
                'is_taxable'  => true,
                'sort_order'  => $idx,
            ]);
        }

        $invoice->recalculate();

        return $invoice;
    }

    private function nextInvoiceNumber(int $orgId): string
    {
        $last = Invoice::withTrashed()
            ->where('organization_id', $orgId)
            ->whereNotNull('invoice_number')
            ->orderByDesc('id')
            ->value('invoice_number');

        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return 'INV-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
