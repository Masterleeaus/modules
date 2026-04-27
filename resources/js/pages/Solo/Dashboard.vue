<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Job {
    id: number;
    title: string;
    status: string;
    scheduled_at: string | null;
    customer: { first_name: string; last_name: string } | null;
    property: { address_line1: string; city: string; state: string } | null;
}

interface Stats {
    jobs_today: number;
    open_jobs: number;
    accounts_receivable: number;
}

const props = defineProps<{
    today_jobs: Job[];
    stats: Stats;
}>();

const STATUS_LABELS: Record<string, string> = {
    scheduled: 'Scheduled',
    in_progress: 'In Progress',
    completed: 'Completed',
    cancelled: 'Cancelled',
};

const STATUS_COLOURS: Record<string, string> = {
    scheduled:   'bg-blue-100 text-blue-700',
    in_progress: 'bg-amber-100 text-amber-700',
    completed:   'bg-green-100 text-green-700',
    cancelled:   'bg-slate-100 text-slate-500',
};

function formatTime(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}

function startJob(jobId: number) {
    router.patch(`/owner/jobs/${jobId}/status`, { status: 'in_progress' });
}

function completeJob(jobId: number) {
    router.patch(`/owner/jobs/${jobId}/status`, { status: 'completed' });
}
</script>

<template>
    <OwnerLayout title="My Day">
        <Head title="My Day — Solo Dashboard" />

        <!-- KPI strip -->
        <section class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm text-center">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jobs Today</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.jobs_today }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm text-center">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Open Jobs</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.open_jobs }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm text-center">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Receivable</p>
                <p class="mt-1 text-2xl font-bold text-blue-600">{{ formatCurrency(stats.accounts_receivable) }}</p>
            </div>
        </section>

        <!-- Quick actions -->
        <div class="flex gap-3 mb-6">
            <Link
                href="/owner/jobs/create"
                class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 py-3 text-sm font-medium text-white hover:bg-slate-700 transition-colors"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                New Job
            </Link>
            <Link
                href="/owner/invoices"
                class="flex-1 flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 hover:border-slate-300 transition-colors"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                </svg>
                Invoices
            </Link>
        </div>

        <!-- Today's jobs -->
        <section>
            <h2 class="text-sm font-semibold text-slate-600 mb-3 uppercase tracking-wide">Today's Jobs</h2>

            <div v-if="today_jobs.length === 0" class="rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
                No jobs scheduled for today. <Link href="/owner/jobs/create" class="text-slate-700 underline hover:no-underline">Create one →</Link>
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="job in today_jobs"
                    :key="job.id"
                    class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="STATUS_COLOURS[job.status] ?? 'bg-slate-100 text-slate-600'"
                                >
                                    {{ STATUS_LABELS[job.status] ?? job.status }}
                                </span>
                                <span class="text-xs text-slate-400">{{ formatTime(job.scheduled_at) }}</span>
                            </div>
                            <p class="mt-1 font-semibold text-slate-800 truncate">{{ job.title }}</p>
                            <p v-if="job.customer" class="text-xs text-slate-500">
                                {{ job.customer.last_name }}, {{ job.customer.first_name }}
                            </p>
                            <p v-if="job.property" class="text-xs text-slate-400">
                                {{ job.property.address_line1 }}, {{ job.property.city }}
                            </p>
                        </div>

                        <!-- One-tap actions -->
                        <div class="flex flex-col gap-2 shrink-0">
                            <button
                                v-if="job.status === 'scheduled'"
                                class="rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-amber-600 transition-colors"
                                @click="startJob(job.id)"
                            >
                                Start
                            </button>
                            <button
                                v-if="job.status === 'in_progress'"
                                class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700 transition-colors"
                                @click="completeJob(job.id)"
                            >
                                Complete
                            </button>
                            <Link
                                :href="`/owner/jobs/${job.id}`"
                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-slate-300 transition-colors text-center"
                            >
                                View
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </OwnerLayout>
</template>
