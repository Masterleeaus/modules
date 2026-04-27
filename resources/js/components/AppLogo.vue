<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const platform = computed(() => (page.props.platform ?? {}) as any);
const appName = computed(() => platform.value.app_name ?? 'TITAN ZERO');
const logoUrl = computed(() => platform.value.logo_url ?? '/titan-zero-logo.png');
</script>

<template>
    <img
        v-if="logoUrl"
        :src="logoUrl"
        :alt="appName"
        class="h-9 w-auto max-w-[180px] object-contain"
    />
    <template v-else>
        <div
            class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
        >
            <AppLogoIcon class="size-5 fill-current text-white" />
        </div>
        <div class="ml-1 grid flex-1 text-left text-sm">
            <span class="mb-0.5 truncate leading-tight font-semibold">{{ appName }}</span>
        </div>
    </template>
</template>
