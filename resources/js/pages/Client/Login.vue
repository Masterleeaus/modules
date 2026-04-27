<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ email: '' });

function submit() {
    form.post('/client/login');
}
</script>

<template>
    <Head title="Client Portal Login" />

    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <h1 class="mb-1 text-xl font-bold text-slate-800">Client Portal</h1>
            <p class="mb-6 text-sm text-slate-500">Enter your email to receive a secure login link.</p>

            <div v-if="$page.props.flash?.success" class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200">
                {{ $page.props.flash.success }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                        placeholder="you@example.com"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                >
                    Send login link
                </button>
            </form>
        </div>
    </div>
</template>
