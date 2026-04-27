<?php

use App\Models\Organization;
use App\Models\OrganizationSetting;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Modules\TitanSolo\Services\SoloModeService;

beforeEach(fn () => (new RolesAndPermissionsSeeder)->run());

// ─── SoloModeService ──────────────────────────────────────────────────────────

test('SoloModeService returns false when no settings exist', function () {
    $org = Organization::factory()->create();
    expect(app(SoloModeService::class)->isSolo($org->id))->toBeFalse();
});

test('SoloModeService detects solo mode from settings', function () {
    $org = Organization::factory()->create();
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'mode' => 'solo']);

    expect(app(SoloModeService::class)->isSolo($org->id))->toBeTrue();
});

test('SoloModeService detects team mode from settings', function () {
    $org = Organization::factory()->create();
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'mode' => 'team']);

    expect(app(SoloModeService::class)->isSolo($org->id))->toBeFalse();
});

test('SoloModeService enableSolo updates the mode', function () {
    $org = Organization::factory()->create();
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'mode' => 'team']);

    app(SoloModeService::class)->enableSolo($org->id);

    expect(OrganizationSetting::where('organization_id', $org->id)->value('mode'))->toBe('solo');
});

test('SoloModeService enableTeam updates the mode', function () {
    $org = Organization::factory()->create();
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'mode' => 'solo']);

    app(SoloModeService::class)->enableTeam($org->id);

    expect(OrganizationSetting::where('organization_id', $org->id)->value('mode'))->toBe('team');
});

// ─── Settings / Mode route ────────────────────────────────────────────────────

function soloOwner(string $mode = 'solo'): array
{
    $org  = Organization::factory()->trialing()->create(['plan' => 'growth']);
    $user = User::factory()->owner($org)->create();
    Subscription::factory()->trialing($org, 'growth')->create();
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'mode' => $mode]);

    return [$user, $org];
}

test('owner can view mode settings page', function () {
    [$owner] = soloOwner('team');

    $this->actingAs($owner)
        ->get(route('owner.settings.mode'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Settings/Mode'));
});

test('mode page shows current mode', function () {
    [$owner] = soloOwner('solo');

    $this->actingAs($owner)
        ->get(route('owner.settings.mode'))
        ->assertInertia(fn ($page) => $page->where('mode', 'solo'));
});

test('owner can switch to solo mode', function () {
    [$owner, $org] = soloOwner('team');

    $this->actingAs($owner)
        ->post(route('owner.settings.mode.update'), ['mode' => 'solo'])
        ->assertRedirect();

    expect(OrganizationSetting::where('organization_id', $org->id)->value('mode'))->toBe('solo');
});

test('owner can switch to team mode', function () {
    [$owner, $org] = soloOwner('solo');

    $this->actingAs($owner)
        ->post(route('owner.settings.mode.update'), ['mode' => 'team'])
        ->assertRedirect();

    expect(OrganizationSetting::where('organization_id', $org->id)->value('mode'))->toBe('team');
});

test('mode update rejects invalid mode values', function () {
    [$owner] = soloOwner('team');

    $this->actingAs($owner)
        ->post(route('owner.settings.mode.update'), ['mode' => 'enterprise'])
        ->assertSessionHasErrors('mode');
});

// ─── EnforceSoloMode middleware ───────────────────────────────────────────────

test('dispatch board is blocked in solo mode', function () {
    [$owner] = soloOwner('solo');

    $this->actingAs($owner)
        ->get(route('owner.dispatch'))
        ->assertRedirect(route('owner.dashboard'));
});

test('dispatch board is accessible in team mode', function () {
    [$owner] = soloOwner('team');

    $this->actingAs($owner)
        ->get(route('owner.dispatch'))
        ->assertOk();
});

test('adding a technician is blocked in solo mode', function () {
    [$owner] = soloOwner('solo');

    $this->actingAs($owner)
        ->post(route('owner.team.store'), [
            'name'     => 'Second Tech',
            'email'    => 'second@example.com',
            'password' => 'Password1!',
            'role'     => 'technician',
        ])
        ->assertRedirect(route('owner.dashboard'));

    expect(User::where('email', 'second@example.com')->exists())->toBeFalse();
});

// ─── Solo mode: technician cap ────────────────────────────────────────────────

test('adding a technician in solo mode is blocked before reaching controller', function () {
    // EnforceSoloMode middleware on team.store redirects to dashboard in solo mode,
    // so no technician can ever be added regardless of the existing count.
    [$owner] = soloOwner('solo');

    $this->actingAs($owner)
        ->post(route('owner.team.store'), [
            'name'     => 'Extra Tech',
            'email'    => 'extra@example.com',
            'password' => 'Password1!',
            'role'     => 'technician',
        ])
        ->assertRedirect(route('owner.dashboard'));

    expect(User::where('email', 'extra@example.com')->exists())->toBeFalse();
});

// ─── Solo dashboard ───────────────────────────────────────────────────────────

test('solo mode dashboard renders Solo/Dashboard component', function () {
    [$owner] = soloOwner('solo');

    $this->actingAs($owner)
        ->get(route('owner.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Solo/Dashboard'));
});

test('team mode dashboard renders Owner/Dashboard component', function () {
    [$owner] = soloOwner('team');

    $this->actingAs($owner)
        ->get(route('owner.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Dashboard'));
});

// ─── Job creation auto-assigns owner in solo mode ─────────────────────────────

test('job create page hides technician picker in solo mode', function () {
    [$owner] = soloOwner('solo');

    $this->actingAs($owner)
        ->get(route('owner.jobs.create'))
        ->assertInertia(fn ($page) => $page
            ->where('is_solo', true)
            ->where('technicians', [])
        );
});

test('job creation auto-assigns owner in solo mode', function () {
    [$owner, $org] = soloOwner('solo');

    $customer = \App\Models\Customer::factory()->create(['organization_id' => $org->id]);
    $jobType  = \App\Models\JobType::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($owner)
        ->post(route('owner.jobs.store'), [
            'title'        => 'Test Solo Job',
            'customer_id'  => $customer->id,
            'job_type_id'  => $jobType->id,
            'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ])
        ->assertRedirect();

    $job = \App\Models\Job::where('title', 'Test Solo Job')->first();
    expect($job)->not->toBeNull();
    expect($job->assigned_to)->toBe($owner->id);
});
