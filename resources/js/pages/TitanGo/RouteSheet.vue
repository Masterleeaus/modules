<script setup lang="ts">
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Job {
    id: number;
    title: string;
    status: string;
    scheduled_at: string | null;
    customer: { id: number; first_name: string; last_name: string } | null;
    property: {
        id: number;
        address_line1: string;
        city: string;
        state: string;
        latitude?: number | null;
        longitude?: number | null;
    } | null;
    job_type: { id: number; name: string; color: string } | null;
}

const props = defineProps<{
    jobs: Job[];
    statuses: Record<string, string>;
}>();

const STATUS_CLASSES: Record<string, string> = {
    scheduled:   'bg-blue-100 text-blue-700',
    en_route:    'bg-purple-100 text-purple-700',
    in_progress: 'bg-amber-100 text-amber-700',
    completed:   'bg-green-100 text-green-700',
    cancelled:   'bg-slate-100 text-slate-500',
    on_hold:     'bg-orange-100 text-orange-700',
};

function formatTime(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

function openDirections(job: Job): void {
    const addr = job.property;
    if (!addr) return;
    const query = encodeURIComponent(`${addr.address_line1}, ${addr.city}, ${addr.state}`);
    // Try Apple Maps on iOS, fall back to Google Maps
    const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
    const url = isIos
        ? `maps://maps.apple.com/?q=${query}`
        : `https://www.google.com/maps/search/?api=1&query=${query}`;
    window.open(url, '_blank');
}

const swipingJob = ref<number | null>(null);
const swipeStartX = ref(0);

function onTouchStart(e: TouchEvent, jobId: number): void {
    swipingJob.value = jobId;
    swipeStartX.value = e.touches[0].clientX;
}

function onTouchEnd(e: TouchEvent, job: Job): void {
    if (swipingJob.value !== job.id) return;
    const dx = e.changedTouches[0].clientX - swipeStartX.value;
    if (dx > 60 && job.status === 'scheduled') {
        // Swipe right → mark en_route
        router.patch(`/api/technician/jobs/${job.id}/status`, { status: 'en_route' }, {
            preserveScroll: true,
        });
    }
    swipingJob.value = null;
}
</script>

<template>
    <TechnicianLayout title="Route Sheet">
        <Head title="Today's Route Sheet" />

        <div class="p-4">
            <div v-if="jobs.length === 0" class="rounded-xl bg-white py-12 text-center shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">No jobs scheduled for today.</p>
            </div>

            <ul v-else class="space-y-3">
                <li
                    v-for="(job, index) in jobs"
                    :key="job.id"
                    @touchstart="onTouchStart($event, job.id)"
                    @touchend="onTouchEnd($event, job)"
                >
                    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200 active:ring-slate-400">
                        <!-- Route order badge -->
                        <span class="absolute left-3 top-3 flex h-6 w-6 items-center justify-center rounded-full bg-slate-800 text-xs font-bold text-white">
                            {{ index + 1 }}
                        </span>

                        <Link :href="`/technician/jobs/${job.id}`" class="block p-4 pl-12">
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <p class="font-semibold text-slate-900">{{ job.title }}</p>
                                <span
                                    class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="STATUS_CLASSES[job.status] ?? 'bg-slate-100 text-slate-600'"
                                >
                                    {{ statuses[job.status] ?? job.status }}
                                </span>
                            </div>

                            <div class="space-y-1 text-sm text-slate-500">
                                <p v-if="job.customer">
                                    {{ job.customer.first_name }} {{ job.customer.last_name }}
                                </p>
                                <p v-if="job.property">
                                    {{ job.property.address_line1 }}, {{ job.property.city }}, {{ job.property.state }}
                                </p>
                                <div class="flex items-center justify-between pt-1">
                                    <span v-if="job.job_type" class="inline-flex items-center gap-1.5">
                                        <span class="h-2 w-2 rounded-full" :style="{ background: job.job_type.color }" />
                                        {{ job.job_type.name }}
                                    </span>
                                    <span class="ml-auto text-xs">{{ formatTime(job.scheduled_at) }}</span>
                                </div>
                            </div>
                        </Link>

                        <!-- Get Directions button -->
                        <div v-if="job.property" class="border-t border-slate-100 px-4 py-2">
                            <button
                                type="button"
                                class="flex w-full items-center justify-center gap-2 rounded-lg py-2 text-xs font-medium text-blue-600 active:bg-blue-50"
                                @click="openDirections(job)"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 12h18m-7-7 7 7-7 7" />
                                </svg>
                                Get Directions
                            </button>
                        </div>
                    </div>
                </li>
            </ul>

            <p class="mt-4 text-center text-xs text-slate-400">
                Swipe right on a scheduled job to mark En Route
            </p>
        </div>
    </TechnicianLayout>
</template>
