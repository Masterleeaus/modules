<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Job {
    id: number;
    title: string;
    status: string;
    scheduled_at: string | null;
    customer: { first_name: string; last_name: string } | null;
    property: { address_line1: string; city: string; state: string } | null;
    invoice: { id: number } | null;
}

interface PaginatedJobs {
    data: Job[];
    links: { url: string | null; label: string; active: boolean }[];
    meta: { current_page: number; last_page: number; total: number };
}

const props = defineProps<{
    jobs: PaginatedJobs;
    filters: { search?: string; status?: string };
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

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

function applyFilters() {
    router.get('/owner/jobs', {
        search: search.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
}

function formatDate(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function startJob(jobId: number) {
    router.patch(`/owner/jobs/${jobId}/status`, { status: 'in_progress' });
}

function completeJob(jobId: number) {
    router.patch(`/owner/jobs/${jobId}/status`, { status: 'completed' });
}
</script>

<template>
    <OwnerLayout title="My Jobs">
        <Head title="My Jobs" />

        <div class="mb-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-slate-800">My Jobs</h2>
            <Link
                href="/owner/jobs/create"
                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                New Job
            </Link>
        </div>

        <!-- Filters -->
        <div class="mb-4 flex flex-col sm:flex-row gap-3">
            <input
                v-model="search"
                type="search"
                placeholder="Search jobs or customers…"
                class="w-full sm:w-64 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                @keyup.enter="applyFilters"
            />
            <select
                v-model="status"
                class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                @change="applyFilters"
            >
                <option value="">All statuses</option>
                <option v-for="(label, key) in STATUS_LABELS" :key="key" :value="key">{{ label }}</option>
            </select>
        </div>

        <!-- Jobs list -->
        <div v-if="jobs.data.length === 0" class="rounded-xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-500">
            No jobs found.
        </div>

        <div v-else class="space-y-3">
            <div
                v-for="job in jobs.data"
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
                            <span class="text-xs text-slate-400">{{ formatDate(job.scheduled_at) }}</span>
                        </div>
                        <p class="mt-1 font-semibold text-slate-800 truncate">{{ job.title }}</p>
                        <p v-if="job.customer" class="text-xs text-slate-500">
                            {{ job.customer.last_name }}, {{ job.customer.first_name }}
                        </p>
                        <p v-if="job.property" class="text-xs text-slate-400">
                            {{ job.property.address_line1 }}, {{ job.property.city }}
                        </p>
                    </div>

                    <!-- Actions -->
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

        <!-- Pagination -->
        <div v-if="jobs.meta.last_page > 1" class="mt-6 flex justify-center gap-1">
            <template v-for="link in jobs.links" :key="link.label">
                <component
                    :is="link.url ? Link : 'span'"
                    :href="link.url ?? undefined"
                    class="px-3 py-1.5 rounded text-sm"
                    :class="link.active
                        ? 'bg-slate-800 text-white'
                        : link.url
                            ? 'border border-slate-200 text-slate-600 hover:border-slate-300'
                            : 'border border-slate-100 text-slate-300 cursor-not-allowed'"
                    v-html="link.label"
                />
            </template>
        </div>
    </OwnerLayout>
</template>
