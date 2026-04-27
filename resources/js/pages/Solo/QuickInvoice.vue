<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Job {
    id: number;
    title: string;
    customer: { id: number; first_name: string; last_name: string; email: string | null } | null;
    property: { address_line1: string; city: string; state: string } | null;
    scheduled_at: string | null;
}

const props = defineProps<{
    job: Job;
    tax_rate: number;
}>();

const form = useForm({
    line_items: [{ description: '', quantity: 1, unit_price: 0 }] as { description: string; quantity: number; unit_price: number }[],
    notes: '',
    tax_rate: props.tax_rate,
});

function addLine() {
    form.line_items.push({ description: '', quantity: 1, unit_price: 0 });
}

function removeLine(index: number) {
    form.line_items.splice(index, 1);
}

const subtotal = () =>
    form.line_items.reduce((sum, item) => sum + item.quantity * item.unit_price, 0);

const tax = () => subtotal() * (form.tax_rate / 100);
const total = () => subtotal() + tax();

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}

function submit() {
    form.post(`/owner/jobs/${props.job.id}/invoice`);
}
</script>

<template>
    <OwnerLayout title="Quick Invoice">
        <Head title="Quick Invoice" />

        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/jobs" class="hover:underline">Jobs</Link>
            <span class="mx-1">›</span>
            <Link :href="`/owner/jobs/${job.id}`" class="hover:underline">{{ job.title }}</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">Quick Invoice</span>
        </nav>

        <div class="max-w-2xl">
            <!-- Job summary -->
            <div class="mb-4 rounded-xl bg-violet-50 border border-violet-200 px-4 py-3 text-sm">
                <p class="font-semibold text-violet-900">{{ job.title }}</p>
                <p v-if="job.customer" class="text-violet-700">
                    {{ job.customer.last_name }}, {{ job.customer.first_name }}
                </p>
                <p v-if="job.property" class="text-violet-600 text-xs">
                    {{ job.property.address_line1 }}, {{ job.property.city }}, {{ job.property.state }}
                </p>
            </div>

            <div class="rounded-xl bg-white shadow">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Invoice Details</h2>
                </div>

                <form @submit.prevent="submit" class="px-6 py-5 space-y-5">
                    <!-- Line items -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-slate-700">Line Items</label>
                            <button
                                type="button"
                                class="text-xs text-slate-500 hover:text-slate-800 underline"
                                @click="addLine"
                            >
                                + Add line
                            </button>
                        </div>

                        <div class="space-y-2">
                            <div
                                v-for="(item, idx) in form.line_items"
                                :key="idx"
                                class="flex items-center gap-2"
                            >
                                <input
                                    v-model="item.description"
                                    type="text"
                                    placeholder="Description"
                                    class="flex-1 rounded border border-slate-200 px-2 py-1.5 text-sm focus:border-slate-400 focus:outline-none"
                                />
                                <input
                                    v-model.number="item.quantity"
                                    type="number"
                                    min="1"
                                    step="1"
                                    placeholder="Qty"
                                    class="w-16 rounded border border-slate-200 px-2 py-1.5 text-sm text-center focus:border-slate-400 focus:outline-none"
                                />
                                <input
                                    v-model.number="item.unit_price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="Price"
                                    class="w-24 rounded border border-slate-200 px-2 py-1.5 text-sm text-right focus:border-slate-400 focus:outline-none"
                                />
                                <button
                                    v-if="form.line_items.length > 1"
                                    type="button"
                                    class="text-slate-400 hover:text-red-500 transition-colors"
                                    @click="removeLine(idx)"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tax rate -->
                    <div class="max-w-xs">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tax Rate (%)</label>
                        <input
                            v-model.number="form.tax_rate"
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            class="w-full rounded border border-slate-200 px-2 py-1.5 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes (optional)</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            class="w-full rounded border border-slate-200 px-2 py-1.5 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>

                    <!-- Totals -->
                    <div class="rounded-lg bg-slate-50 px-4 py-3 space-y-1 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span>{{ formatCurrency(subtotal()) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Tax ({{ form.tax_rate }}%)</span>
                            <span>{{ formatCurrency(tax()) }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-slate-800 border-t border-slate-200 pt-1">
                            <span>Total</span>
                            <span>{{ formatCurrency(total()) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            Generate Invoice
                        </button>
                        <Link :href="`/owner/jobs/${job.id}`" class="text-sm text-slate-500 hover:text-slate-700">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>
