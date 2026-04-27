<?php

use App\Models\Job;
use App\Models\JobChecklistItem;
use App\Models\JobLineItem;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(fn () => (new RolesAndPermissionsSeeder)->run());

// ── Helpers ────────────────────────────────────────────────────────────────────

function syncTechWithJob(): array
{
    $org  = Organization::factory()->create();
    $tech = User::factory()->technician($org)->create();
    $job  = Job::factory()->create([
        'organization_id' => $org->id,
        'assigned_to'     => $tech->id,
        'status'          => Job::STATUS_SCHEDULED,
        'scheduled_at'    => now(),
    ]);

    return [$tech, $job, $org];
}

// ── batch sync ─────────────────────────────────────────────────────────────────

test('technician can batch sync status and notes mutations', function () {
    [$tech, $job] = syncTechWithJob();

    $this->actingAs($tech)
        ->postJson('/api/technician/sync', [
            'mutations' => [
                ['type' => 'status', 'job_id' => $job->id, 'status' => 'in_progress'],
                ['type' => 'notes',  'job_id' => $job->id, 'technician_notes' => 'All done.'],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonCount(2, 'results');

    expect($job->fresh()->status)->toBe('in_progress');
    expect($job->fresh()->technician_notes)->toBe('All done.');
});

test('batch sync returns 207 with partial status on error', function () {
    [$tech, $job] = syncTechWithJob();

    $this->actingAs($tech)
        ->postJson('/api/technician/sync', [
            'mutations' => [
                ['type' => 'status', 'job_id' => $job->id, 'status' => 'in_progress'],
                ['type' => 'status', 'job_id' => $job->id, 'status' => 'invalid_status'],
            ],
        ])
        ->assertStatus(207)
        ->assertJsonPath('status', 'partial');
});

test('batch sync rejects mutations for jobs not assigned to technician', function () {
    $org   = Organization::factory()->create();
    $tech  = User::factory()->technician($org)->create();
    $other = User::factory()->technician($org)->create();
    $job   = Job::factory()->create(['organization_id' => $org->id, 'assigned_to' => $other->id]);

    $this->actingAs($tech)
        ->postJson('/api/technician/sync', [
            'mutations' => [
                ['type' => 'status', 'job_id' => $job->id, 'status' => 'in_progress'],
            ],
        ])
        ->assertStatus(207)
        ->assertJsonPath('results.0.status', 'error');
});

test('batch sync can create line items', function () {
    [$tech, $job] = syncTechWithJob();

    $this->actingAs($tech)
        ->postJson('/api/technician/sync', [
            'mutations' => [
                [
                    'type'       => 'line_item_create',
                    'job_id'     => $job->id,
                    'name'       => 'Parts',
                    'unit_price' => 10.00,
                    'quantity'   => 3,
                ],
            ],
        ])
        ->assertOk();

    expect($job->lineItems()->count())->toBe(1);
});

test('batch sync can toggle checklist item', function () {
    [$tech, $job] = syncTechWithJob();

    $item = JobChecklistItem::create([
        'job_id'       => $job->id,
        'label'        => 'Test item',
        'sort_order'   => 1,
        'is_required'  => false,
        'completed_at' => null,
    ]);

    $this->actingAs($tech)
        ->postJson('/api/technician/sync', [
            'mutations' => [
                ['type' => 'checklist', 'job_id' => $job->id, 'item_id' => $item->id, 'completed' => true],
            ],
        ])
        ->assertOk();

    expect($item->fresh()->completed_at)->not->toBeNull();
});

test('guest cannot call batch sync', function () {
    $this->postJson('/api/technician/sync', ['mutations' => []])
        ->assertUnauthorized();
});

test('batch sync validates mutations array is required', function () {
    [$tech] = syncTechWithJob();

    $this->actingAs($tech)
        ->postJson('/api/technician/sync', [])
        ->assertUnprocessable();
});
