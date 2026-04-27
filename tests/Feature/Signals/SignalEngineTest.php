<?php

/**
 * Feature tests for the Signal Engine intake → dispatch pipeline.
 *
 * Covers:
 *   - Signal intake via SignalRouter
 *   - Validation (valid payload, missing required fields, unknown type)
 *   - Governance (auto-approve, require_review, auto_reject)
 *   - Dispatch to handlers + dispatch log
 *   - Dead-letter recording for handler failures
 *   - Migration of JobCreated, JobStatusChanged, PaymentReceived
 */

use App\Events\JobCreated;
use App\Events\JobStatusChanged;
use App\Events\PaymentReceived;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Signal;
use App\Models\SignalDispatchLog;
use App\Signals\SignalDispatcher;
use App\Signals\SignalGovernor;
use App\Signals\SignalRegistry;
use App\Signals\SignalRouter;
use App\Signals\SignalValidator;
use Illuminate\Support\Facades\Event;

// Clear registered handlers before every test so each test is fully isolated.
beforeEach(function () {
    SignalDispatcher::clearHandlers();
});

// ── Helpers ───────────────────────────────────────────────────────────────────

function signalEngineSetup(): array
{
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$org, $customer];
}

// ── SignalRegistry ────────────────────────────────────────────────────────────

test('SignalRegistry discovers the core signal contracts', function () {
    $registry = app(SignalRegistry::class);

    expect($registry->getContract('job.created'))->toBeArray()
        ->and($registry->getContract('job.status_changed'))->toBeArray()
        ->and($registry->getContract('payment.received'))->toBeArray();
});

test('SignalRegistry returns null for an unknown signal type', function () {
    $registry = app(SignalRegistry::class);

    expect($registry->getContract('unknown.signal.type'))->toBeNull();
});

// ── SignalValidator ───────────────────────────────────────────────────────────

test('SignalValidator accepts a valid job.created payload', function () {
    /** @var \App\Models\Signal $signal */
    $signal = Signal::make([
        'source'  => 'internal',
        'type'    => 'job.created',
        'payload' => ['job_id' => 1],
        'status'  => Signal::STATUS_PENDING,
    ]);

    $errors = app(SignalValidator::class)->validate($signal);

    expect($errors)->toBeEmpty();
});

test('SignalValidator rejects a payload missing required fields', function () {
    $signal = Signal::make([
        'source'  => 'internal',
        'type'    => 'job.created',
        'payload' => [],   // job_id missing
        'status'  => Signal::STATUS_PENDING,
    ]);

    $errors = app(SignalValidator::class)->validate($signal);

    expect($errors)->not->toBeEmpty()
        ->and($errors[0])->toContain('job_id');
});

test('SignalValidator rejects an unknown signal type', function () {
    $signal = Signal::make([
        'source'  => 'internal',
        'type'    => 'totally.unknown',
        'payload' => [],
        'status'  => Signal::STATUS_PENDING,
    ]);

    $errors = app(SignalValidator::class)->validate($signal);

    expect($errors)->not->toBeEmpty()
        ->and($errors[0])->toContain('Unknown signal type');
});

// ── SignalGovernor ────────────────────────────────────────────────────────────

test('SignalGovernor auto-approves signals by default', function () {
    $signal = Signal::make([
        'source'  => 'internal',
        'type'    => 'job.created',
        'payload' => ['job_id' => 1],
        'status'  => Signal::STATUS_PENDING,
    ]);

    $decision = app(SignalGovernor::class)->evaluate($signal);

    expect($decision)->toBe('approved');
});

test('SignalGovernor honours require_review rule from config', function () {
    config(['signals.approval_rules.job.created' => 'require_review']);

    $signal = Signal::make([
        'source'  => 'internal',
        'type'    => 'job.created',
        'payload' => ['job_id' => 1],
        'status'  => Signal::STATUS_PENDING,
    ]);

    $decision = app(SignalGovernor::class)->evaluate($signal);

    expect($decision)->toBe('pending');

    // Restore default
    config(['signals.approval_rules.job.created' => null]);
});

test('SignalGovernor honours auto_reject rule from config', function () {
    config(['signals.approval_rules.job.created' => 'auto_reject']);

    $signal = Signal::make([
        'source'  => 'internal',
        'type'    => 'job.created',
        'payload' => ['job_id' => 1],
        'status'  => Signal::STATUS_PENDING,
    ]);

    $decision = app(SignalGovernor::class)->evaluate($signal);

    expect($decision)->toBe('rejected');

    config(['signals.approval_rules.job.created' => null]);
});

// ── SignalRouter — happy path ─────────────────────────────────────────────────

test('SignalRouter persists and auto-approves an internal signal', function () {
    [$org] = signalEngineSetup();

    $dispatched = [];
    SignalDispatcher::registerHandler('job.created', function (Signal $s) use (&$dispatched) {
        $dispatched[] = $s->type;
    });

    $router = app(SignalRouter::class);
    $signal = $router->route('internal', 'job.created', ['job_id' => 1], $org->id);

    expect($signal->status)->toBe(Signal::STATUS_DISPATCHED)
        ->and($signal->source)->toBe('internal')
        ->and($signal->type)->toBe('job.created')
        ->and(Signal::count())->toBe(1)
        ->and($dispatched)->toBe(['job.created']);

    $log = SignalDispatchLog::first();
    expect($log)->not->toBeNull()
        ->and($log->result)->toBe(SignalDispatchLog::RESULT_SUCCESS);
});

test('SignalRouter marks signal as failed when payload is invalid', function () {
    [$org] = signalEngineSetup();

    $router = app(SignalRouter::class);
    $signal = $router->route('internal', 'job.created', [], $org->id); // missing job_id

    expect($signal->status)->toBe(Signal::STATUS_FAILED)
        ->and(SignalDispatchLog::count())->toBe(0);
});

test('SignalRouter rejects signal when governor says auto_reject', function () {
    [$org] = signalEngineSetup();

    config(['signals.approval_rules.job.created' => 'auto_reject']);

    $router = app(SignalRouter::class);
    $signal = $router->route('internal', 'job.created', ['job_id' => 1], $org->id);

    expect($signal->status)->toBe(Signal::STATUS_REJECTED);

    config(['signals.approval_rules.job.created' => null]);
});

test('SignalRouter leaves signal pending when governor requires review', function () {
    [$org] = signalEngineSetup();

    config(['signals.approval_rules.job.created' => 'require_review']);

    $router = app(SignalRouter::class);
    $signal = $router->route('internal', 'job.created', ['job_id' => 1], $org->id);

    expect($signal->status)->toBe(Signal::STATUS_PENDING);

    config(['signals.approval_rules.job.created' => null]);
});

// ── Dead-letter handling ──────────────────────────────────────────────────────

test('SignalDispatcher records failure in dispatch log when handler throws', function () {
    [$org] = signalEngineSetup();

    SignalDispatcher::registerHandler('job.created', function () {
        throw new \RuntimeException('Handler exploded');
    });

    $router = app(SignalRouter::class);
    $signal = $router->route('internal', 'job.created', ['job_id' => 1], $org->id);

    expect($signal->status)->toBe(Signal::STATUS_FAILED);

    $log = SignalDispatchLog::first();
    expect($log)->not->toBeNull()
        ->and($log->result)->toBe(SignalDispatchLog::RESULT_FAILURE)
        ->and($log->last_error)->toContain('Handler exploded');
});

// ── Migrated events: job.created ─────────────────────────────────────────────

test('CreateJobAction routes job.created through Signal Engine and fires JobCreated event', function () {
    Event::fake();

    [$org, $customer] = signalEngineSetup();

    $action = new \App\Actions\Jobs\CreateJobAction();
    $job    = $action->execute([
        'organization_id' => $org->id,
        'customer_id'     => $customer->id,
        'title'           => 'Signal Engine Job',
        'scheduled_at'    => now()->addDay(),
    ]);

    // Signal Engine recorded the intake signal
    expect(Signal::where('type', 'job.created')->count())->toBe(1);

    $signal = Signal::where('type', 'job.created')->first();
    expect($signal->status)->toBe(Signal::STATUS_DISPATCHED)
        ->and($signal->payload['job_id'])->toBe($job->id);

    // JobCreated is dispatched directly by the action (not via handler)
    Event::assertDispatched(JobCreated::class, fn ($e) => $e->job->id === $job->id);
});

// ── Migrated events: job.status_changed ──────────────────────────────────────

test('UpdateJobStatusAction routes job.status_changed through Signal Engine and fires JobStatusChanged event', function () {
    Event::fake();

    [$org, $customer] = signalEngineSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'organization_id' => $org->id,
        'status'          => Job::STATUS_SCHEDULED,
    ]);

    $action = new \App\Actions\Jobs\UpdateJobStatusAction();
    $action->execute($job, Job::STATUS_COMPLETED);

    // Signal Engine recorded the intake signal
    expect(Signal::where('type', 'job.status_changed')->count())->toBe(1);

    $signal = Signal::where('type', 'job.status_changed')->first();
    expect($signal->status)->toBe(Signal::STATUS_DISPATCHED)
        ->and($signal->payload['old_status'])->toBe(Job::STATUS_SCHEDULED)
        ->and($signal->payload['new_status'])->toBe(Job::STATUS_COMPLETED);

    // JobStatusChanged is dispatched directly by the action
    Event::assertDispatched(
        JobStatusChanged::class,
        fn ($e) => $e->oldStatus === Job::STATUS_SCHEDULED && $e->newStatus === Job::STATUS_COMPLETED,
    );
});

// ── Migrated events: payment.received ────────────────────────────────────────

test('RecordPaymentAction routes payment.received through Signal Engine and fires PaymentReceived event', function () {
    Event::fake();

    [$org, $customer] = signalEngineSetup();

    $invoice = Invoice::factory()->forCustomer($customer)->create([
        'total'       => '200.00',
        'amount_paid' => '0.00',
        'balance_due' => '200.00',
        'status'      => Invoice::STATUS_SENT,
    ]);

    $action  = new \App\Actions\Invoices\RecordPaymentAction();
    $payment = $action->execute($invoice, [
        'amount'  => 200.00,
        'method'  => 'cash',
        'paid_at' => today()->toDateString(),
    ]);

    // Signal Engine recorded the intake signal
    expect(Signal::where('type', 'payment.received')->count())->toBe(1);

    $signal = Signal::where('type', 'payment.received')->first();
    expect($signal->status)->toBe(Signal::STATUS_DISPATCHED)
        ->and($signal->payload['payment_id'])->toBe($payment->id)
        ->and($signal->payload['amount'])->toBe(200.0);

    // PaymentReceived is dispatched directly by the action
    Event::assertDispatched(PaymentReceived::class, fn ($e) => $e->payment->id === $payment->id);
});

// ── Multi-handler dispatch ────────────────────────────────────────────────────

test('SignalDispatcher calls all registered handlers for a signal type', function () {
    [$org] = signalEngineSetup();

    $calls = [];
    SignalDispatcher::registerHandler('job.created', function () use (&$calls) { $calls[] = 'handler-1'; });
    SignalDispatcher::registerHandler('job.created', function () use (&$calls) { $calls[] = 'handler-2'; });

    app(SignalRouter::class)->route('internal', 'job.created', ['job_id' => 1], $org->id);

    expect($calls)->toBe(['handler-1', 'handler-2'])
        ->and(SignalDispatchLog::count())->toBe(2);
});
