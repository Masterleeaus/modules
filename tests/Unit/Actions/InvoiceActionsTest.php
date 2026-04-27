<?php

/**
 * Unit tests for Invoice Action classes.
 */

use App\Actions\Invoices\GenerateFromJobAction;
use App\Actions\Invoices\RecordPaymentAction;
use App\Actions\Invoices\VoidInvoiceAction;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Payment;

// ── GenerateFromJobAction ─────────────────────────────────────────────────────

test('GenerateFromJobAction creates a draft invoice from a completed job', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);
    $job      = Job::factory()->completed()->forCustomer($customer)->create();
    $job->lineItems()->create(['name' => 'Labor', 'unit_price' => 200, 'quantity' => 1, 'sort_order' => 0]);
    $job->lineItems()->create(['name' => 'Parts', 'unit_price' => 50,  'quantity' => 2, 'sort_order' => 1]);

    $invoice = app(GenerateFromJobAction::class)->execute($job);

    expect($invoice)->toBeInstanceOf(Invoice::class)
        ->and($invoice->status)->toBe(Invoice::STATUS_DRAFT)
        ->and($invoice->job_id)->toBe($job->id)
        ->and($invoice->lineItems()->count())->toBe(2)
        ->and((float) $invoice->total)->toBe(300.0);
});

test('GenerateFromJobAction generates sequential invoice numbers', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    $job1 = Job::factory()->completed()->forCustomer($customer)->create();
    $job2 = Job::factory()->completed()->forCustomer($customer)->create();

    $inv1 = app(GenerateFromJobAction::class)->execute($job1);
    $inv2 = app(GenerateFromJobAction::class)->execute($job2);

    expect($inv1->invoice_number)->toBe('INV-0001')
        ->and($inv2->invoice_number)->toBe('INV-0002');
});

// ── RecordPaymentAction ───────────────────────────────────────────────────────

test('RecordPaymentAction records a payment and marks invoice paid when balance cleared', function () {
    $invoice = Invoice::factory()->create([
        'status'      => Invoice::STATUS_SENT,
        'total'       => 300,
        'amount_paid' => 0,
        'balance_due' => 300,
    ]);

    $payment = app(RecordPaymentAction::class)->execute($invoice, [
        'amount'  => 300,
        'method'  => Payment::METHOD_CASH,
        'paid_at' => today()->toDateString(),
    ]);

    $invoice->refresh();

    expect($payment)->toBeInstanceOf(Payment::class)
        ->and((float) $payment->amount)->toBe(300.0)
        ->and($invoice->status)->toBe(Invoice::STATUS_PAID)
        ->and((float) $invoice->balance_due)->toBe(0.0)
        ->and($invoice->paid_at)->not->toBeNull();
});

test('RecordPaymentAction marks invoice as partial for a partial payment', function () {
    $invoice = Invoice::factory()->create([
        'status'      => Invoice::STATUS_SENT,
        'total'       => 500,
        'amount_paid' => 0,
        'balance_due' => 500,
    ]);

    app(RecordPaymentAction::class)->execute($invoice, [
        'amount'  => 200,
        'method'  => Payment::METHOD_CHECK,
        'paid_at' => today()->toDateString(),
    ]);

    $invoice->refresh();

    expect($invoice->status)->toBe(Invoice::STATUS_PARTIAL)
        ->and((float) $invoice->balance_due)->toBe(300.0);
});

// ── VoidInvoiceAction ─────────────────────────────────────────────────────────

test('VoidInvoiceAction voids a sent invoice', function () {
    $invoice = Invoice::factory()->create(['status' => Invoice::STATUS_SENT]);

    $voided = app(VoidInvoiceAction::class)->execute($invoice);

    expect($voided->status)->toBe(Invoice::STATUS_VOID);
});
