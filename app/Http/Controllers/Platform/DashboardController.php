<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $organizations = Organization::query()
            ->withCount(['users', 'customers', 'jobs', 'invoices'])
            ->with(['subscriptions' => fn ($query) => $query->latest()->limit(1)])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Organization $organization) => [
                'id' => $organization->id,
                'name' => $organization->name,
                'slug' => $organization->slug,
                'plan' => $organization->plan,
                'trial_ends_at' => $organization->trial_ends_at?->toIso8601String(),
                'stripe_customer_id' => $organization->stripe_customer_id,
                'users_count' => $organization->users_count,
                'customers_count' => $organization->customers_count,
                'jobs_count' => $organization->jobs_count,
                'invoices_count' => $organization->invoices_count,
                'subscription' => $organization->subscriptions->first() ? [
                    'id' => $organization->subscriptions->first()->id,
                    'plan' => $organization->subscriptions->first()->plan,
                    'status' => $organization->subscriptions->first()->status,
                    'billing_interval' => $organization->subscriptions->first()->billing_interval,
                    'trial_ends_at' => $organization->subscriptions->first()->trial_ends_at?->toIso8601String(),
                    'current_period_end' => $organization->subscriptions->first()->current_period_end?->toIso8601String(),
                ] : null,
            ]);

        return Inertia::render('Platform/Dashboard', [
            'stats' => [
                'organizations' => Organization::count(),
                'users' => User::count(),
                'active_subscriptions' => Subscription::whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIALING])->count(),
                'expired_trials' => Subscription::where('status', Subscription::STATUS_TRIALING)
                    ->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '<', now())
                    ->count(),
            ],
            'organizations' => $organizations,
            'plans' => ['starter', 'growth', 'pro'],
            'statuses' => [
                Subscription::STATUS_TRIALING,
                Subscription::STATUS_ACTIVE,
                Subscription::STATUS_PAST_DUE,
                Subscription::STATUS_CANCELED,
                Subscription::STATUS_PAUSED,
            ],
        ]);
    }

    public function updateOrganization(Request $request, Organization $organization): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:organizations,slug,'.$organization->id],
            'plan' => ['required', 'in:starter,growth,pro'],
            'trial_ends_at' => ['nullable', 'date'],
        ]);

        $organization->update($data);
        $this->forgetOrganizationCache($organization->id);

        return back()->with('success', 'Organization updated.');
    }

    public function updateSubscription(Request $request, Organization $organization): RedirectResponse
    {
        $data = $request->validate([
            'plan' => ['required', 'in:starter,growth,pro'],
            'status' => ['required', 'in:trialing,active,past_due,canceled,paused'],
            'billing_interval' => ['required', 'in:monthly,annual'],
            'trial_ends_at' => ['nullable', 'date'],
            'current_period_end' => ['nullable', 'date'],
        ]);

        $subscription = $organization->activeSubscription()
            ?? $organization->subscriptions()->latest()->first()
            ?? new Subscription(['organization_id' => $organization->id]);

        $subscription->fill($data);
        $subscription->organization_id = $organization->id;
        $subscription->save();

        $organization->update(['plan' => $data['plan']]);
        $this->forgetOrganizationCache($organization->id);

        return back()->with('success', 'Subscription updated.');
    }

    public function extendTrial(Organization $organization): RedirectResponse
    {
        $subscription = $organization->activeSubscription()
            ?? $organization->subscriptions()->latest()->first()
            ?? new Subscription(['organization_id' => $organization->id]);

        $subscription->fill([
            'plan' => $organization->plan ?: 'growth',
            'status' => Subscription::STATUS_TRIALING,
            'billing_interval' => $subscription->billing_interval ?: 'monthly',
            'trial_ends_at' => now()->addDays(30),
        ]);
        $subscription->organization_id = $organization->id;
        $subscription->save();

        $organization->update(['trial_ends_at' => now()->addDays(30)]);
        $this->forgetOrganizationCache($organization->id);

        return back()->with('success', 'Trial extended by 30 days.');
    }

    public function activate(Organization $organization): RedirectResponse
    {
        $subscription = $organization->activeSubscription()
            ?? $organization->subscriptions()->latest()->first()
            ?? new Subscription(['organization_id' => $organization->id]);

        $subscription->fill([
            'plan' => $organization->plan ?: 'pro',
            'status' => Subscription::STATUS_ACTIVE,
            'billing_interval' => $subscription->billing_interval ?: 'monthly',
            'trial_ends_at' => null,
            'current_period_start' => now(),
            'current_period_end' => now()->addYear(),
        ]);
        $subscription->organization_id = $organization->id;
        $subscription->save();

        $organization->update(['plan' => $subscription->plan, 'trial_ends_at' => null]);
        $this->forgetOrganizationCache($organization->id);

        return back()->with('success', 'Organization activated.');
    }

    private function forgetOrganizationCache(int $organizationId): void
    {
        Cache::forget("org.{$organizationId}.active_subscription");
        Cache::forget("org.{$organizationId}.active_plan");
        Cache::forget("org.{$organizationId}.tech_count");
    }
}
