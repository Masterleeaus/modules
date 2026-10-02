<?php

use App\Events\WorkflowStuck;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use App\Models\WorkflowTransition;
use App\Workflow\Exceptions\InvalidTransitionException;
use App\Workflow\StateMachine;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Event;

// ── Helpers ───────────────────────────────────────────────────────────────────

function workflowUser(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$user, $org, $customer];
}

// ── StateMachine unit tests ───────────────────────────────────────────────────

test('state machine can detect a legal transition', function () {
    [, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $machine = new StateMachine('job');

    expect($machine->can($job, 'dispatch'))->toBeTrue();
    expect($machine->can($job, 'complete'))->toBeFalse();
    expect($machine->can($job, 'arrive'))->toBeFalse();
});

test('state machine returns available transitions for a state', function () {
    [, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $machine = new StateMachine('job');

    $available = $machine->availableTransitions($job);
    expect($available)->toContain('dispatch');
    expect($available)->toContain('cancel');
    expect($available)->not->toContain('complete');
});

test('state machine applies a valid transition and records history', function () {
    [, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $machine = new StateMachine('job');
    $machine->apply($job, 'dispatch');

    expect($job->fresh()->status)->toBe(Job::STATUS_EN_ROUTE);

    $this->assertDatabaseHas('workflow_transitions', [
        'entity_type' => 'job',
        'entity_id'   => (string) $job->id,
        'from_state'  => Job::STATUS_SCHEDULED,
        'to_state'    => Job::STATUS_EN_ROUTE,
        'transition'  => 'dispatch',
    ]);
});

test('state machine throws InvalidTransitionException for illegal transition', function () {
    [, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->completed()->create();

    $machine = new StateMachine('job');

    expect(fn () => $machine->apply($job, 'dispatch'))
        ->toThrow(InvalidTransitionException::class);
});

test('state machine throws for an unknown transition name', function () {
    [, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $machine = new StateMachine('job');

    expect(fn () => $machine->apply($job, 'teleport'))
        ->toThrow(InvalidTransitionException::class);
});

// ── Owner HTTP tests (via UpdateJobStatusAction) ──────────────────────────────

test('owner can transition a scheduled job to en_route', function () {
    [$user, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_EN_ROUTE])
        ->assertRedirect();

    expect($job->fresh()->status)->toBe(Job::STATUS_EN_ROUTE);
});

test('owner can transition en_route to in_progress', function () {
    [$user, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->create(['status' => Job::STATUS_EN_ROUTE]);

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_IN_PROGRESS])
        ->assertRedirect();

    expect($job->fresh()->status)->toBe(Job::STATUS_IN_PROGRESS);
});

test('owner can complete an in_progress job and timestamps are set', function () {
    [$user, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->create(['status' => Job::STATUS_IN_PROGRESS]);

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_COMPLETED])
        ->assertRedirect();

    $fresh = $job->fresh();
    expect($fresh->status)->toBe(Job::STATUS_COMPLETED);
    expect($fresh->completed_at)->not->toBeNull();
});

test('owner cannot make an illegal transition (completed to en_route)', function () {
    [$user, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->completed()->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_EN_ROUTE])
        ->assertSessionHasErrors(['status']);

    expect($job->fresh()->status)->toBe(Job::STATUS_COMPLETED);
});

test('owner cannot make an illegal transition (scheduled to completed)', function () {
    [$user, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_COMPLETED])
        ->assertSessionHasErrors(['status']);

    expect($job->fresh()->status)->toBe(Job::STATUS_SCHEDULED);
});

test('owner can cancel a scheduled job', function () {
    [$user, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_CANCELLED])
        ->assertRedirect();

    expect($job->fresh()->status)->toBe(Job::STATUS_CANCELLED);
});

test('owner can hold an in_progress job', function () {
    [$user, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->create(['status' => Job::STATUS_IN_PROGRESS]);

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_ON_HOLD])
        ->assertRedirect();

    expect($job->fresh()->status)->toBe(Job::STATUS_ON_HOLD);
});

test('owner can resume an on_hold job', function () {
    [$user, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->create(['status' => Job::STATUS_ON_HOLD]);

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_IN_PROGRESS])
        ->assertRedirect();

    expect($job->fresh()->status)->toBe(Job::STATUS_IN_PROGRESS);
});

// ── History recording ─────────────────────────────────────────────────────────

test('every valid transition records a workflow_transitions row', function () {
    [$user, , $customer] = workflowUser();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_EN_ROUTE]);

    $this->assertDatabaseHas('workflow_transitions', [
        'entity_type' => 'job',
        'entity_id'   => (string) $job->id,
        'from_state'  => Job::STATUS_SCHEDULED,
        'to_state'    => Job::STATUS_EN_ROUTE,
        'transition'  => 'dispatch',
    ]);
});

// ── Stuck-state detection ─────────────────────────────────────────────────────

test('workflow:detect-stuck fires WorkflowStuck for jobs past the threshold', function () {
    Event::fake([WorkflowStuck::class]);

    [, , $customer] = workflowUser();
    Job::factory()->forCustomer($customer)->create([
        'status'     => Job::STATUS_IN_PROGRESS,
        'updated_at' => now()->subHours(30),
    ]);

    $this->artisan('workflow:detect-stuck')->assertExitCode(0);

    Event::assertDispatched(WorkflowStuck::class, function ($event) {
        return $event->entityType === 'job' && $event->state === Job::STATUS_IN_PROGRESS;
    });
});

test('workflow:detect-stuck does not fire for jobs within the threshold', function () {
    Event::fake([WorkflowStuck::class]);

    [, , $customer] = workflowUser();
    Job::factory()->forCustomer($customer)->create([
        'status'     => Job::STATUS_IN_PROGRESS,
        'updated_at' => now()->subHours(1),
    ]);

    $this->artisan('workflow:detect-stuck')->assertExitCode(0);

    Event::assertNotDispatched(WorkflowStuck::class);
});
