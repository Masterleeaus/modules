<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps<{ title?: string }>();

const page = usePage();
const currentUrl = computed(() => page.url);
const platform = computed(() => (page.props.platform ?? {}) as any);
const appName = computed(() => platform.value.app_name ?? 'TITAN ZERO');
const logoUrl = computed(() => platform.value.logo_url ?? '/titan-zero-logo.png');
const sidebarOpen = ref(false);

function navClass(href: string): string {
    return currentUrl.value.startsWith(href)
        ? 'block rounded-md bg-indigo-600 px-3 py-2 font-medium text-white'
        : 'block rounded-md px-3 py-2 text-slate-300 transition-colors hover:bg-slate-800 hover:text-white';
}
</script>

<template>
    <div class="flex min-h-screen bg-slate-100">
        <div v-if="sidebarOpen" class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false" />

        <aside
            class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-slate-950 text-slate-100 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="border-b border-slate-800 px-6 py-4">
                <div class="flex items-center gap-3">
                    <img v-if="logoUrl" :src="logoUrl" :alt="appName" class="h-9 max-w-[190px] object-contain" />
                    <div v-else class="text-xl font-bold tracking-tight">{{ appName }}</div>
                </div>
                <div class="mt-2 text-xs font-semibold uppercase tracking-wider text-indigo-300">Platform Admin</div>
            </div>

            <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4 text-sm">
                <Link href="/platform/dashboard" :class="navClass('/platform/dashboard')">SaaS Dashboard</Link>

                <div class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Tenant tools</div>
                <a href="#organizations" class="block rounded-md px-3 py-2 text-slate-300 transition-colors hover:bg-slate-800 hover:text-white">Organizations</a>
                <a href="#subscriptions" class="block rounded-md px-3 py-2 text-slate-300 transition-colors hover:bg-slate-800 hover:text-white">Subscriptions</a>

                <div class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-slate-500">App</div>
                <Link href="/owner/dashboard" class="block rounded-md px-3 py-2 text-slate-300 transition-colors hover:bg-slate-800 hover:text-white">Owner dashboard</Link>
            </nav>

            <div class="border-t border-slate-800 px-4 py-3">
                <Link href="/logout" method="post" as="button" class="w-full text-left text-sm text-slate-400 transition-colors hover:text-white">
                    Sign out
                </Link>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-10 flex h-14 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm sm:px-6">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="-ml-1 flex h-9 w-9 items-center justify-center rounded-md text-slate-500 hover:text-slate-900 lg:hidden"
                        aria-label="Open navigation"
                        @click="sidebarOpen = !sidebarOpen"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="truncate text-base font-semibold text-slate-800">{{ title ?? 'Platform Admin' }}</h1>
                </div>
                <div class="text-sm text-slate-600"><slot name="header-actions" /></div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
