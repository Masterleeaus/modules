<?php

use App\Models\Customer;
use App\Models\Item;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Organization;
use App\Models\Scopes\TenantScope;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

/**
 * Verifies that the TenantScope global scope enforces organisation-level isolation
 * at the Eloquent query layer, without relying on controller-level scoping.
 */
beforeEach(function () {
    (new RolesAndPermissionsSeeder)->run();
});

// ── Helpers ───────────────────────────────────────────────────────────────────

function tenantOwner(): array
{
    $myOrg    = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $myOrg->id]);
    $user->assignRole('owner');

    $otherOrg = Organization::factory()->create();

    return [$user, $myOrg, $otherOrg];
}

// ── Cross-org isolation ───────────────────────────────────────────────────────

test('org A user cannot query org B customers via global scope', function () {
    [$user, $myOrg, $otherOrg] = tenantOwner();

    $mine  = Customer::factory()->create(['organization_id' => $myOrg->id]);
    $other = Customer::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user);

    $ids = Customer::pluck('id');

    expect($ids)->toContain($mine->id)
        ->not->toContain($other->id);
});

test('org A user cannot find org B job by id', function () {
    [$user, , $otherOrg] = tenantOwner();

    $customer  = Customer::factory()->create(['organization_id' => $otherOrg->id]);
    $otherJob  = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user);

    expect(Job::find($otherJob->id))->toBeNull();
});

test('org A user cannot see org B job types', function () {
    [$user, $myOrg, $otherOrg] = tenantOwner();

    $mine  = JobType::factory()->create(['organization_id' => $myOrg->id]);
    $other = JobType::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user);

    $ids = JobType::pluck('id');

    expect($ids)->toContain($mine->id)
        ->not->toContain($other->id);
});

test('org A user cannot see org B items', function () {
    [$user, $myOrg, $otherOrg] = tenantOwner();

    $mine  = Item::factory()->create(['organization_id' => $myOrg->id]);
    $other = Item::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user);

    $ids = Item::pluck('id');

    expect($ids)->toContain($mine->id)
        ->not->toContain($other->id);
});

// ── Super-admin bypass ────────────────────────────────────────────────────────

test('super_admin can query all organisations records without scope', function () {
    [, $myOrg, $otherOrg] = tenantOwner();

    $admin = User::factory()->create(['organization_id' => $myOrg->id]);
    $admin->assignRole('super_admin');

    $customer1 = Customer::factory()->create(['organization_id' => $myOrg->id]);
    $customer2 = Customer::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($admin);

    $ids = Customer::pluck('id');

    expect($ids)->toContain($customer1->id)
        ->toContain($customer2->id);
});

// ── Auto-set organization_id on creation ──────────────────────────────────────

test('organization_id is auto-set from authenticated user on create', function () {
    [$user, $myOrg] = tenantOwner();

    $this->actingAs($user);

    $customer = Customer::create([
        'first_name' => 'Auto',
        'last_name'  => 'Scoped',
        'email'      => 'auto@example.com',
    ]);

    expect($customer->organization_id)->toBe($myOrg->id);
});

test('organization_id is not overridden when already set', function () {
    [$user, $myOrg, $otherOrg] = tenantOwner();

    $this->actingAs($user);

    $customer = Customer::create([
        'first_name'      => 'Explicit',
        'last_name'       => 'Org',
        'email'           => 'explicit@example.com',
        'organization_id' => $myOrg->id,
    ]);

    expect($customer->organization_id)->toBe($myOrg->id);
});

// ── withoutGlobalScope allows explicit cross-org queries ─────────────────────

test('withoutGlobalScope allows cross-org queries when explicitly requested', function () {
    [$user, $myOrg, $otherOrg] = tenantOwner();

    $customer1 = Customer::factory()->create(['organization_id' => $myOrg->id]);
    $customer2 = Customer::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user);

    $ids = Customer::withoutGlobalScope(TenantScope::class)->pluck('id');

    expect($ids)->toContain($customer1->id)
        ->toContain($customer2->id);
});
