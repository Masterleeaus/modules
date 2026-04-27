<?php

use App\Models\Organization;
use App\Models\Subscription;
use App\Services\PlanService;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Constants ─────────────────────────────────────────────────────────────────

test('subscription has expected status constants', function () {
    expect(Subscription::STATUS_TRIALING)->toBe('trialing');
    expect(Subscription::STATUS_ACTIVE)->toBe('active');
    expect(Subscription::STATUS_PAST_DUE)->toBe('past_due');
    expect(Subscription::STATUS_CANCELED)->toBe('canceled');
    expect(Subscription::STATUS_PAUSED)->toBe('paused');
});

// ── isTrialing() ──────────────────────────────────────────────────────────────

test('isTrialing returns true when status is trialing and trial has not ended', function () {
    $org          = Organization::factory()->create();
    $subscription = Subscription::factory()->trialing($org)->create();

    expect($subscription->isTrialing())->toBeTrue();
});

test('isTrialing returns false when trial has expired', function () {
    $org          = Organization::factory()->create();
    $subscription = Subscription::factory()->trialExpired($org)->create();

    expect($subscription->isTrialing())->toBeFalse();
});

test('isTrialing returns false when status is active', function () {
    $org          = Organization::factory()->create();
    $subscription = Subscription::factory()->active($org)->create();

    expect($subscription->isTrialing())->toBeFalse();
});

test('isTrialing returns false when no trial_ends_at is set', function () {
    $org = Organization::factory()->create();
    $subscription = Subscription::create([
        'organization_id'  => $org->id,
        'plan'             => PlanService::PLAN_GROWTH,
        'status'           => Subscription::STATUS_TRIALING,
        'billing_interval' => 'monthly',
        'trial_ends_at'    => null,
    ]);

    expect($subscription->isTrialing())->toBeFalse();
});

// ── isActive() ────────────────────────────────────────────────────────────────

test('isActive returns true for active subscriptions', function () {
    $org          = Organization::factory()->create();
    $subscription = Subscription::factory()->active($org)->create();

    expect($subscription->isActive())->toBeTrue();
});

test('isActive returns true for trialing subscriptions', function () {
    $org          = Organization::factory()->create();
    $subscription = Subscription::factory()->trialing($org)->create();

    expect($subscription->isActive())->toBeTrue();
});

test('isActive returns false when trial has expired', function () {
    $org          = Organization::factory()->create();
    $subscription = Subscription::factory()->trialExpired($org)->create();

    expect($subscription->isActive())->toBeFalse();
});

test('isActive returns false for canceled subscriptions', function () {
    $org          = Organization::factory()->create();
    $subscription = Subscription::factory()->canceled($org)->create();

    expect($subscription->isActive())->toBeFalse();
});

// ── trialDaysRemaining() ──────────────────────────────────────────────────────

test('trialDaysRemaining returns approximate remaining days', function () {
    $org = Organization::factory()->create();
    $subscription = Subscription::create([
        'organization_id'  => $org->id,
        'plan'             => PlanService::PLAN_GROWTH,
        'status'           => Subscription::STATUS_TRIALING,
        'billing_interval' => 'monthly',
        'trial_ends_at'    => now()->addDays(7),
    ]);

    expect($subscription->trialDaysRemaining())->toBeGreaterThanOrEqual(6);
    expect($subscription->trialDaysRemaining())->toBeLessThanOrEqual(7);
});

test('trialDaysRemaining returns 0 when trial has ended', function () {
    $org          = Organization::factory()->create();
    $subscription = Subscription::factory()->trialExpired($org)->create();

    expect($subscription->trialDaysRemaining())->toBe(0);
});

test('trialDaysRemaining returns 0 when no trial_ends_at is set', function () {
    $org = Organization::factory()->create();
    $subscription = Subscription::factory()->active($org)->create();

    expect($subscription->trialDaysRemaining())->toBe(0);
});

// ── Relationships ─────────────────────────────────────────────────────────────

test('subscription belongs to an organization', function () {
    $org          = Organization::factory()->create();
    $subscription = Subscription::factory()->trialing($org)->create();

    expect($subscription->organization)->toBeInstanceOf(Organization::class);
    expect($subscription->organization->id)->toBe($org->id);
});
