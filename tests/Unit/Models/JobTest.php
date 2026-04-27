<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\JobChecklistItem;
use App\Models\JobLineItem;
use App\Models\JobType;
use App\Models\JobTypeChecklistItem;
use App\Models\Organization;
use App\Models\User;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeOrgAndCustomer(): array
{
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$org, $customer];
}

// ── Constants ─────────────────────────────────────────────────────────────────

test('job has expected status constants', function () {
    expect(Job::STATUS_SCHEDULED)->toBe('scheduled');
    expect(Job::STATUS_EN_ROUTE)->toBe('en_route');
    expect(Job::STATUS_IN_PROGRESS)->toBe('in_progress');
    expect(Job::STATUS_COMPLETED)->toBe('completed');
    expect(Job::STATUS_CANCELLED)->toBe('cancelled');
    expect(Job::STATUS_ON_HOLD)->toBe('on_hold');
});

test('statuses() returns all statuses', function () {
    $statuses = Job::statuses();

    expect($statuses)->toHaveCount(6);
    expect($statuses)->toHaveKey('scheduled');
    expect($statuses)->toHaveKey('completed');
    expect($statuses)->toHaveKey('cancelled');
});

// ── isCompleted() / isCancelled() ─────────────────────────────────────────────

test('isCompleted returns true for completed jobs', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->completed()->create();

    expect($job->isCompleted())->toBeTrue();
});

test('isCompleted returns false for non-completed jobs', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    expect($job->isCompleted())->toBeFalse();
});

test('isCancelled returns true for cancelled jobs', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->create(['status' => Job::STATUS_CANCELLED]);

    expect($job->isCancelled())->toBeTrue();
});

test('isCancelled returns false for non-cancelled jobs', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    expect($job->isCancelled())->toBeFalse();
});

// ── Relationships ─────────────────────────────────────────────────────────────

test('job belongs to a customer', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    expect($job->customer)->toBeInstanceOf(Customer::class);
    expect($job->customer->id)->toBe($customer->id);
});

test('job belongs to an organization', function () {
    [$org, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    expect($job->organization)->toBeInstanceOf(Organization::class);
    expect($job->organization->id)->toBe($org->id);
});

test('job has many line items', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    JobLineItem::factory()->create(['job_id' => $job->id, 'sort_order' => 0]);
    JobLineItem::factory()->create(['job_id' => $job->id, 'sort_order' => 1]);

    expect($job->lineItems)->toHaveCount(2);
});

test('line items are ordered by sort_order', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    JobLineItem::factory()->create(['job_id' => $job->id, 'sort_order' => 2, 'name' => 'Second']);
    JobLineItem::factory()->create(['job_id' => $job->id, 'sort_order' => 0, 'name' => 'First']);

    expect($job->lineItems->first()->name)->toBe('First');
});

test('job has many checklist items', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    $job->checklistItems()->create(['label' => 'Step 1', 'sort_order' => 0, 'is_required' => false]);
    $job->checklistItems()->create(['label' => 'Step 2', 'sort_order' => 1, 'is_required' => true]);

    expect($job->checklistItems)->toHaveCount(2);
});

// ── Checklist auto-copy from job type ────────────────────────────────────────

test('creating a job with a job_type_id copies checklist items from the template', function () {
    [$org, $customer] = makeOrgAndCustomer();
    $jobType = JobType::factory()->create(['organization_id' => $org->id]);

    JobTypeChecklistItem::create(['job_type_id' => $jobType->id, 'label' => 'Check A', 'sort_order' => 0, 'is_required' => false]);
    JobTypeChecklistItem::create(['job_type_id' => $jobType->id, 'label' => 'Check B', 'sort_order' => 1, 'is_required' => true]);

    $job = Job::factory()->forCustomer($customer)->create(['job_type_id' => $jobType->id]);

    expect($job->checklistItems)->toHaveCount(2);
    expect($job->checklistItems->first()->label)->toBe('Check A');
});

test('creating a job without a job_type_id does not copy checklist items', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->create(['job_type_id' => null]);

    expect($job->checklistItems)->toBeEmpty();
});

// ── Timestamps from factories ─────────────────────────────────────────────────

test('completed factory state sets status to completed', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->completed()->create();

    expect($job->status)->toBe(Job::STATUS_COMPLETED);
});

test('scheduled factory state sets status to scheduled', function () {
    [, $customer] = makeOrgAndCustomer();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    expect($job->status)->toBe(Job::STATUS_SCHEDULED);
});
