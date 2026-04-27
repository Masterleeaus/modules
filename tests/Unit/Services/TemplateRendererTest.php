<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\MessageTemplate;
use App\Models\Organization;
use App\Services\TemplateRenderer;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeJob(array $attrs = []): Job
{
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'first_name'      => 'Alice',
        'last_name'       => 'Walker',
    ]);

    return Job::factory()->forCustomer($customer)->create(array_merge([
        'title'        => 'HVAC Service',
        'scheduled_at' => '2026-05-15 09:00:00',
    ], $attrs));
}

// ── variables() ───────────────────────────────────────────────────────────────

test('variables returns expected keys', function () {
    $job      = makeJob();
    $renderer = new TemplateRenderer;
    $vars     = $renderer->variables($job);

    expect($vars)->toHaveKey('{{customer_name}}');
    expect($vars)->toHaveKey('{{job_title}}');
    expect($vars)->toHaveKey('{{job_date}}');
    expect($vars)->toHaveKey('{{technician_name}}');
    expect($vars)->toHaveKey('{{company_name}}');
});

test('variables substitutes the customer full name', function () {
    $job      = makeJob();
    $renderer = new TemplateRenderer;
    $vars     = $renderer->variables($job);

    expect($vars['{{customer_name}}'])->toBe('Alice Walker');
});

test('variables falls back to valued customer when no customer is set', function () {
    // Since customer_id is NOT NULL in the schema, we test the eager-loaded customer being null
    // by calling variables() on a job and manually unsetting the customer relationship.
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);
    $job      = Job::factory()->forCustomer($customer)->create(['title' => 'General Service']);

    // Simulate a job whose customer relationship is null (e.g., customer was deleted)
    $job->setRelation('customer', null);

    $vars = (new TemplateRenderer)->variables($job);

    expect($vars['{{customer_name}}'])->toBe('Valued Customer');
});

test('variables falls back to your technician when no technician is assigned', function () {
    $job  = makeJob(['assigned_to' => null]);
    $vars = (new TemplateRenderer)->variables($job);

    expect($vars['{{technician_name}}'])->toBe('Your technician');
});

test('variables formats the scheduled date correctly', function () {
    $job  = makeJob(['scheduled_at' => '2026-05-15 09:00:00']);
    $vars = (new TemplateRenderer)->variables($job);

    expect($vars['{{job_date}}'])->toBe('Friday, May 15 at 9:00 AM');
});

test('variables returns TBD when scheduled_at is null', function () {
    $job  = makeJob(['scheduled_at' => null]);
    $vars = (new TemplateRenderer)->variables($job);

    expect($vars['{{job_date}}'])->toBe('TBD');
});

// ── render() — hardcoded defaults ────────────────────────────────────────────

test('render returns job_scheduled email default when no template exists', function () {
    $job    = makeJob();
    $result = (new TemplateRenderer)->render($job->organization_id, 'job_scheduled', 'email', $job);

    expect($result)->not->toBeNull();
    expect($result['subject'])->toContain('HVAC Service');
    expect($result['body'])->toContain('Alice Walker');
});

test('render returns job_scheduled sms default', function () {
    $job    = makeJob();
    $result = (new TemplateRenderer)->render($job->organization_id, 'job_scheduled', 'sms', $job);

    expect($result)->not->toBeNull();
    expect($result['body'])->toContain('HVAC Service');
});

test('render returns null for unknown event channel combination', function () {
    $job    = makeJob();
    $result = (new TemplateRenderer)->render($job->organization_id, 'unknown_event', 'sms', $job);

    expect($result)->toBeNull();
});

// ── render() — custom templates ───────────────────────────────────────────────

test('render uses custom template when one exists for the org', function () {
    $job = makeJob();

    MessageTemplate::create([
        'organization_id' => $job->organization_id,
        'event'           => 'job_scheduled',
        'channel'         => 'email',
        'subject'         => 'Custom: {{job_title}}',
        'body'            => 'Hello {{customer_name}}, custom body.',
        'is_active'       => true,
    ]);

    $result = (new TemplateRenderer)->render($job->organization_id, 'job_scheduled', 'email', $job);

    expect($result['subject'])->toBe('Custom: HVAC Service');
    expect($result['body'])->toBe('Hello Alice Walker, custom body.');
});

test('render ignores inactive templates and falls back to default', function () {
    $job = makeJob();

    MessageTemplate::create([
        'organization_id' => $job->organization_id,
        'event'           => 'job_scheduled',
        'channel'         => 'email',
        'subject'         => 'Inactive template',
        'body'            => 'Should not appear',
        'is_active'       => false,
    ]);

    $result = (new TemplateRenderer)->render($job->organization_id, 'job_scheduled', 'email', $job);

    expect($result['subject'])->not->toBe('Inactive template');
});

test('render defaults include job reminder email', function () {
    $job    = makeJob();
    $result = (new TemplateRenderer)->render($job->organization_id, 'job_reminder', 'email', $job);

    expect($result)->not->toBeNull();
    expect($result['subject'])->toContain('HVAC Service');
});

test('render defaults include en_route sms', function () {
    $job    = makeJob();
    $result = (new TemplateRenderer)->render($job->organization_id, 'en_route', 'sms', $job);

    expect($result)->not->toBeNull();
    expect($result['body'])->toContain('on the way');
});

test('render defaults include job_completed email', function () {
    $job    = makeJob();
    $result = (new TemplateRenderer)->render($job->organization_id, 'job_completed', 'email', $job);

    expect($result)->not->toBeNull();
    expect($result['subject'])->toContain('HVAC Service');
});
