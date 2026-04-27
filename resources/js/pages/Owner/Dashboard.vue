<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Stats {
    jobs_today: number;
    revenue_this_week: number;
    accounts_receivable: number;
    overdue_invoices: number;
    open_jobs: number;
    unassigned_jobs: number;
}

interface ChecklistItem {
    key: string;
    label: string;
    done: boolean;
    href: string;
}

interface OnboardingChecklist {
    items: ChecklistItem[];
    all_done: boolean;
}

const props = defineProps<{
    stats: Stats;
    onboarding_checklist: OnboardingChecklist | null;
}>();

const checklistDismissed = ref(false);

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}

function completedCount(): number {
    return props.onboarding_checklist?.items.filter(i => i.done).length ?? 0;
}
</script>

<template>
    <OwnerLayout title="Owner Dashboard">
        <Head title="Owner Dashboard" />

        <!-- Post-setup checklist widget -->
        <div
            v-if="onboarding_checklist && !onboarding_checklist.all_done && !checklistDismissed"
            class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-5"
        >
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-blue-900 mb-0.5">Get started checklist</h2>
                    <p class="text-xs text-blue-700">
                        {{ completedCount() }} of {{ onboarding_checklist.items.length }} tasks complete
                    </p>
                </div>
                <button
                    type="button"
                    class="text-blue-400 hover:text-blue-600 transition-colors"
                    @click="checklistDismissed = true"
                    title="Dismiss"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Progress bar -->
            <div class="mt-3 h-1.5 w-full rounded-full bg-blue-200 overflow-hidden">
                <div
                    class="h-full rounded-full bg-blue-500 transition-all"
                    :style="{ width: `${(completedCount() / onboarding_checklist.items.length) * 100}%` }"
                />
            </div>

            <ul class="mt-4 space-y-2">
                <li
                    v-for="item in onboarding_checklist.items"
                    :key="item.key"
                    class="flex items-center gap-3"
                >
                    <span
                        class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition-colors"
                        :class="item.done ? 'bg-blue-500 border-blue-500 text-white' : 'border-blue-300'"
                    >
                        <svg v-if="item.done" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <component
                        :is="item.done ? 'span' : Link"
                        :href="item.done ? undefined : item.href"
                        class="text-sm transition-colors"
                        :class="item.done ? 'text-blue-400 line-through' : 'text-blue-800 hover:text-blue-600 hover:underline'"
                    >
                        {{ item.label }}
                    </component>
                </li>
            </ul>
        </div>

        <!-- KPI cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jobs Today</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ stats.jobs_today }}</p>
                <Link href="/owner/jobs" class="mt-2 inline-block text-xs text-slate-400 hover:text-slate-700 transition-colors">View jobs →</Link>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Revenue This Week</p>
                <p class="mt-2 text-3xl font-bold text-green-600">{{ formatCurrency(stats.revenue_this_week) }}</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Accounts Receivable</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">{{ formatCurrency(stats.accounts_receivable) }}</p>
                <Link href="/owner/invoices" class="mt-2 inline-block text-xs text-slate-400 hover:text-slate-700 transition-colors">View invoices →</Link>
            </div>

            <div class="bg-white rounded-xl border border-rose-100 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Overdue Invoices</p>
                <p class="mt-2 text-3xl font-bold text-rose-600">{{ stats.overdue_invoices }}</p>
                <Link href="/owner/billing" class="mt-2 inline-block text-xs text-slate-400 hover:text-slate-700 transition-colors">View billing →</Link>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Open Jobs</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ stats.open_jobs }}</p>
            </div>

            <div class="bg-white rounded-xl border border-amber-100 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Unassigned Jobs</p>
                <p class="mt-2 text-3xl font-bold text-amber-600">{{ stats.unassigned_jobs }}</p>
                <Link href="/owner/dispatch" class="mt-2 inline-block text-xs text-slate-400 hover:text-slate-700 transition-colors">Open dispatch →</Link>
            </div>
        </section>

        <!-- Quick links to reports -->
        <section>
            <h2 class="text-sm font-semibold text-slate-600 mb-3 uppercase tracking-wide">Reports</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <Link
                    href="/owner/reports/jobs-by-type"
                    class="block bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:border-slate-300 hover:shadow transition-all"
                >
                    <p class="font-semibold text-slate-800">Jobs by Type</p>
                    <p class="text-xs text-slate-500 mt-1">Breakdown of jobs grouped by service type</p>
                </Link>
                <Link
                    href="/owner/reports/job-profitability"
                    class="block bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:border-slate-300 hover:shadow transition-all"
                >
                    <p class="font-semibold text-slate-800">Job Profitability</p>
                    <p class="text-xs text-slate-500 mt-1">Revenue vs. parts cost per job</p>
                </Link>
                <Link
                    href="/owner/reports/technician-performance"
                    class="block bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:border-slate-300 hover:shadow transition-all"
                >
                    <p class="font-semibold text-slate-800">Technician Performance</p>
                    <p class="text-xs text-slate-500 mt-1">Jobs completed, revenue, and avg duration</p>
                </Link>
            </div>
        </section>
    </OwnerLayout>
</template>
