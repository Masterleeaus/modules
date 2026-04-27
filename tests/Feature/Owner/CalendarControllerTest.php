<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function calendarSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$user, $org, $customer];
}

// ── Index ─────────────────────────────────────────────────────────────────────

test('calendar index requires authentication', function () {
    $this->get('/owner/calendar')->assertRedirect('/login');
});

test('owner can view the calendar page', function () {
    [$user] = calendarSetup();

    $this->actingAs($user)
        ->get('/owner/calendar')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Calendar'));
});

// ── Events API ────────────────────────────────────────────────────────────────

test('calendar events api requires authentication', function () {
    $this->getJson('/owner/calendar/events?start=2026-05-01&end=2026-05-31')
        ->assertUnauthorized();
});

test('calendar events returns jobs in the requested date range', function () {
    [$user, $org, $customer] = calendarSetup();

    Job::factory()->forCustomer($customer)->count(2)->create([
        'scheduled_at' => '2026-05-15 09:00:00',
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    // Outside the range — should not appear
    Job::factory()->forCustomer($customer)->create([
        'scheduled_at' => '2026-06-20 09:00:00',
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($user)
        ->getJson('/owner/calendar/events?start=2026-05-01&end=2026-05-31')
        ->assertOk()
        ->assertJsonCount(2);
});

test('calendar events excludes jobs with no scheduled_at', function () {
    [$user, $org, $customer] = calendarSetup();

    Job::factory()->forCustomer($customer)->create([
        'scheduled_at' => '2026-05-10 09:00:00',
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    // No scheduled_at — should not appear
    Job::factory()->forCustomer($customer)->create([
        'scheduled_at' => null,
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($user)
        ->getJson('/owner/calendar/events?start=2026-05-01&end=2026-05-31')
        ->assertOk()
        ->assertJsonCount(1);
});

test('calendar events are scoped to the authenticated user\'s organization', function () {
    [$user, $org, $customer] = calendarSetup();
    [, , $otherCustomer]     = calendarSetup();

    // Own org: 2 jobs
    Job::factory()->forCustomer($customer)->count(2)->create([
        'scheduled_at' => '2026-05-10 09:00:00',
    ]);
    // Other org: 5 jobs — must not appear
    Job::factory()->forCustomer($otherCustomer)->count(5)->create([
        'scheduled_at' => '2026-05-12 10:00:00',
    ]);

    $this->actingAs($user)
        ->getJson('/owner/calendar/events?start=2026-05-01&end=2026-05-31')
        ->assertOk()
        ->assertJsonCount(2);
});

test('calendar events response contains expected fields', function () {
    [$user, $org, $customer] = calendarSetup();

    Job::factory()->forCustomer($customer)->create([
        'title'        => 'Boiler Service',
        'scheduled_at' => '2026-05-10 09:00:00',
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($user)
        ->getJson('/owner/calendar/events?start=2026-05-01&end=2026-05-31')
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['id', 'title', 'start', 'url', 'backgroundColor', 'extendedProps'],
        ]);
});

test('calendar events validation requires start and end params', function () {
    [$user] = calendarSetup();

    $this->actingAs($user)
        ->getJson('/owner/calendar/events')
        ->assertUnprocessable();
});

test('calendar events title includes customer last name when available', function () {
    [$user, $org, $customer] = calendarSetup();

    $customer->update(['last_name' => 'Smith', 'first_name' => 'Alice']);
    Job::factory()->forCustomer($customer)->create([
        'title'        => 'AC Service',
        'scheduled_at' => '2026-05-10 09:00:00',
    ]);

    $response = $this->actingAs($user)
        ->getJson('/owner/calendar/events?start=2026-05-01&end=2026-05-31')
        ->assertOk();

    $events = $response->json();
    expect($events[0]['title'])->toContain('Smith');
    expect($events[0]['title'])->toContain('AC Service');
});

test('calendar events use job type color when available', function () {
    [$user, $org, $customer] = calendarSetup();

    $jobType = JobType::factory()->create(['organization_id' => $org->id, 'color' => '#ff0000']);
    Job::factory()->forCustomer($customer)->create([
        'job_type_id'  => $jobType->id,
        'scheduled_at' => '2026-05-10 09:00:00',
    ]);

    $response = $this->actingAs($user)
        ->getJson('/owner/calendar/events?start=2026-05-01&end=2026-05-31')
        ->assertOk();

    $events = $response->json();
    expect($events[0]['backgroundColor'])->toBe('#ff0000');
});
