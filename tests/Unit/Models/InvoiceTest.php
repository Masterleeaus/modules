<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Models\Organization;
use App\Models\Payment;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeInvoiceWithCustomer(): array
{
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$org, $customer];
}

// ── Constants ─────────────────────────────────────────────────────────────────

test('invoice has expected status constants', function () {
    expect(Invoice::STATUS_DRAFT)->toBe('draft');
    expect(Invoice::STATUS_SENT)->toBe('sent');
    expect(Invoice::STATUS_PAID)->toBe('paid');
    expect(Invoice::STATUS_PARTIAL)->toBe('partial');
    expect(Invoice::STATUS_OVERDUE)->toBe('overdue');
    expect(Invoice::STATUS_VOID)->toBe('void');
});

test('statuses() returns all six statuses', function () {
    expect(Invoice::statuses())->toHaveCount(6);
});

// ── isPaid() ──────────────────────────────────────────────────────────────────

test('isPaid returns true for a paid invoice', function () {
    [, $customer] = makeInvoiceWithCustomer();
    $invoice = Invoice::factory()->forCustomer($customer)->paid()->create();

    expect($invoice->isPaid())->toBeTrue();
});

test('isPaid returns false for a draft invoice', function () {
    [, $customer] = makeInvoiceWithCustomer();
    $invoice = Invoice::factory()->forCustomer($customer)->draft()->create();

    expect($invoice->isPaid())->toBeFalse();
});

// ── recalculate() ─────────────────────────────────────────────────────────────

test('recalculate computes subtotal from line items', function () {
    [, $customer] = makeInvoiceWithCustomer();

    $invoice = Invoice::factory()->forCustomer($customer)->create([
        'tax_rate'       => 0,
        'discount_amount'=> 0,
        'amount_paid'    => 0,
    ]);

    $invoice->lineItems()->create(['name' => 'Labor', 'unit_price' => 100, 'quantity' => 2, 'sort_order' => 0, 'is_taxable' => false]);
    $invoice->lineItems()->create(['name' => 'Parts', 'unit_price' => 50,  'quantity' => 1, 'sort_order' => 1, 'is_taxable' => false]);

    $invoice->recalculate();

    expect((float) $invoice->fresh()->subtotal)->toBe(250.0);
});

test('recalculate applies tax rate only to taxable line items', function () {
    [, $customer] = makeInvoiceWithCustomer();

    $invoice = Invoice::factory()->forCustomer($customer)->create([
        'tax_rate'       => 0.10,
        'discount_amount'=> 0,
        'amount_paid'    => 0,
    ]);

    $invoice->lineItems()->create(['name' => 'Labor', 'unit_price' => 100, 'quantity' => 1, 'sort_order' => 0, 'is_taxable' => true]);
    $invoice->lineItems()->create(['name' => 'Parts', 'unit_price' => 50,  'quantity' => 1, 'sort_order' => 1, 'is_taxable' => false]);

    $invoice->recalculate();
    $fresh = $invoice->fresh();

    expect((float) $fresh->subtotal)->toBe(150.0);
    expect((float) $fresh->tax_amount)->toBe(10.0);   // 10% of taxable 100
    expect((float) $fresh->total)->toBe(160.0);
});

test('recalculate applies discount before calculating total', function () {
    [, $customer] = makeInvoiceWithCustomer();

    $invoice = Invoice::factory()->forCustomer($customer)->create([
        'tax_rate'       => 0,
        'discount_amount'=> 20,
        'amount_paid'    => 0,
    ]);

    $invoice->lineItems()->create(['name' => 'Service', 'unit_price' => 200, 'quantity' => 1, 'sort_order' => 0, 'is_taxable' => false]);

    $invoice->recalculate();

    expect((float) $invoice->fresh()->total)->toBe(180.0);
});

test('recalculate sets balance_due as total minus amount_paid', function () {
    [, $customer] = makeInvoiceWithCustomer();

    $invoice = Invoice::factory()->forCustomer($customer)->create([
        'tax_rate'       => 0,
        'discount_amount'=> 0,
        'amount_paid'    => 50,
    ]);

    $invoice->lineItems()->create(['name' => 'Service', 'unit_price' => 200, 'quantity' => 1, 'sort_order' => 0, 'is_taxable' => false]);

    $invoice->recalculate();

    expect((float) $invoice->fresh()->balance_due)->toBe(150.0);
});

test('recalculate returns zero subtotal when no line items exist', function () {
    [, $customer] = makeInvoiceWithCustomer();

    $invoice = Invoice::factory()->forCustomer($customer)->create([
        'tax_rate'       => 0,
        'discount_amount'=> 0,
        'amount_paid'    => 0,
    ]);

    $invoice->recalculate();

    expect((float) $invoice->fresh()->subtotal)->toBe(0.0);
    expect((float) $invoice->fresh()->total)->toBe(0.0);
});

// ── Relationships ─────────────────────────────────────────────────────────────

test('invoice has many line items', function () {
    [, $customer] = makeInvoiceWithCustomer();
    $invoice = Invoice::factory()->forCustomer($customer)->create();

    $invoice->lineItems()->create(['name' => 'Labor', 'unit_price' => 100, 'quantity' => 1, 'sort_order' => 0]);
    $invoice->lineItems()->create(['name' => 'Parts', 'unit_price' => 50,  'quantity' => 1, 'sort_order' => 1]);

    expect($invoice->lineItems)->toHaveCount(2);
});

test('invoice has many payments', function () {
    [, $customer] = makeInvoiceWithCustomer();
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create([
        'total'       => 300,
        'balance_due' => 300,
    ]);

    Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 100]);
    Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 200]);

    expect($invoice->payments)->toHaveCount(2);
});

test('invoice belongs to an organization', function () {
    [$org, $customer] = makeInvoiceWithCustomer();
    $invoice = Invoice::factory()->forCustomer($customer)->create();

    expect($invoice->organization)->toBeInstanceOf(Organization::class);
    expect($invoice->organization->id)->toBe($org->id);
});
