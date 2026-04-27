<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

interface Job {
    id: number;
    token: string;
    title: string;
    technician: string | null;
    job_type: string | null;
    completed_at: string | null;
}

const props = defineProps<{ job: Job }>();

const form = useForm({
    rating: 5,
    comment: '',
    tip_amount: '',
});

function submit() {
    form.post(`/review/${props.job.token}`);
}
</script>

<template>
    <Head title="Rate Your Service" />

    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <h1 class="mb-1 text-xl font-bold text-slate-800">How did we do?</h1>
            <p class="mb-1 text-sm text-slate-600">{{ job.title }}</p>
            <p v-if="job.technician" class="mb-6 text-sm text-slate-400">Technician: {{ job.technician }}</p>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Star Rating -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Your rating</label>
                    <div class="flex gap-2">
                        <button
                            v-for="star in [1, 2, 3, 4, 5]"
                            :key="star"
                            type="button"
                            class="text-3xl transition"
                            :class="star <= form.rating ? 'text-amber-400' : 'text-slate-200'"
                            @click="form.rating = star"
                        >★</button>
                    </div>
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Comments (optional)</label>
                    <textarea
                        v-model="form.comment"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                        placeholder="Tell us about your experience..."
                    ></textarea>
                </div>

                <!-- Tip -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Add a tip (optional)</label>
                    <div class="flex gap-2">
                        <button
                            v-for="pct in ['15%', '20%', '25%']"
                            :key="pct"
                            type="button"
                            class="rounded-lg border px-3 py-1.5 text-sm font-medium transition"
                            :class="form.tip_amount === pct ? 'border-slate-800 bg-slate-800 text-white' : 'border-slate-200 text-slate-600 hover:border-slate-400'"
                            @click="form.tip_amount = form.tip_amount === pct ? '' : pct"
                        >{{ pct }}</button>
                        <input
                            v-model="form.tip_amount"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="Custom $"
                            class="w-28 rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                >
                    Submit review
                </button>
            </form>
        </div>
    </div>
</template>
