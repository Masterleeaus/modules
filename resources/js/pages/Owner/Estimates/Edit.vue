<script setup lang="ts">
import EstimateForm from './EstimateForm.vue';
import type { Customer, Estimate, EstimatePackage, Item, EstimateTier } from '@/types';

defineProps<{
    estimate: Estimate;
    customers: Customer[];
    jobs: { id: number; title: string; scheduled_at: string | null; customer_id: number }[];
    catalogItems: Item[];
    tiers: EstimateTier[];
    statuses: Record<string, string>;
}>();
</script>

<template>
    <EstimateForm
        :estimate="estimate"
        :customers="customers"
        :jobs="jobs"
        :catalog-items="catalogItems"
        :tiers="tiers"
        :statuses="statuses"
    />
</template>
