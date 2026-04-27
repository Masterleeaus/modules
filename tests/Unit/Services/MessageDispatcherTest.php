<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\JobMessage;
use App\Models\Organization;
use App\Services\MessageDispatcher;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeDispatchJob(): Job
{
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'email'           => 'customer@example.com',
    ]);

    return Job::factory()->forCustomer($customer)->create([
        'title' => 'Pump Repair',
    ]);
}

function makeFakeSmsDispatcher(): array
{
    $sent = [];
    $sms = new class($sent) implements SmsService {
        public function __construct(private array &$sent) {}
        public function send(string $to, string $message): void { $this->sent[] = compact('to', 'message'); }
        public function getSent(): array { return $this->sent; }
    };
    return [$sms, &$sent];
}

// ── sendEmail ─────────────────────────────────────────────────────────────────

test('sendEmail logs a sent JobMessage record', function () {
    Mail::fake();

    $job        = makeDispatchJob();
    [$sms]      = makeFakeSmsDispatcher();
    $dispatcher = new MessageDispatcher($sms);

    $dispatcher->sendEmail($job, 'job_scheduled', 'customer@example.com', 'Subject', 'emails.test');

    $message = JobMessage::where('job_id', $job->id)->first();
    expect($message)->not->toBeNull();
    expect($message->channel)->toBe('email');
    expect($message->event)->toBe('job_scheduled');
    expect($message->status)->toBe('sent');
    expect($message->recipient)->toBe('customer@example.com');
});

test('sendEmail logs a failed JobMessage when mail throws', function () {
    Mail::shouldReceive('send')->andThrow(new \Exception('SMTP error'));
    Log::shouldReceive('warning')->once();

    $job        = makeDispatchJob();
    [$sms]      = makeFakeSmsDispatcher();
    $dispatcher = new MessageDispatcher($sms);

    $dispatcher->sendEmail($job, 'job_scheduled', 'fail@example.com', 'Subject', 'emails.test');

    $message = JobMessage::where('job_id', $job->id)->first();
    expect($message->status)->toBe('failed');
    expect($message->error)->not->toBeNull();
});

// ── sendSms ───────────────────────────────────────────────────────────────────

test('sendSms routes through the SmsService', function () {
    $sent       = [];
    $sms        = new class($sent) implements SmsService {
        public function __construct(private array &$sent) {}
        public function send(string $to, string $message): void { $this->sent[] = compact('to', 'message'); }
        public function getSent(): array { return $this->sent; }
    };
    $dispatcher = new MessageDispatcher($sms);
    $job        = makeDispatchJob();

    $dispatcher->sendSms($job, 'en_route', '+15551234567', 'Technician is on the way!');

    expect($sms->getSent())->toHaveCount(1);
    expect($sms->getSent()[0]['to'])->toBe('+15551234567');
    expect($sms->getSent()[0]['message'])->toBe('Technician is on the way!');
});

test('sendSms logs a sent JobMessage record', function () {
    [$sms]      = makeFakeSmsDispatcher();
    $dispatcher = new MessageDispatcher($sms);
    $job        = makeDispatchJob();

    $dispatcher->sendSms($job, 'en_route', '+15551234567', 'On the way');

    $message = JobMessage::where('job_id', $job->id)->first();
    expect($message)->not->toBeNull();
    expect($message->channel)->toBe('sms');
    expect($message->event)->toBe('en_route');
    expect($message->status)->toBe('sent');
});

test('sendSms logs a failed JobMessage when sms service throws', function () {
    $failingSms = new class implements SmsService {
        public function send(string $to, string $message): void
        {
            throw new \RuntimeException('Twilio error');
        }
    };

    Log::shouldReceive('warning')->once();

    $dispatcher = new MessageDispatcher($failingSms);
    $job        = makeDispatchJob();

    $dispatcher->sendSms($job, 'en_route', '+15551234567', 'On the way');

    $message = JobMessage::where('job_id', $job->id)->first();
    expect($message->status)->toBe('failed');
    expect($message->error)->toContain('Twilio error');
});
