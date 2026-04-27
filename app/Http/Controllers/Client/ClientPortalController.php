<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientPortalToken;
use App\Models\Customer;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Response;
use Inertia\ResponseFactory;

class ClientPortalController extends Controller
{
    /**
     * Show the magic-link login form.
     */
    public function showLogin(): Response|ResponseFactory
    {
        return inertia('Client/Login');
    }

    /**
     * Send a magic-link email to the customer.
     */
    public function sendMagicLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $customer = Customer::where('email', $request->email)->first();

        if ($customer) {
            $token = ClientPortalToken::create([
                'customer_id' => $customer->id,
                'token'       => Str::random(64),
                'expires_at'  => now()->addHours(24),
            ]);

            $url = route('client.auth', ['token' => $token->token]);

            \Illuminate\Support\Facades\Mail::raw(
                "Click this link to access your client portal (expires in 24 hours):\n\n{$url}",
                fn ($m) => $m->to($customer->email, $customer->full_name)->subject('Your Client Portal Access Link')
            );
        }

        // Always return success to avoid email enumeration
        return back()->with('success', 'If that email matches an account, you will receive a login link shortly.');
    }

    /**
     * Authenticate via magic token and store customer in session.
     */
    public function authenticate(Request $request, string $token): RedirectResponse
    {
        $portalToken = ClientPortalToken::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $portalToken) {
            return redirect()->route('client.login')->withErrors(['token' => 'This link has expired or already been used.']);
        }

        $portalToken->update(['used_at' => now()]);

        session(['client_portal_customer_id' => $portalToken->customer_id]);

        return redirect()->route('client.dashboard');
    }

    /**
     * Client portal dashboard.
     */
    public function dashboard(Request $request): Response|ResponseFactory
    {
        $customer = $this->resolveCustomer($request);

        if (! $customer) {
            return redirect()->route('client.login');
        }

        $upcomingJobs = Job::where('customer_id', $customer->id)
            ->whereIn('status', [Job::STATUS_SCHEDULED, Job::STATUS_EN_ROUTE, Job::STATUS_IN_PROGRESS])
            ->orderBy('scheduled_at')
            ->with(['jobType', 'property', 'assignedTechnician'])
            ->limit(5)
            ->get()
            ->map(fn (Job $j) => $this->formatJob($j));

        $recentJobs = Job::where('customer_id', $customer->id)
            ->where('status', Job::STATUS_COMPLETED)
            ->orderByDesc('completed_at')
            ->with(['jobType', 'property', 'invoice', 'review'])
            ->limit(10)
            ->get()
            ->map(fn (Job $j) => $this->formatJob($j));

        $openInvoices = $customer->invoices()
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->orderBy('due_at')
            ->get(['id', 'invoice_number', 'total', 'balance_due', 'due_at', 'status']);

        return inertia('Client/Dashboard', [
            'customer'      => $customer->only('id', 'first_name', 'last_name', 'email'),
            'upcoming_jobs' => $upcomingJobs,
            'recent_jobs'   => $recentJobs,
            'open_invoices' => $openInvoices,
        ]);
    }

    /**
     * Logout — clear session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('client_portal_customer_id');
        return redirect()->route('client.login');
    }

    private function resolveCustomer(Request $request): ?Customer
    {
        $customerId = $request->session()->get('client_portal_customer_id');
        return $customerId ? Customer::find($customerId) : null;
    }

    private function formatJob(Job $job): array
    {
        return [
            'id'           => $job->id,
            'title'        => $job->title,
            'status'       => $job->status,
            'scheduled_at' => $job->scheduled_at?->toISOString(),
            'completed_at' => $job->completed_at?->toISOString(),
            'job_type'     => $job->jobType ? ['name' => $job->jobType->name, 'color' => $job->jobType->color] : null,
            'address'      => $job->property?->full_address,
            'technician'   => $job->assignedTechnician?->name,
            'invoice_id'   => $job->invoice?->id,
            'has_review'   => $job->relationLoaded('review') && $job->review !== null,
        ];
    }
}
