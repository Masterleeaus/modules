<?php

use App\Models\Job;
use App\Models\JobChecklistItem;
use App\Models\JobLineItem;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(fn () => (new RolesAndPermissionsSeeder)->run());

// ── Helpers ────────────────────────────────────────────────────────────────────

function technicianWithJob(): array
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

// ── today ──────────────────────────────────────────────────────────────────────

test('technician can fetch today jobs via api', function () {
    [$tech, $job] = technicianWithJob();

    $this->actingAs($tech)
        ->getJson('/api/technician/jobs/today')
        ->assertOk()
        ->assertJsonPath('data.0.id', $job->id);
});

test('guest cannot fetch today jobs', function () {
    $this->getJson('/api/technician/jobs/today')
        ->assertUnauthorized();
});

// ── status update ──────────────────────────────────────────────────────────────

test('technician can update job status to in_progress', function () {
    [$tech, $job] = technicianWithJob();

    $this->actingAs($tech)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'in_progress'])
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('data.status', 'in_progress');

    expect($job->fresh()->status)->toBe('in_progress');
});

test('technician cannot update another technicians job', function () {
    $org  = Organization::factory()->create();
    $tech = User::factory()->technician($org)->create();
    $other = User::factory()->technician($org)->create();
    $job  = Job::factory()->create(['organization_id' => $org->id, 'assigned_to' => $other->id]);

    $this->actingAs($tech)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'in_progress'])
        ->assertForbidden();
});

test('technician cannot set status to cancelled', function () {
    [$tech, $job] = technicianWithJob();

    $this->actingAs($tech)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'cancelled'])
        ->assertUnprocessable();
});

// ── notes ──────────────────────────────────────────────────────────────────────

test('technician can update technician notes', function () {
    [$tech, $job] = technicianWithJob();

    $this->actingAs($tech)
        ->patchJson("/api/technician/jobs/{$job->id}/notes", ['technician_notes' => 'Brought extra parts.'])
        ->assertOk();

    expect($job->fresh()->technician_notes)->toBe('Brought extra parts.');
});

// ── checklist ──────────────────────────────────────────────────────────────────

test('technician can toggle checklist item completed', function () {
    [$tech, $job] = technicianWithJob();

    $item = JobChecklistItem::create([
        'job_id'       => $job->id,
        'label'        => 'Test item',
        'sort_order'   => 1,
        'is_required'  => false,
        'completed_at' => null,
    ]);

    $this->actingAs($tech)
        ->patchJson("/api/technician/jobs/{$job->id}/checklist/{$item->id}", ['completed' => true])
        ->assertOk();

    expect($item->fresh()->completed_at)->not->toBeNull();
});

// ── line items ─────────────────────────────────────────────────────────────────

test('technician can add a line item', function () {
    [$tech, $job] = technicianWithJob();

    $this->actingAs($tech)
        ->postJson("/api/technician/jobs/{$job->id}/line-items", [
            'name'       => 'Filter replacement',
            'unit_price' => 25.00,
            'quantity'   => 2,
        ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Filter replacement');
});

test('technician can delete a line item', function () {
    [$tech, $job] = technicianWithJob();

    $li = JobLineItem::factory()->create(['job_id' => $job->id]);

    $this->actingAs($tech)
        ->deleteJson("/api/technician/jobs/{$job->id}/line-items/{$li->id}")
        ->assertOk();

    expect(JobLineItem::find($li->id))->toBeNull();
});

// ── catalog ────────────────────────────────────────────────────────────────────

test('technician can search catalog', function () {
    [$tech] = technicianWithJob();

    $this->actingAs($tech)
        ->getJson('/api/technician/catalog')
        ->assertOk()
        ->assertJsonStructure(['data']);
});
