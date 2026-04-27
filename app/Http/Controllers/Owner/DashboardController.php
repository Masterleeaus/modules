<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Inertia\ResponseFactory;

class DashboardController extends Controller
{
    /**
     * @param  Request  $request
     *
     * @return Response|ResponseFactory
     */
    public function index(Request $request): Response|ResponseFactory
    {
        $user  = $request->user();
        $orgId = $user->organization_id;
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        $jobsToday = Job::where('organization_id', $orgId)
            ->whereDate('scheduled_at', $today)
            ->whereNotIn('status', [Job::STATUS_CANCELLED])
            ->count();

        $revenueThisWeek = Invoice::where('organization_id', $orgId)
            ->where('status', Invoice::STATUS_PAID)
            ->whereBetween('paid_at', [$weekStart, $weekEnd])
            ->sum('total');

        $accountsReceivable = Invoice::where('organization_id', $orgId)
            ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE])
            ->sum('balance_due');

        $overdueInvoices = Invoice::where('organization_id', $orgId)
            ->where('status', Invoice::STATUS_OVERDUE)
            ->count();

        $openJobs = Job::where('organization_id', $orgId)
            ->whereNotIn('status', [Job::STATUS_COMPLETED, Job::STATUS_CANCELLED])
            ->count();

        $unassignedJobs = Job::where('organization_id', $orgId)
            ->whereNull('assigned_to')
            ->whereNotIn('status', [Job::STATUS_COMPLETED, Job::STATUS_CANCELLED])
            ->count();

        return inertia('Owner/Dashboard', [
            'user'  => $user,
            'stats' => [
                'jobs_today'          => $jobsToday,
                'revenue_this_week'   => (float) $revenueThisWeek,
                'accounts_receivable' => (float) $accountsReceivable,
                'overdue_invoices'    => $overdueInvoices,
                'open_jobs'           => $openJobs,
                'unassigned_jobs'     => $unassignedJobs,
            ],
        ]);
    }
}
