<?php

/**
 * Unit tests for Job Action classes.
 *
 * These tests run against the in-memory SQLite database and exercise
 * the action classes independently of the HTTP / Filament layers.
 */

use App\Actions\Jobs\AssignTechnicianAction;
use App\Actions\Jobs\CreateJobAction;
use App\Actions\Jobs\UpdateJobStatusAction;
use App\Events\JobCreated;
use App\Events\JobStatusChanged;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Event;

// ── CreateJobAction ───────────────────────────────────────────────────────────

test('CreateJobAction creates a job with scheduled status and dispatches JobCreated', function () {
    Event::fake([JobCreated::class]);

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    $job = app(CreateJobAction::class)->execute([
        'organization_id' => $org->id,
        'customer_id'     => $customer->id,
        'title'           => 'Test Job',
    ]);

    expect($job)->toBeInstanceOf(Job::class)
        ->and($job->status)->toBe(Job::STATUS_SCHEDULED)
        ->and($job->title)->toBe('Test Job')
        ->and($job->organization_id)->toBe($org->id);

    Event::assertDispatched(JobCreated::class, fn ($e) => $e->job->id === $job->id);
});

// ── UpdateJobStatusAction ─────────────────────────────────────────────────────

test('UpdateJobStatusAction transitions status and stamps completed_at', function () {
    Event::fake([JobStatusChanged::class]);

    $job = Job::factory()->create(['status' => Job::STATUS_IN_PROGRESS]);

    $updated = app(UpdateJobStatusAction::class)->execute($job, Job::STATUS_COMPLETED);

    expect($updated->status)->toBe(Job::STATUS_COMPLETED)
        ->and($updated->completed_at)->not->toBeNull();

    Event::assertDispatched(JobStatusChanged::class, function ($e) use ($job) {
        return $e->job->id === $job->id
            && $e->oldStatus === Job::STATUS_IN_PROGRESS
            && $e->newStatus === Job::STATUS_COMPLETED;
    });
});

test('UpdateJobStatusAction stamps started_at when transitioning to in_progress', function () {
    Event::fake([JobStatusChanged::class]);

    $job     = Job::factory()->create(['status' => Job::STATUS_SCHEDULED]);
    $updated = app(UpdateJobStatusAction::class)->execute($job, Job::STATUS_IN_PROGRESS);

    expect($updated->status)->toBe(Job::STATUS_IN_PROGRESS)
        ->and($updated->started_at)->not->toBeNull();
});

test('UpdateJobStatusAction stamps cancelled_at when cancelling', function () {
    Event::fake([JobStatusChanged::class]);

    $job     = Job::factory()->create(['status' => Job::STATUS_SCHEDULED]);
    $updated = app(UpdateJobStatusAction::class)->execute($job, Job::STATUS_CANCELLED);

    expect($updated->status)->toBe(Job::STATUS_CANCELLED)
        ->and($updated->cancelled_at)->not->toBeNull();
});

// ── AssignTechnicianAction ────────────────────────────────────────────────────

test('AssignTechnicianAction assigns a user to a job', function () {
    (new RolesAndPermissionsSeeder)->run();

    $org        = Organization::factory()->create();
    $tech       = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    $job        = Job::factory()->create(['organization_id' => $org->id, 'assigned_to' => null]);

    $updated = app(AssignTechnicianAction::class)->execute($job, $tech->id);

    expect($updated->assigned_to)->toBe($tech->id);
});

test('AssignTechnicianAction can unassign by passing null', function () {
    (new RolesAndPermissionsSeeder)->run();

    $org  = Organization::factory()->create();
    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    $job  = Job::factory()->create(['organization_id' => $org->id, 'assigned_to' => $tech->id]);

    $updated = app(AssignTechnicianAction::class)->execute($job, null);

    expect($updated->assigned_to)->toBeNull();
});
