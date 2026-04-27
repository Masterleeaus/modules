<script setup lang="ts">
import { useLocationSharing } from '@/composables/useLocationSharing';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    title?: string;
}>();

const page = usePage();
const user = (page.props.auth as { user: { name: string } }).user;
const platform = computed(() => (page.props.platform ?? {}) as any);
const appName = computed(() => platform.value.app_name ?? 'TITAN ZERO');

const { enabled: locationEnabled, permissionDenied, toggle: toggleLocation } = useLocationSharing();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-slate-50">
        <!-- Top bar — pt accounts for iOS status bar notch -->
        <header class="sticky top-0 z-10 flex h-14 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm pt-[env(safe-area-inset-top,0px)]" style="height: calc(3.5rem + env(safe-area-inset-top, 0px))">
            <span class="text-base font-semibold text-slate-800">{{ title ?? appName }}</span>
            <div class="flex items-center gap-3 text-sm text-slate-500">
                <!-- Location sharing toggle -->
                <button
                    type="button"
                    class="flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium transition"
                    :class="locationEnabled
                        ? 'bg-green-100 text-green-700'
                        : 'bg-slate-100 text-slate-500'"
                    :title="permissionDenied ? 'Location permission denied' : (locationEnabled ? 'Sharing location' : 'Share location')"
                    @click="toggleLocation"
                >
                    <!-- location pin icon -->
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-2.083 3.952-5.125 3.952-8.577 0-4.85-3.922-8.773-8.75-8.773S3.75 7.65 3.75 12.5c0 3.452 2.008 6.494 3.952 8.577a19.58 19.58 0 002.683 2.282 16.975 16.975 0 001.144.742zM12.5 15a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" clip-rule="evenodd" />
                    </svg>
                    <span v-if="locationEnabled">On</span>
                    <span v-else>Off</span>
                </button>

                <span class="hidden sm:inline">{{ user?.name }}</span>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="rounded-md px-2 py-1 text-xs font-medium text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                >
                    Sign out
                </Link>
            </div>
        </header>

        <!-- Page content — pb accounts for bottom nav height + iOS home bar -->
        <main class="flex-1 pb-[calc(5rem+env(safe-area-inset-bottom,0px))]">
            <slot />
        </main>

        <!-- Bottom navigation — extends into iOS home bar safe area -->
        <nav class="fixed bottom-0 left-0 right-0 z-10 flex border-t border-slate-200 bg-white pb-[env(safe-area-inset-bottom,0px)]">
            <Link
                href="/technician/dashboard"
                class="flex min-h-[48px] flex-1 flex-col items-center justify-center gap-1 py-2 text-xs font-medium text-slate-500 hover:text-slate-900 active:text-slate-900"
            >
                <!-- home icon -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" />
                    <path d="M9 21V12h6v9" />
                </svg>
                Dashboard
            </Link>
            <Link
                href="/technician/jobs"
                class="flex min-h-[48px] flex-1 flex-col items-center justify-center gap-1 py-2 text-xs font-medium text-slate-500 hover:text-slate-900 active:text-slate-900"
            >
                <!-- map / route icon -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                Route
            </Link>
        </nav>
    </div>
</template>
