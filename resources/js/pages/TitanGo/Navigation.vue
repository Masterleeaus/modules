<script setup lang="ts">
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';
import { Head } from '@inertiajs/vue3';

interface Property {
    address_line1: string;
    city: string;
    state: string;
    postal_code?: string;
    latitude?: number | null;
    longitude?: number | null;
}

const props = defineProps<{
    address: Property;
    jobTitle?: string;
}>();

function buildMapsUrl(type: 'google' | 'apple' | 'waze'): string {
    const fullAddress = [
        props.address.address_line1,
        props.address.city,
        props.address.state,
        props.address.postal_code,
    ].filter(Boolean).join(', ');

    const encodedAddress = encodeURIComponent(fullAddress);

    if (type === 'apple') {
        return props.address.latitude && props.address.longitude
            ? `maps://maps.apple.com/?ll=${props.address.latitude},${props.address.longitude}&q=${encodedAddress}`
            : `maps://maps.apple.com/?q=${encodedAddress}`;
    }
    if (type === 'waze') {
        return props.address.latitude && props.address.longitude
            ? `https://waze.com/ul?ll=${props.address.latitude},${props.address.longitude}&navigate=yes`
            : `https://waze.com/ul?q=${encodedAddress}&navigate=yes`;
    }
    // Google Maps
    return props.address.latitude && props.address.longitude
        ? `https://www.google.com/maps/dir/?api=1&destination=${props.address.latitude},${props.address.longitude}`
        : `https://www.google.com/maps/search/?api=1&query=${encodedAddress}`;
}

const isIos = typeof navigator !== 'undefined' && /iphone|ipad|ipod/i.test(navigator.userAgent);
const isAndroid = typeof navigator !== 'undefined' && /android/i.test(navigator.userAgent);
</script>

<template>
    <TechnicianLayout title="Navigation">
        <Head title="Navigation" />

        <div class="p-4">
            <div class="mb-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p v-if="jobTitle" class="mb-1 text-sm font-semibold text-slate-800">{{ jobTitle }}</p>
                <p class="text-sm text-slate-600">
                    {{ address.address_line1 }}, {{ address.city }}, {{ address.state }}
                    <span v-if="address.postal_code"> {{ address.postal_code }}</span>
                </p>
            </div>

            <div class="space-y-3">
                <!-- Apple Maps — shown on iOS -->
                <a
                    v-if="isIos"
                    :href="buildMapsUrl('apple')"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200 active:ring-slate-400"
                >
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-xl">🗺️</span>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Apple Maps</p>
                        <p class="text-xs text-slate-500">Open in Apple Maps</p>
                    </div>
                </a>

                <!-- Google Maps -->
                <a
                    :href="buildMapsUrl('google')"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200 active:ring-slate-400"
                >
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-xl">📍</span>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Google Maps</p>
                        <p class="text-xs text-slate-500">Open in Google Maps</p>
                    </div>
                </a>

                <!-- Waze -->
                <a
                    :href="buildMapsUrl('waze')"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200 active:ring-slate-400"
                >
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-cyan-100 text-xl">🚗</span>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Waze</p>
                        <p class="text-xs text-slate-500">Open in Waze</p>
                    </div>
                </a>
            </div>
        </div>
    </TechnicianLayout>
</template>
