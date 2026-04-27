<?php

/**
 * Unit tests for Estimate Action classes.
 */

use App\Actions\Estimates\ConvertEstimateToJobAction;
use App\Actions\Estimates\SendEstimateAction;
use App\Events\EstimateSent;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\EstimatePackage;
use App\Models\Job;
use App\Models\Organization;
use Illuminate\Support\Facades\Event;

// ── SendEstimateAction ────────────────────────────────────────────────────────

test('SendEstimateAction marks estimate as sent and dispatches EstimateSent event', function () {
    Event::fake([EstimateSent::class]);

    $estimate = Estimate::factory()->draft()->create();

    $updated = app(SendEstimateAction::class)->execute($estimate);

    expect($updated->status)->toBe(Estimate::STATUS_SENT)
        ->and($updated->sent_at)->not->toBeNull();

    Event::assertDispatched(EstimateSent::class, fn ($e) => $e->estimate->id === $estimate->id);
});

// ── ConvertEstimateToJobAction ────────────────────────────────────────────────

test('ConvertEstimateToJobAction creates a scheduled job from an accepted estimate', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    $estimate = Estimate::factory()->accepted('good')->forCustomer($customer)->create([
        'title'  => 'Pool Service',
        'footer' => 'Office note',
    ]);

    $package = EstimatePackage::create([
        'estimate_id'    => $estimate->id,
        'tier'           => 'good',
        'label'          => 'Standard',
        'description'    => 'Full service',
        'is_recommended' => false,
        'subtotal'       => 200,
        'tax_amount'     => 0,
        'total'          => 200,
    ]);

    $package->lineItems()->create([
        'name'       => 'Pool Clean',
        'unit_price' => 200,
        'quantity'   => 1,
        'sort_order' => 0,
    ]);

    $job = app(ConvertEstimateToJobAction::class)->execute($estimate);

    expect($job)->toBeInstanceOf(Job::class)
        ->and($job->status)->toBe(Job::STATUS_SCHEDULED)
        ->and($job->estimate_id)->toBe($estimate->id)
        ->and($job->title)->toBe('Pool Service')
        ->and($job->office_notes)->toBe('Office note')
        ->and($job->lineItems()->count())->toBe(1);
});

test('ConvertEstimateToJobAction throws when estimate has no packages', function () {
    $estimate = Estimate::factory()->accepted()->create();

    expect(fn () => app(ConvertEstimateToJobAction::class)->execute($estimate))
        ->toThrow(\RuntimeException::class);
});
