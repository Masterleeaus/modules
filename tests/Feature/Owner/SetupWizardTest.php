<?php

use App\Http\Controllers\Owner\SetupController;
use App\Models\JobType;
use App\Models\MessageTemplate;
use App\Models\Organization;
use App\Models\OrganizationSetting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function setupUser(string $role = 'owner'): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole($role);

    return [$user, $org];
}

// ── isComplete() logic ────────────────────────────────────────────────────────

test('isComplete returns false when company name is missing', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org = Organization::factory()->create();
    expect(SetupController::isComplete($org->id))->toBeFalse();
});

test('isComplete returns false when no job types exist', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org = Organization::factory()->create();
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    expect(SetupController::isComplete($org->id))->toBeFalse();
});

test('isComplete returns false when no technicians exist', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org = Organization::factory()->create();
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);
    expect(SetupController::isComplete($org->id))->toBeFalse();
});

test('isComplete returns true when all required steps are done', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org  = Organization::factory()->create();
    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);
    expect(SetupController::isComplete($org->id))->toBeTrue();
});

test('isComplete returns true when setup_complete flag is set', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org = Organization::factory()->create();
    OrganizationSetting::factory()->create([
        'organization_id' => $org->id,
        'setup_complete'  => true,
    ]);
    expect(SetupController::isComplete($org->id))->toBeTrue();
});

// ── HTTP routes ───────────────────────────────────────────────────────────────

test('setup wizard is accessible to owner', function () {
    [$user] = setupUser('owner');
    $this->actingAs($user)->get('/owner/setup')->assertOk();
});

test('setup wizard redirects when setup is already complete', function () {
    [$user, $org] = setupUser('owner');
    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)->get('/owner/setup')->assertRedirect('/owner/dashboard');
});

test('dispatcher cannot access setup wizard', function () {
    [$user] = setupUser('dispatcher');
    $this->actingAs($user)->get('/owner/setup')->assertForbidden();
});

test('owner can save company info', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/company', [
        'name'  => 'My Company',
        'email' => 'info@myco.com',
        'phone' => '555-1234',
    ])->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->company_name)->toBe('My Company');
    expect($settings->company_email)->toBe('info@myco.com');
});

test('saving company info marks company step as complete', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/company', [
        'name' => 'My Company',
    ])->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->setup_completed_steps)->toContain('company');
});

test('owner can add a job type via setup', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/job-types', [
        'name'  => 'HVAC Service',
        'color' => '#3b82f6',
    ])->assertRedirect();

    expect(JobType::where('organization_id', $org->id)->where('name', 'HVAC Service')->exists())->toBeTrue();
});

test('adding a job type marks job_types step as complete', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/job-types', [
        'name'  => 'Deep Clean',
        'color' => '#10b981',
    ])->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->setup_completed_steps)->toContain('job_types');
});

test('owner can add a technician via setup', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/technicians', [
        'name'     => 'Tech Bob',
        'email'    => 'bob@example.com',
        'password' => 'Password1!',
    ])->assertRedirect();

    $tech = User::where('email', 'bob@example.com')->first();
    expect($tech)->not->toBeNull();
    expect($tech->hasRole('technician'))->toBeTrue();
    expect($tech->organization_id)->toBe($org->id);
});

test('adding a technician marks technicians step as complete', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/technicians', [
        'name'     => 'Tech Alice',
        'email'    => 'alice@example.com',
        'password' => 'Password1!',
    ])->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->setup_completed_steps)->toContain('technicians');
});

test('owner can save notification templates', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/templates', [
        'templates' => [
            [
                'event'     => 'job_scheduled',
                'channel'   => 'email',
                'subject'   => 'Your job is confirmed',
                'body'      => 'Hi {{customer_name}}, your job is confirmed.',
                'is_active' => true,
            ],
        ],
    ])->assertRedirect();

    expect(MessageTemplate::where('organization_id', $org->id)
        ->where('event', 'job_scheduled')
        ->where('channel', 'email')
        ->exists()
    )->toBeTrue();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->setup_completed_steps)->toContain('templates');
});

test('owner can save branding', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/branding', [
        'brand_color'          => '#6366f1',
        'customer_facing_name' => 'Acme Cleaning Co.',
    ])->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->brand_color)->toBe('#6366f1');
    expect($settings->customer_facing_name)->toBe('Acme Cleaning Co.');
    expect($settings->setup_completed_steps)->toContain('branding');
});

test('owner can skip an optional step', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/skip', ['step' => 'branding'])->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->setup_completed_steps)->toContain('branding');
});

test('owner cannot skip a required step', function () {
    [$user] = setupUser();

    $this->actingAs($user)->post('/owner/setup/skip', ['step' => 'company'])
        ->assertSessionHasErrors('step');
});

test('owner can mark payment step complete', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/payment')->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->setup_completed_steps)->toContain('payment');
});

test('complete redirects to dashboard when setup is done', function () {
    [$user, $org] = setupUser();
    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)->post('/owner/setup/complete')->assertRedirect('/owner/dashboard');
});

test('complete marks setup_complete flag in settings', function () {
    [$user, $org] = setupUser();
    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)->post('/owner/setup/complete');

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->setup_complete)->toBeTrue();
});

test('complete returns error when setup is incomplete', function () {
    [$user] = setupUser();

    $this->actingAs($user)->post('/owner/setup/complete')->assertSessionHasErrors('setup');
});

// ── Progress persistence ──────────────────────────────────────────────────────

test('wizard shows previously saved company data after refresh', function () {
    [$user, $org] = setupUser();
    OrganizationSetting::factory()->create([
        'organization_id' => $org->id,
        'company_name'    => 'Saved Company',
        'company_email'   => 'saved@example.com',
    ]);

    $response = $this->actingAs($user)->get('/owner/setup');
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Owner/Setup/Wizard')
        ->where('company.name', 'Saved Company')
        ->where('company.email', 'saved@example.com')
    );
});

test('wizard passes completed steps to the frontend', function () {
    [$user, $org] = setupUser();
    OrganizationSetting::factory()->create([
        'organization_id'        => $org->id,
        'company_name'           => 'Acme',
        'setup_completed_steps'  => ['company', 'job_types'],
    ]);

    $response = $this->actingAs($user)->get('/owner/setup');
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Owner/Setup/Wizard')
        ->where('setup_completed_steps', ['company', 'job_types'])
    );
});

// ── Redirect enforcement middleware ───────────────────────────────────────────

test('owner with incomplete setup is redirected from dashboard', function () {
    [$user] = setupUser();

    $this->actingAs($user)->get('/owner/dashboard')->assertRedirect('/owner/setup');
});

test('owner with complete setup can access dashboard', function () {
    [$user, $org] = setupUser();
    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)->get('/owner/dashboard')->assertOk();
});
