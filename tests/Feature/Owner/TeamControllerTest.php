<?php

use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PlanService;
use Database\Seeders\RolesAndPermissionsSeeder;

function teamSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');

    return [$user, $org];
}

// ── Index ─────────────────────────────────────────────────────────────────────

test('team index requires authentication', function () {
    $this->get('/owner/team')->assertRedirect('/login');
});

test('owner can view team index', function () {
    [$user] = teamSetup();

    $this->actingAs($user)
        ->get('/owner/team')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Team/Index')
            ->has('team_members')
            ->has('technician_limit')
            ->has('technician_count')
            ->has('at_limit')
            ->has('active_plan')
        );
});

// ── Store ─────────────────────────────────────────────────────────────────────

test('owner can add a technician to their team', function () {
    [$user, $org] = teamSetup();

    $this->actingAs($user)
        ->post('/owner/team', [
            'name'     => 'Jane Tech',
            'email'    => 'janetech@example.com',
            'password' => 'password123',
            'role'     => 'technician',
        ])
        ->assertRedirect();

    $newUser = User::where('email', 'janetech@example.com')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->organization_id)->toBe($org->id);
    expect($newUser->hasRole('technician'))->toBeTrue();
});

test('owner can add an admin to their team', function () {
    [$user, $org] = teamSetup();

    $this->actingAs($user)
        ->post('/owner/team', [
            'name'     => 'Bob Admin',
            'email'    => 'bobadmin@example.com',
            'password' => 'password123',
            'role'     => 'admin',
        ])
        ->assertRedirect();

    $newUser = User::where('email', 'bobadmin@example.com')->first();
    expect($newUser->hasRole('admin'))->toBeTrue();
});

test('adding a team member requires name email password and role', function () {
    [$user] = teamSetup();

    $this->actingAs($user)
        ->post('/owner/team', [])
        ->assertSessionHasErrors(['name', 'email', 'password', 'role']);
});

test('email must be unique when adding a team member', function () {
    [$user, $org] = teamSetup();
    User::factory()->create(['organization_id' => $org->id, 'email' => 'taken@example.com']);

    $this->actingAs($user)
        ->post('/owner/team', [
            'name'     => 'Duplicate',
            'email'    => 'taken@example.com',
            'password' => 'password123',
            'role'     => 'technician',
        ])
        ->assertSessionHasErrors(['email']);
});

test('adding a technician when at the plan limit returns a role error', function () {
    [$user, $org] = teamSetup();

    // Starter plan allows 3 technicians; fill all 3 seats.
    Subscription::where('organization_id', $org->id)->update([
        'plan'          => PlanService::PLAN_STARTER,
        'status'        => Subscription::STATUS_ACTIVE,
        'trial_ends_at' => null,
    ]);
    $org->update(['plan' => PlanService::PLAN_STARTER]);

    for ($i = 1; $i <= 3; $i++) {
        $tech = User::factory()->create(['organization_id' => $org->id]);
        $tech->assignRole('technician');
    }

    $this->actingAs($user)
        ->post('/owner/team', [
            'name'     => 'Extra Tech',
            'email'    => 'extratech@example.com',
            'password' => 'password123',
            'role'     => 'technician',
        ])
        ->assertSessionHasErrors(['role']);
});

test('adding a non-technician role succeeds even when at technician limit', function () {
    [$user, $org] = teamSetup();

    Subscription::where('organization_id', $org->id)->update([
        'plan'          => PlanService::PLAN_STARTER,
        'status'        => Subscription::STATUS_ACTIVE,
        'trial_ends_at' => null,
    ]);
    $org->update(['plan' => PlanService::PLAN_STARTER]);

    for ($i = 1; $i <= 3; $i++) {
        $tech = User::factory()->create(['organization_id' => $org->id]);
        $tech->assignRole('technician');
    }

    $this->actingAs($user)
        ->post('/owner/team', [
            'name'     => 'New Admin',
            'email'    => 'newadmin@example.com',
            'password' => 'password123',
            'role'     => 'admin',
        ])
        ->assertRedirect();

    expect(User::where('email', 'newadmin@example.com')->exists())->toBeTrue();
});

// ── Update ────────────────────────────────────────────────────────────────────

test('owner can update a team member\'s role', function () {
    [$user, $org] = teamSetup();

    $member = User::factory()->create(['organization_id' => $org->id]);
    $member->assignRole('dispatcher');

    $this->actingAs($user)
        ->patch("/owner/team/{$member->id}", ['role' => 'admin'])
        ->assertRedirect();

    expect($member->fresh()->hasRole('admin'))->toBeTrue();
    expect($member->fresh()->hasRole('dispatcher'))->toBeFalse();
});

test('owner cannot update a user from another organization', function () {
    [$user] = teamSetup();
    [, $otherOrg] = teamSetup();

    $otherMember = User::factory()->create(['organization_id' => $otherOrg->id]);
    $otherMember->assignRole('technician');

    $this->actingAs($user)
        ->patch("/owner/team/{$otherMember->id}", ['role' => 'admin'])
        ->assertForbidden();
});

test('promoting to technician when at limit returns a role error', function () {
    [$user, $org] = teamSetup();

    Subscription::where('organization_id', $org->id)->update([
        'plan'          => PlanService::PLAN_STARTER,
        'status'        => Subscription::STATUS_ACTIVE,
        'trial_ends_at' => null,
    ]);
    $org->update(['plan' => PlanService::PLAN_STARTER]);

    for ($i = 1; $i <= 3; $i++) {
        $tech = User::factory()->create(['organization_id' => $org->id]);
        $tech->assignRole('technician');
    }

    $admin = User::factory()->create(['organization_id' => $org->id]);
    $admin->assignRole('admin');

    $this->actingAs($user)
        ->patch("/owner/team/{$admin->id}", ['role' => 'technician'])
        ->assertSessionHasErrors(['role']);
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('owner can remove a team member', function () {
    [$user, $org] = teamSetup();

    $member = User::factory()->create(['organization_id' => $org->id]);
    $member->assignRole('technician');

    $this->actingAs($user)
        ->delete("/owner/team/{$member->id}")
        ->assertRedirect();

    expect(User::find($member->id))->toBeNull();
});

test('owner cannot remove themselves from the team', function () {
    [$user] = teamSetup();

    $this->actingAs($user)
        ->delete("/owner/team/{$user->id}")
        ->assertForbidden();
});

test('owner cannot remove a member from another organization', function () {
    [$user] = teamSetup();
    [, $otherOrg] = teamSetup();

    $otherMember = User::factory()->create(['organization_id' => $otherOrg->id]);
    $otherMember->assignRole('technician');

    $this->actingAs($user)
        ->delete("/owner/team/{$otherMember->id}")
        ->assertForbidden();
});
