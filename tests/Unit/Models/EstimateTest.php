<?php

use App\Models\Customer;
use App\Models\Estimate;
use App\Models\EstimatePackage;
use App\Models\Organization;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeEstimateSetup(): array
{
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$org, $customer];
}

// ── Constants ─────────────────────────────────────────────────────────────────

test('estimate has expected status constants', function () {
    expect(Estimate::STATUS_DRAFT)->toBe('draft');
    expect(Estimate::STATUS_SENT)->toBe('sent');
    expect(Estimate::STATUS_ACCEPTED)->toBe('accepted');
    expect(Estimate::STATUS_DECLINED)->toBe('declined');
    expect(Estimate::STATUS_EXPIRED)->toBe('expired');
});

test('estimate defines tier constants', function () {
    expect(Estimate::TIERS)->toBe(['good', 'better', 'best']);
});

test('statuses() returns all five statuses', function () {
    expect(Estimate::statuses())->toHaveCount(5);
});

// ── isExpired() ───────────────────────────────────────────────────────────────

test('isExpired returns false when status is draft', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->draft()->create([
        'expires_at' => now()->subDay(),
    ]);

    expect($estimate->isExpired())->toBeFalse();
});

test('isExpired returns false when expires_at is in the future', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create([
        'expires_at' => now()->addWeek(),
    ]);

    expect($estimate->isExpired())->toBeFalse();
});

test('isExpired returns true when sent and expires_at is past', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create([
        'expires_at' => now()->subDay(),
    ]);

    expect($estimate->isExpired())->toBeTrue();
});

test('isExpired returns false when no expires_at is set', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create([
        'expires_at' => null,
    ]);

    expect($estimate->isExpired())->toBeFalse();
});

// ── Token generation ──────────────────────────────────────────────────────────

test('estimate token is auto-generated on create', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->create();

    expect($estimate->token)->not->toBeNull();
    expect(strlen($estimate->token))->toBe(48);
});

test('estimate does not overwrite a pre-set token', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->create([
        'token' => 'preset-token-value',
    ]);

    expect($estimate->token)->toBe('preset-token-value');
});

// ── Relationships ─────────────────────────────────────────────────────────────

test('estimate belongs to a customer', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->create();

    expect($estimate->customer)->toBeInstanceOf(Customer::class);
    expect($estimate->customer->id)->toBe($customer->id);
});

test('estimate belongs to an organization', function () {
    [$org, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->create();

    expect($estimate->organization)->toBeInstanceOf(Organization::class);
    expect($estimate->organization->id)->toBe($org->id);
});

test('estimate has many packages', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->create();

    $estimate->packages()->create(['tier' => 'good',   'label' => 'Basic',   'subtotal' => 100, 'tax_amount' => 0, 'total' => 100]);
    $estimate->packages()->create(['tier' => 'better', 'label' => 'Standard','subtotal' => 200, 'tax_amount' => 0, 'total' => 200]);

    expect($estimate->packages)->toHaveCount(2);
});

test('packages are ordered good then better then best', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->create();

    $estimate->packages()->create(['tier' => 'best',   'label' => 'Premium', 'subtotal' => 300, 'tax_amount' => 0, 'total' => 300]);
    $estimate->packages()->create(['tier' => 'good',   'label' => 'Basic',   'subtotal' => 100, 'tax_amount' => 0, 'total' => 100]);
    $estimate->packages()->create(['tier' => 'better', 'label' => 'Standard','subtotal' => 200, 'tax_amount' => 0, 'total' => 200]);

    $tiers = $estimate->fresh()->packages->pluck('tier')->toArray();

    expect($tiers)->toBe(['good', 'better', 'best']);
});

// ── Factory states ────────────────────────────────────────────────────────────

test('draft factory state sets status to draft', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->draft()->create();

    expect($estimate->status)->toBe(Estimate::STATUS_DRAFT);
});

test('sent factory state sets status to sent and sets sent_at', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create();

    expect($estimate->status)->toBe(Estimate::STATUS_SENT);
    expect($estimate->sent_at)->not->toBeNull();
});

test('accepted factory state sets status to accepted', function () {
    [, $customer] = makeEstimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->accepted()->create();

    expect($estimate->status)->toBe(Estimate::STATUS_ACCEPTED);
});
