<?php

use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PlanService;
use Database\Seeders\RolesAndPermissionsSeeder;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function planSetup(string $plan = PlanService::PLAN_GROWTH, bool $trialing = true): array
{
    (new RolesAndPermissionsSeeder)->run();

    $org = Organization::factory()->create(['plan' => $plan]);

    // Override the default subscription created by OrganizationFactory
    Subscription::where('organization_id', $org->id)->delete();

    if ($trialing) {
        Subscription::factory()->trialing($org, $plan)->create();
    } else {
        Subscription::factory()->active($org, $plan)->create();
    }

    return [$org, new PlanService];
}

// ── activePlan() ──────────────────────────────────────────────────────────────

test('activePlan returns growth features during starter trial', function () {
    [$org, $service] = planSetup(PlanService::PLAN_STARTER, trialing: true);

    expect($service->activePlan($org))->toBe(PlanService::PLAN_GROWTH);
});

test('activePlan returns growth features during growth trial', function () {
    [$org, $service] = planSetup(PlanService::PLAN_GROWTH, trialing: true);

    expect($service->activePlan($org))->toBe(PlanService::PLAN_GROWTH);
});

test('activePlan returns pro features during pro trial', function () {
    [$org, $service] = planSetup(PlanService::PLAN_PRO, trialing: true);

    expect($service->activePlan($org))->toBe(PlanService::PLAN_PRO);
});

test('activePlan returns actual plan when not trialing', function () {
    [$org, $service] = planSetup(PlanService::PLAN_STARTER, trialing: false);

    expect($service->activePlan($org))->toBe(PlanService::PLAN_STARTER);
});

test('activePlan returns starter when no subscription exists', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org = Organization::factory()->withoutSubscription()->create();
    $service = new PlanService;

    expect($service->activePlan($org))->toBe(PlanService::PLAN_STARTER);
});

test('activePlan returns starter when trial has expired', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org = Organization::factory()->create(['plan' => PlanService::PLAN_STARTER]);
    Subscription::where('organization_id', $org->id)->delete();
    Subscription::factory()->trialExpired($org, PlanService::PLAN_STARTER)->create();
    $service = new PlanService;

    expect($service->activePlan($org))->toBe(PlanService::PLAN_STARTER);
});

// ── technicianLimit() ─────────────────────────────────────────────────────────

test('technicianLimit returns 3 for starter plan', function () {
    [$org, $service] = planSetup(PlanService::PLAN_STARTER, trialing: false);

    expect($service->technicianLimit($org))->toBe(3);
});

test('technicianLimit returns 10 for growth plan', function () {
    [$org, $service] = planSetup(PlanService::PLAN_GROWTH, trialing: false);

    expect($service->technicianLimit($org))->toBe(10);
});

test('technicianLimit returns null for pro plan', function () {
    [$org, $service] = planSetup(PlanService::PLAN_PRO, trialing: false);

    expect($service->technicianLimit($org))->toBeNull();
});

// ── atTechnicianLimit() ───────────────────────────────────────────────────────

test('atTechnicianLimit returns false when under the cap', function () {
    [$org, $service] = planSetup(PlanService::PLAN_STARTER, trialing: false);

    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');

    expect($service->atTechnicianLimit($org))->toBeFalse();
});

test('atTechnicianLimit returns true when at the cap', function () {
    [$org, $service] = planSetup(PlanService::PLAN_STARTER, trialing: false);

    for ($i = 0; $i < 3; $i++) {
        $tech = User::factory()->create(['organization_id' => $org->id]);
        $tech->assignRole('technician');
    }

    expect($service->atTechnicianLimit($org))->toBeTrue();
});

test('atTechnicianLimit always returns false for pro plan', function () {
    [$org, $service] = planSetup(PlanService::PLAN_PRO, trialing: false);

    for ($i = 0; $i < 100; $i++) {
        $tech = User::factory()->create(['organization_id' => $org->id]);
        $tech->assignRole('technician');
    }

    expect($service->atTechnicianLimit($org))->toBeFalse();
});

// ── technicianCount() ─────────────────────────────────────────────────────────

test('technicianCount returns 0 with no technicians', function () {
    [$org, $service] = planSetup();

    expect($service->technicianCount($org))->toBe(0);
});

test('technicianCount counts only technician role users', function () {
    [$org, $service] = planSetup();

    $tech1 = User::factory()->create(['organization_id' => $org->id]);
    $tech1->assignRole('technician');

    $tech2 = User::factory()->create(['organization_id' => $org->id]);
    $tech2->assignRole('technician');

    $admin = User::factory()->create(['organization_id' => $org->id]);
    $admin->assignRole('admin');

    expect($service->technicianCount($org))->toBe(2);
});

// ── isValidPlan() ─────────────────────────────────────────────────────────────

test('isValidPlan returns true for known plan keys', function () {
    $service = new PlanService;

    expect($service->isValidPlan('starter'))->toBeTrue();
    expect($service->isValidPlan('growth'))->toBeTrue();
    expect($service->isValidPlan('pro'))->toBeTrue();
});

test('isValidPlan returns false for unknown plan key', function () {
    expect((new PlanService)->isValidPlan('enterprise'))->toBeFalse();
});

// ── label() ──────────────────────────────────────────────────────────────────

test('label returns human-readable plan names', function () {
    $service = new PlanService;

    expect($service->label('starter'))->toBe('Starter');
    expect($service->label('growth'))->toBe('Growth');
    expect($service->label('pro'))->toBe('Pro');
});

// ── monthlyPrice() / annualPrice() ────────────────────────────────────────────

test('monthlyPrice returns expected values', function () {
    $service = new PlanService;

    expect($service->monthlyPrice('starter'))->toBe(79);
    expect($service->monthlyPrice('growth'))->toBe(149);
    expect($service->monthlyPrice('pro'))->toBe(249);
});

test('annualPrice is cheaper than monthly price', function () {
    $service = new PlanService;

    foreach (['starter', 'growth', 'pro'] as $plan) {
        expect($service->annualPrice($plan))->toBeLessThan($service->monthlyPrice($plan));
    }
});
