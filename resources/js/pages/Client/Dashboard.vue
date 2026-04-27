<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Customer, Invoice, Job, JobStatus } from '@/types';

// Client portal augments the Job with pre-formatted fields for display
type ClientJob = Pick<Job, 'id' | 'title' | 'scheduled_at' | 'completed_at'> & {
    status: JobStatus;
    job_type: { name: string; color: string } | null;
    address: string | null;
    technician: string | null;
    invoice_id: number | null;
    review_token: string;
    has_review: boolean;
};

const props = defineProps<{
    customer: Customer;
    upcoming_jobs: ClientJob[];
    recent_jobs: ClientJob[];
    open_invoices: Invoice[];
}>();

const logoutForm = useForm({});

function formatDate(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatTime(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

function statusLabel(status: string): string {
    const map: Record<string, string> = {
        scheduled: 'Scheduled', en_route: 'En Route', in_progress: 'In Progress',
        completed: 'Completed', cancelled: 'Cancelled', on_hold: 'On Hold',
    };
    return map[status] ?? status;
}
</script>

<template>
    <Head title="My Portal" />

    <div class="min-h-screen bg-slate-50">
        <!-- Header -->
        <header class="border-b border-slate-200 bg-white px-6 py-4">
            <div class="mx-auto flex max-w-4xl items-center justify-between">
                <p class="font-semibold text-slate-800">Hi, {{ customer.first_name }}!</p>
                <form @submit.prevent="logoutForm.post('/client/logout')">
                    <button type="submit" class="text-sm text-slate-400 hover:text-slate-600">Sign out</button>
                </form>
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-6 py-8 space-y-8">
            <!-- Open Invoices -->
            <section v-if="open_invoices.length > 0">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Open Invoices</h2>
                <div class="space-y-2">
                    <div
                        v-for="inv in open_invoices"
                        :key="inv.id"
                        class="flex items-center justify-between rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-slate-200"
                    >
                        <div>
                            <p class="text-sm font-medium text-slate-800">Invoice #{{ inv.invoice_number }}</p>
                            <p class="text-xs text-slate-500">Due {{ formatDate(inv.due_at) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-rose-600">${{ Number(inv.balance_due).toFixed(2) }} due</p>
                            <span class="text-xs capitalize text-slate-400">{{ inv.status }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Upcoming Jobs -->
            <section>
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Upcoming Appointments</h2>
                <div v-if="upcoming_jobs.length === 0" class="rounded-xl bg-white px-5 py-6 text-center text-sm text-slate-400 shadow-sm ring-1 ring-slate-200">
                    No upcoming appointments.
                </div>
                <div v-else class="space-y-2">
                    <div
                        v-for="job in upcoming_jobs"
                        :key="job.id"
                        class="rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-slate-200"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800">{{ job.title }}</p>
                                <p v-if="job.address" class="mt-0.5 text-xs text-slate-500">{{ job.address }}</p>
                                <p v-if="job.technician" class="mt-0.5 text-xs text-slate-400">Tech: {{ job.technician }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-medium text-slate-700">{{ formatDate(job.scheduled_at) }}</p>
                                <p class="text-xs text-slate-400">{{ formatTime(job.scheduled_at) }}</p>
                                <span
                                    class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-blue-50 text-blue-700"
                                >{{ statusLabel(job.status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Past Jobs -->
            <section v-if="recent_jobs.length > 0">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Recent Services</h2>
                <div class="space-y-2">
                    <div
                        v-for="job in recent_jobs"
                        :key="job.id"
                        class="flex items-center justify-between rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-slate-200"
                    >
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ job.title }}</p>
                            <p class="text-xs text-slate-500">Completed {{ formatDate(job.completed_at) }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <Link
                                v-if="!job.has_review"
                                :href="`/review/${job.review_token}`"
                                class="text-xs text-slate-400 hover:text-slate-700 underline"
                            >
                                Leave review
                            </Link>
                            <span v-else class="text-xs text-green-600">Reviewed ✓</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>
