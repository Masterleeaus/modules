<?php

use App\Events\InvoiceSent;
use App\Listeners\SendInvoiceNotification;
use App\Mail\InvoiceMail;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\User;
use App\Services\SmsService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

function makeInvoiceFakeSms(array &$sent): SmsService
{
    return new class($sent) implements SmsService {
        public function __construct(private array &$sent) {}
        public function send(string $to, string $message): void { $this->sent[] = compact('to', 'message'); }
    };
}

function invoiceNotificationSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id, 'email' => 'customer@example.com']);

    return [$user, $org, $customer];
}

// ── Event dispatch ─────────────────────────────────────────────────────────────

test('sending an invoice dispatches the InvoiceSent event', function () {
    Event::fake();

    [$user, , $customer] = invoiceNotificationSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->draft()->create();

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/send")
        ->assertRedirect();

    Event::assertDispatched(InvoiceSent::class, fn ($e) => $e->invoice->id === $invoice->id);
});

// ── Listener: email ────────────────────────────────────────────────────────────

test('SendInvoiceNotification queues email to customer', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id, 'email' => 'customer@example.com']);
    $invoice  = Invoice::factory()->forCustomer($customer)->sent()->create(['balance_due' => 200]);

    $sent     = [];
    $listener = new SendInvoiceNotification(makeInvoiceFakeSms($sent));
    $listener->handle(new InvoiceSent($invoice));

    Mail::assertQueued(InvoiceMail::class, fn ($mail) => $mail->invoice->id === $invoice->id);
});

test('SendInvoiceNotification skips email when customer has no email', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id, 'email' => null]);
    $invoice  = Invoice::factory()->forCustomer($customer)->sent()->create();

    $sent     = [];
    $listener = new SendInvoiceNotification(makeInvoiceFakeSms($sent));
    $listener->handle(new InvoiceSent($invoice));

    Mail::assertNothingQueued();
});

// ── Listener: SMS ──────────────────────────────────────────────────────────────

test('SendInvoiceNotification sends SMS to customer mobile', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => '+15550001234',
        'phone'           => '+15559999999',
    ]);
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create([
        'balance_due' => 150,
    ]);

    $sent     = [];
    $listener = new SendInvoiceNotification(makeInvoiceFakeSms($sent));
    $listener->handle(new InvoiceSent($invoice));

    expect($sent)->toHaveCount(1);
    expect($sent[0]['to'])->toBe('+15550001234');
    expect($sent[0]['message'])->toContain($invoice->invoice_number);
});

test('SendInvoiceNotification falls back to phone when mobile is null', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => null,
        'phone'           => '+15558887777',
    ]);
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create();

    $sent     = [];
    $listener = new SendInvoiceNotification(makeInvoiceFakeSms($sent));
    $listener->handle(new InvoiceSent($invoice));

    expect($sent)->toHaveCount(1);
    expect($sent[0]['to'])->toBe('+15558887777');
});

test('SendInvoiceNotification skips SMS when customer has no phone', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => null,
        'phone'           => null,
    ]);
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create();

    $sent     = [];
    $listener = new SendInvoiceNotification(makeInvoiceFakeSms($sent));
    $listener->handle(new InvoiceSent($invoice));

    expect($sent)->toBeEmpty();
});

test('SendInvoiceNotification listener is queued', function () {
    $sent = [];

    expect(new SendInvoiceNotification(makeInvoiceFakeSms($sent)))
        ->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});
