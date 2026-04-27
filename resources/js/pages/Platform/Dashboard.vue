<script setup lang="ts">
import PlatformLayout from '@/layouts/PlatformLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type Subscription = {
    id: number;
    plan: string;
    status: string;
    billing_interval: string;
    trial_ends_at: string | null;
    current_period_end: string | null;
};

type Organization = {
    id: number;
    name: string;
    slug: string;
    plan: string;
    trial_ends_at: string | null;
    stripe_customer_id: string | null;
    users_count: number;
    customers_count: number;
    jobs_count: number;
    invoices_count: number;
    subscription: Subscription | null;
};

const props = defineProps<{
    stats: {
        organizations: number;
        users: number;
        active_subscriptions: number;
        expired_trials: number;
    };
    organizations: Organization[];
    plans: string[];
    statuses: string[];
}>();

const selectedId = ref<number | null>(props.organizations[0]?.id ?? null);
const selectedOrganization = computed(() => props.organizations.find((organization) => organization.id === selectedId.value) ?? null);

const organizationForm = useForm({
    name: selectedOrganization.value?.name ?? '',
    slug: selectedOrganization.value?.slug ?? '',
    plan: selectedOrganization.value?.plan ?? 'growth',
    trial_ends_at: selectedOrganization.value?.trial_ends_at?.slice(0, 10) ?? '',
});

const subscriptionForm = useForm({
    plan: selectedOrganization.value?.subscription?.plan ?? selectedOrganization.value?.plan ?? 'growth',
    status: selectedOrganization.value?.subscription?.status ?? 'trialing',
    billing_interval: selectedOrganization.value?.subscription?.billing_interval ?? 'monthly',
    trial_ends_at: selectedOrganization.value?.subscription?.trial_ends_at?.slice(0, 10) ?? '',
    current_period_end: selectedOrganization.value?.subscription?.current_period_end?.slice(0, 10) ?? '',
});

function selectOrganization(organization: Organization): void {
    selectedId.value = organization.id;
    organizationForm.name = organization.name;
    organizationForm.slug = organization.slug;
    organizationForm.plan = organization.plan;
    organizationForm.trial_ends_at = organization.trial_ends_at?.slice(0, 10) ?? '';
    subscriptionForm.plan = organization.subscription?.plan ?? organization.plan ?? 'growth';
    subscriptionForm.status = organization.subscription?.status ?? 'trialing';
    subscriptionForm.billing_interval = organization.subscription?.billing_interval ?? 'monthly';
    subscriptionForm.trial_ends_at = organization.subscription?.trial_ends_at?.slice(0, 10) ?? '';
    subscriptionForm.current_period_end = organization.subscription?.current_period_end?.slice(0, 10) ?? '';
}

function updateOrganization(): void {
    if (!selectedOrganization.value) return;
    organizationForm.patch(`/platform/organizations/${selectedOrganization.value.id}`, { preserveScroll: true });
}

function updateSubscription(): void {
    if (!selectedOrganization.value) return;
    subscriptionForm.patch(`/platform/organizations/${selectedOrganization.value.id}/subscription`, { preserveScroll: true });
}

function extendTrial(organization: Organization): void {
    router.post(`/platform/organizations/${organization.id}/extend-trial`, {}, { preserveScroll: true });
}

function activate(organization: Organization): void {
    router.post(`/platform/organizations/${organization.id}/activate`, {}, { preserveScroll: true });
}

function formatDate(value: string | null): string {
    if (!value) return '—';
    return new Intl.DateTimeFormat('en-US', { dateStyle: 'medium' }).format(new Date(value));
}
</script>

<template>
    <PlatformLayout title="SaaS Admin Panel">
        <Head title="SaaS Admin Panel" />

        <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Organizations</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.organizations }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Users</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.users }}</p>
            </div>
            <div class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Active subscriptions</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600">{{ stats.active_subscriptions }}</p>
            </div>
            <div class="rounded-xl border border-rose-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Expired trials</p>
                <p class="mt-2 text-3xl font-bold text-rose-600">{{ stats.expired_trials }}</p>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-5">
            <div id="organizations" class="xl:col-span-3 rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="font-semibold text-slate-900">Organizations</h2>
                    <p class="mt-1 text-sm text-slate-500">Select a tenant to adjust SaaS plan, trial, and subscription status.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Tenant</th>
                                <th class="px-5 py-3">Plan</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Trial ends</th>
                                <th class="px-5 py-3">Records</th>
                                <th class="px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="organization in organizations"
                                :key="organization.id"
                                class="cursor-pointer hover:bg-slate-50"
                                :class="selectedId === organization.id ? 'bg-indigo-50' : ''"
                                @click="selectOrganization(organization)"
                            >
                                <td class="px-5 py-3">
                                    <div class="font-medium text-slate-900">{{ organization.name }}</div>
                                    <div class="text-xs text-slate-500">/{{ organization.slug }}</div>
                                </td>
                                <td class="px-5 py-3 capitalize">{{ organization.plan }}</td>
                                <td class="px-5 py-3 capitalize">{{ organization.subscription?.status ?? 'none' }}</td>
                                <td class="px-5 py-3">{{ formatDate(organization.subscription?.trial_ends_at ?? organization.trial_ends_at) }}</td>
                                <td class="px-5 py-3 text-xs text-slate-500">
                                    {{ organization.users_count }} users · {{ organization.jobs_count }} jobs · {{ organization.invoices_count }} invoices
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <button class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200" @click.stop="extendTrial(organization)">+30 trial</button>
                                        <button class="rounded-md bg-emerald-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-emerald-700" @click.stop="activate(organization)">Activate</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="subscriptions" class="xl:col-span-2 space-y-6">
                <form v-if="selectedOrganization" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm" @submit.prevent="updateOrganization">
                    <h2 class="font-semibold text-slate-900">Organization settings</h2>
                    <div class="mt-4 space-y-4">
                        <label class="block text-sm">
                            <span class="font-medium text-slate-700">Name</span>
                            <input v-model="organizationForm.name" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" />
                        </label>
                        <label class="block text-sm">
                            <span class="font-medium text-slate-700">Slug</span>
                            <input v-model="organizationForm.slug" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" />
                        </label>
                        <label class="block text-sm">
                            <span class="font-medium text-slate-700">Plan</span>
                            <select v-model="organizationForm.plan" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 capitalize">
                                <option v-for="plan in plans" :key="plan" :value="plan">{{ plan }}</option>
                            </select>
                        </label>
                        <label class="block text-sm">
                            <span class="font-medium text-slate-700">Organization trial date</span>
                            <input v-model="organizationForm.trial_ends_at" type="date" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" />
                        </label>
                    </div>
                    <button type="submit" class="mt-5 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" :disabled="organizationForm.processing">Save organization</button>
                </form>

                <form v-if="selectedOrganization" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm" @submit.prevent="updateSubscription">
                    <h2 class="font-semibold text-slate-900">Subscription override</h2>
                    <div class="mt-4 space-y-4">
                        <label class="block text-sm">
                            <span class="font-medium text-slate-700">Plan</span>
                            <select v-model="subscriptionForm.plan" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 capitalize">
                                <option v-for="plan in plans" :key="plan" :value="plan">{{ plan }}</option>
                            </select>
                        </label>
                        <label class="block text-sm">
                            <span class="font-medium text-slate-700">Status</span>
                            <select v-model="subscriptionForm.status" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 capitalize">
                                <option v-for="status in statuses" :key="status" :value="status">{{ status.replace('_', ' ') }}</option>
                            </select>
                        </label>
                        <label class="block text-sm">
                            <span class="font-medium text-slate-700">Billing interval</span>
                            <select v-model="subscriptionForm.billing_interval" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 capitalize">
                                <option value="monthly">Monthly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </label>
                        <label class="block text-sm">
                            <span class="font-medium text-slate-700">Trial ends</span>
                            <input v-model="subscriptionForm.trial_ends_at" type="date" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" />
                        </label>
                        <label class="block text-sm">
                            <span class="font-medium text-slate-700">Current period ends</span>
                            <input v-model="subscriptionForm.current_period_end" type="date" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" />
                        </label>
                    </div>
                    <button type="submit" class="mt-5 rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800" :disabled="subscriptionForm.processing">Save subscription</button>
                </form>
            </div>
        </section>
    </PlatformLayout>
</template>
