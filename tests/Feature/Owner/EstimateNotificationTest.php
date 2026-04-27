<?php

use App\Events\EstimateSent;
use App\Listeners\SendEstimateNotification;
use App\Mail\EstimateMail;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Organization;
use App\Models\User;
use App\Services\SmsService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

// Helper: build a fake SmsService and capture sent messages
function makeFakeSms(array &$sent): SmsService
{
    return new class($sent) implements SmsService {
        public function __construct(private array &$sent) {}
        public function send(string $to, string $message): void { $this->sent[] = compact('to', 'message'); }
    };
}

// ── Event dispatch ──────────────────────────────────────────────────────────────

test('sending estimate dispatches EstimateSent event', function () {
    Event::fake();
    (new RolesAndPermissionsSeeder)->run();

    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);
    $estimate = Estimate::factory()->forCustomer($customer)->draft()->create();

    $this->actingAs($user)
        ->post("/owner/estimates/{$estimate->id}/send")
        ->assertRedirect();

    Event::assertDispatched(EstimateSent::class, fn ($e) => $e->estimate->id === $estimate->id);
});

// ── Email ────────────────────────────────────────────────────────────────────────

test('SendEstimateNotification sends email when customer has email', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'email'           => 'customer@example.com',
    ]);
    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create();

    $sent     = [];
    $listener = new SendEstimateNotification(makeFakeSms($sent));
    $listener->handle(new EstimateSent($estimate));

    Mail::assertSent(EstimateMail::class, fn ($mail) => $mail->hasTo('customer@example.com'));
});

test('SendEstimateNotification skips email when customer has no email', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'email'           => null,
    ]);
    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create();

    $sent     = [];
    $listener = new SendEstimateNotification(makeFakeSms($sent));
    $listener->handle(new EstimateSent($estimate));

    Mail::assertNothingSent();
});

// ── SMS ──────────────────────────────────────────────────────────────────────────

test('SendEstimateNotification sends SMS to customer mobile', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => '+15550001234',
        'phone'           => '+15559999999',
        'email'           => null,
    ]);
    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create(['title' => 'HVAC Service']);

    $sent     = [];
    $listener = new SendEstimateNotification(makeFakeSms($sent));
    $listener->handle(new EstimateSent($estimate));

    expect($sent)->toHaveCount(1);
    expect($sent[0]['to'])->toBe('+15550001234');
    expect($sent[0]['message'])->toContain('HVAC Service');
});

test('SendEstimateNotification falls back to phone when mobile is null', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => null,
        'phone'           => '+15558887777',
        'email'           => null,
    ]);
    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create();

    $sent     = [];
    $listener = new SendEstimateNotification(makeFakeSms($sent));
    $listener->handle(new EstimateSent($estimate));

    expect($sent)->toHaveCount(1);
    expect($sent[0]['to'])->toBe('+15558887777');
});

test('SendEstimateNotification skips SMS when customer has no phone', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => null,
        'phone'           => null,
        'email'           => null,
    ]);
    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create();

    $sent     = [];
    $listener = new SendEstimateNotification(makeFakeSms($sent));
    $listener->handle(new EstimateSent($estimate));

    expect($sent)->toBeEmpty();
});

// ── Listener contract ──────────────────────────────────────────────────────────

test('SendEstimateNotification listener is queued', function () {
    $sent = [];
    expect(new SendEstimateNotification(makeFakeSms($sent)))
        ->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});
