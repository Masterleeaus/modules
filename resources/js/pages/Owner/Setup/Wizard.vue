<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import type { JobType, UserRef } from '@/types';

interface TemplateEntry {
    subject: string | null;
    body: string;
    active: boolean;
}

interface Branding {
    brand_color: string | null;
    customer_facing_name: string | null;
    logo_path: string | null;
}

interface StepConfig {
    label: string;
    required: boolean;
}

const props = defineProps<{
    company: {
        name: string | null;
        email: string | null;
        phone: string | null;
        address: string | null;
        city: string | null;
        state: string | null;
        zip: string | null;
    };
    job_types: JobType[];
    technicians: UserRef[];
    templates: Record<string, TemplateEntry>;
    branding: Branding;
    setup_completed_steps: string[];
    steps: Record<string, StepConfig>;
    template_events: Record<string, string>;
    template_variables: Record<string, string>;
}>();

// ── Wizard step state ─────────────────────────────────────────────────────────

const STEP_KEYS = ['company', 'job_types', 'technicians', 'templates', 'branding', 'payment'] as const;
type StepKey = typeof STEP_KEYS[number];

// Find the first incomplete step to resume from
function firstIncompleteStep(): number {
    for (let i = 0; i < STEP_KEYS.length; i++) {
        if (!props.setup_completed_steps.includes(STEP_KEYS[i])) {
            return i + 1;
        }
    }
    return STEP_KEYS.length + 1; // all done
}

const step = ref<number>(firstIncompleteStep());

const stepLabels = [
    'Company Details',
    'Service Types',
    'Team Members',
    'Notifications',
    'Branding',
    'Payment',
];

function isStepCompleted(idx: number): boolean {
    return props.setup_completed_steps.includes(STEP_KEYS[idx - 1]);
}

const canFinish = computed(() => {
    const required = ['company', 'job_types', 'technicians'];
    return required.every(s => props.setup_completed_steps.includes(s));
});

// ── Step 1: Company form ──────────────────────────────────────────────────────

const companyForm = useForm({
    name:    props.company.name ?? '',
    email:   props.company.email ?? '',
    phone:   props.company.phone ?? '',
    address: props.company.address ?? '',
    city:    props.company.city ?? '',
    state:   props.company.state ?? '',
    zip:     props.company.zip ?? '',
});

function saveCompany() {
    companyForm.post('/owner/setup/company', {
        preserveScroll: true,
        onSuccess: () => { step.value = 2; },
    });
}

// ── Step 2: Job types ─────────────────────────────────────────────────────────

const jobTypeForm = useForm({
    name:  '',
    color: '#3b82f6',
});

const PRESET_COLORS = [
    '#3b82f6', '#10b981', '#f59e0b', '#6366f1',
    '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6',
];

function addJobType() {
    jobTypeForm.post('/owner/setup/job-types', {
        preserveScroll: true,
        onSuccess: () => {
            jobTypeForm.reset();
            jobTypeForm.color = '#3b82f6';
        },
    });
}

function removeJobType(id: number) {
    router.delete(`/owner/setup/job-types/${id}`, { preserveScroll: true });
}

// ── Step 3: Technicians ───────────────────────────────────────────────────────

const techForm = useForm({
    name:     '',
    email:    '',
    password: '',
});

function addTechnician() {
    techForm.post('/owner/setup/technicians', {
        preserveScroll: true,
        onSuccess: () => techForm.reset(),
    });
}

// ── Step 4: Notification templates ───────────────────────────────────────────

interface TemplateFormEntry {
    event: string;
    channel: string;
    subject: string;
    body: string;
    is_active: boolean;
}

function defaultTemplates(): TemplateFormEntry[] {
    const defaults: Record<string, Record<string, string>> = {
        job_scheduled: {
            email: 'Hi {{customer_name}}, your {{job_title}} is scheduled for {{job_date}}. Your technician will be {{technician_name}}. — {{company_name}}',
            sms:   'Hi {{customer_name}}, your {{job_title}} is confirmed for {{job_date}}. Tech: {{technician_name}}. — {{company_name}}',
        },
        job_reminder: {
            email: 'Reminder: Your {{job_title}} is tomorrow, {{job_date}}. Technician: {{technician_name}}. — {{company_name}}',
            sms:   'Reminder: {{job_title}} tomorrow at {{job_date}}. Tech: {{technician_name}}. — {{company_name}}',
        },
        en_route: {
            email: '{{technician_name}} is on the way for your {{job_title}}! — {{company_name}}',
            sms:   '{{technician_name}} is en route for your {{job_title}}! — {{company_name}}',
        },
        job_completed: {
            email: 'Your {{job_title}} is complete. Thank you for choosing {{company_name}}!',
            sms:   '{{job_title}} complete. Thanks for choosing {{company_name}}!',
        },
    };

    const result: TemplateFormEntry[] = [];
    for (const event of Object.keys(defaults)) {
        for (const channel of ['email', 'sms']) {
            const key = `${event}.${channel}`;
            const existing = props.templates[key];
            result.push({
                event,
                channel,
                subject:   existing?.subject ?? (channel === 'email' ? `Job Update – {{job_title}}` : ''),
                body:      existing?.body ?? defaults[event][channel],
                is_active: existing?.active ?? true,
            });
        }
    }
    return result;
}

const templatesForm = useForm({
    templates: defaultTemplates(),
});

function saveTemplates() {
    templatesForm.post('/owner/setup/templates', {
        preserveScroll: true,
        onSuccess: () => { step.value = 5; },
    });
}

function skipTemplates() {
    router.post('/owner/setup/skip', { step: 'templates' }, {
        preserveScroll: true,
        onSuccess: () => { step.value = 5; },
    });
}

// ── Step 5: Branding ─────────────────────────────────────────────────────────

const brandingForm = useForm({
    brand_color:          props.branding.brand_color ?? '#3b82f6',
    customer_facing_name: props.branding.customer_facing_name ?? '',
});

function saveBranding() {
    brandingForm.post('/owner/setup/branding', {
        preserveScroll: true,
        onSuccess: () => { step.value = 6; },
    });
}

function skipBranding() {
    router.post('/owner/setup/skip', { step: 'branding' }, {
        preserveScroll: true,
        onSuccess: () => { step.value = 6; },
    });
}

// ── Step 6: Payment ───────────────────────────────────────────────────────────

const paymentProcessing = ref(false);

function skipPayment() {
    paymentProcessing.value = true;
    router.post('/owner/setup/skip', { step: 'payment' }, {
        preserveScroll: true,
        onFinish: () => { paymentProcessing.value = false; },
    });
}

function markPaymentDone() {
    paymentProcessing.value = true;
    router.post('/owner/setup/payment', {}, {
        preserveScroll: true,
        onFinish: () => { paymentProcessing.value = false; },
    });
}

// ── Finish ────────────────────────────────────────────────────────────────────

const finishing = ref(false);

function finish() {
    finishing.value = true;
    router.post('/owner/setup/complete', {}, {
        onFinish: () => { finishing.value = false; },
    });
}
</script>

<template>
    <Head title="Setup — FieldOps Hub" />

    <div class="min-h-screen bg-slate-50 flex flex-col items-center justify-start py-12 px-4">
        <!-- Header -->
        <div class="w-full max-w-2xl mb-8 text-center">
            <h1 class="text-2xl font-bold text-slate-900">Welcome to FieldOps Hub</h1>
            <p class="mt-2 text-slate-500 text-sm">Let's get your account set up in a few quick steps.</p>
        </div>

        <!-- Step progress -->
        <div class="w-full max-w-2xl mb-8">
            <ol class="flex items-center gap-0">
                <li
                    v-for="(label, idx) in stepLabels"
                    :key="idx"
                    class="flex flex-1 items-center"
                >
                    <button
                        type="button"
                        class="flex items-center gap-2 focus:outline-none"
                        @click="isStepCompleted(idx + 1) ? (step = idx + 1) : undefined"
                        :title="isStepCompleted(idx + 1) ? `Revisit: ${label}` : label"
                    >
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold border-2 transition-colors"
                            :class="isStepCompleted(idx + 1)
                                ? 'bg-green-600 border-green-600 text-white cursor-pointer'
                                : step === idx + 1
                                    ? 'bg-slate-900 border-slate-900 text-white'
                                    : 'border-slate-300 text-slate-400'"
                        >
                            <svg v-if="isStepCompleted(idx + 1)" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span v-else>{{ idx + 1 }}</span>
                        </div>
                        <span
                            class="hidden sm:inline text-xs font-medium"
                            :class="step === idx + 1 ? 'text-slate-900' : (isStepCompleted(idx + 1) ? 'text-green-700' : 'text-slate-400')"
                        >{{ label }}</span>
                    </button>
                    <div v-if="idx < stepLabels.length - 1" class="flex-1 h-0.5 mx-2" :class="isStepCompleted(idx + 1) ? 'bg-green-500' : 'bg-slate-200'" />
                </li>
            </ol>
        </div>

        <!-- Card -->
        <div class="w-full max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">

            <!-- ── Step 1: Company Info ──────────────────────────────────────── -->
            <div v-if="step === 1">
                <h2 class="text-lg font-semibold text-slate-800 mb-1">Company Information</h2>
                <p class="text-sm text-slate-500 mb-6">This appears on invoices and customer communications.</p>

                <form @submit.prevent="saveCompany" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Company name <span class="text-rose-500">*</span></label>
                        <input
                            v-model="companyForm.name"
                            type="text"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                            placeholder="Acme Services"
                            required
                        />
                        <p v-if="companyForm.errors.name" class="mt-1 text-xs text-rose-600">{{ companyForm.errors.name }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input v-model="companyForm.email" type="email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="info@company.com" />
                            <p v-if="companyForm.errors.email" class="mt-1 text-xs text-rose-600">{{ companyForm.errors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                            <input v-model="companyForm.phone" type="tel" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="(555) 000-1234" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Street address</label>
                        <input v-model="companyForm.address" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="123 Main St" />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">City</label>
                            <input v-model="companyForm.city" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="Springfield" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">State</label>
                            <input v-model="companyForm.state" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="IL" maxlength="2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">ZIP</label>
                            <input v-model="companyForm.zip" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="62701" />
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="companyForm.processing"
                            class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 disabled:opacity-50 transition-colors"
                        >
                            Save &amp; Continue →
                        </button>
                    </div>
                </form>
            </div>

            <!-- ── Step 2: Job Types ─────────────────────────────────────────── -->
            <div v-if="step === 2">
                <h2 class="text-lg font-semibold text-slate-800 mb-1">Service / Job Types</h2>
                <p class="text-sm text-slate-500 mb-6">Add the types of work your team performs (e.g. Regular Clean, Deep Clean, End of Lease).</p>

                <!-- Existing job types -->
                <ul v-if="job_types.length > 0" class="mb-5 space-y-2">
                    <li
                        v-for="jt in job_types"
                        :key="jt.id"
                        class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2.5"
                    >
                        <div class="flex items-center gap-2.5">
                            <span class="h-3 w-3 rounded-full" :style="jt.color ? { background: jt.color } : undefined" />
                            <span class="text-sm font-medium text-slate-800">{{ jt.name }}</span>
                        </div>
                        <button
                            type="button"
                            class="text-xs text-slate-400 hover:text-rose-600 transition-colors"
                            @click="removeJobType(jt.id)"
                        >Remove</button>
                    </li>
                </ul>
                <p v-else class="mb-5 rounded-lg border border-dashed border-slate-200 px-4 py-6 text-center text-sm text-slate-400">
                    No job types added yet. Add at least one below.
                </p>

                <!-- Add job type form -->
                <form @submit.prevent="addJobType" class="flex items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                        <input
                            v-model="jobTypeForm.name"
                            type="text"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                            placeholder="Regular Clean"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Color</label>
                        <div class="flex items-center gap-1.5">
                            <span
                                v-for="c in PRESET_COLORS"
                                :key="c"
                                class="h-6 w-6 rounded-full cursor-pointer border-2 transition-all"
                                :class="jobTypeForm.color === c ? 'border-slate-900 scale-110' : 'border-transparent'"
                                :style="{ background: c }"
                                @click="jobTypeForm.color = c"
                            />
                        </div>
                    </div>
                    <button
                        type="submit"
                        :disabled="jobTypeForm.processing"
                        class="shrink-0 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 transition-colors"
                    >
                        + Add
                    </button>
                </form>
                <p v-if="jobTypeForm.errors.name" class="mt-1 text-xs text-rose-600">{{ jobTypeForm.errors.name }}</p>

                <div class="flex justify-between pt-6">
                    <button type="button" class="text-sm text-slate-500 hover:text-slate-700" @click="step = 1">← Back</button>
                    <button
                        type="button"
                        :disabled="job_types.length === 0"
                        class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 disabled:opacity-50 transition-colors"
                        @click="step = 3"
                    >
                        Continue →
                    </button>
                </div>
            </div>

            <!-- ── Step 3: Technicians ───────────────────────────────────────── -->
            <div v-if="step === 3">
                <h2 class="text-lg font-semibold text-slate-800 mb-1">Add Technicians</h2>
                <p class="text-sm text-slate-500 mb-6">Create accounts for your field technicians. They'll use the mobile app to manage jobs.</p>

                <!-- Existing technicians -->
                <ul v-if="technicians.length > 0" class="mb-5 space-y-2">
                    <li
                        v-for="tech in technicians"
                        :key="tech.id"
                        class="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5"
                    >
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">
                            {{ tech.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ tech.name }}</p>
                            <p class="text-xs text-slate-400">{{ tech.email }}</p>
                        </div>
                    </li>
                </ul>
                <p v-else class="mb-5 rounded-lg border border-dashed border-slate-200 px-4 py-6 text-center text-sm text-slate-400">
                    No technicians added yet. Add at least one below.
                </p>

                <!-- Add technician form -->
                <form @submit.prevent="addTechnician" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Full name</label>
                            <input
                                v-model="techForm.name"
                                type="text"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                                placeholder="Jane Doe"
                                required
                            />
                            <p v-if="techForm.errors.name" class="mt-0.5 text-xs text-rose-600">{{ techForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                            <input
                                v-model="techForm.email"
                                type="email"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                                placeholder="jane@company.com"
                                required
                            />
                            <p v-if="techForm.errors.email" class="mt-0.5 text-xs text-rose-600">{{ techForm.errors.email }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Password</label>
                        <input
                            v-model="techForm.password"
                            type="password"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                            placeholder="Minimum 8 characters"
                            required
                        />
                        <p v-if="techForm.errors.password" class="mt-0.5 text-xs text-rose-600">{{ techForm.errors.password }}</p>
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="techForm.processing"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 transition-colors"
                        >
                            + Add Technician
                        </button>
                    </div>
                </form>

                <div class="flex justify-between pt-6">
                    <button type="button" class="text-sm text-slate-500 hover:text-slate-700" @click="step = 2">← Back</button>
                    <button
                        type="button"
                        :disabled="technicians.length === 0"
                        class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 disabled:opacity-50 transition-colors"
                        @click="step = 4"
                    >
                        Continue →
                    </button>
                </div>
            </div>

            <!-- ── Step 4: Notification Templates ────────────────────────────── -->
            <div v-if="step === 4">
                <div class="flex items-start justify-between mb-1">
                    <h2 class="text-lg font-semibold text-slate-800">Notification Templates</h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">Optional</span>
                </div>
                <p class="text-sm text-slate-500 mb-6">Customize the SMS and email messages sent to your customers. Use <code class="text-xs bg-slate-100 px-1 rounded">{{ '{{variable}}' }}</code> placeholders.</p>

                <!-- Available variables -->
                <div class="mb-5 rounded-lg bg-slate-50 border border-slate-200 px-4 py-3">
                    <p class="text-xs font-semibold text-slate-600 mb-2">Available variables:</p>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="(desc, varName) in template_variables" :key="varName" class="text-xs bg-white border border-slate-200 rounded px-2 py-0.5" :title="desc">{{ varName }}</span>
                    </div>
                </div>

                <form @submit.prevent="saveTemplates" class="space-y-6">
                    <div v-for="(eventLabel, eventKey) in template_events" :key="eventKey" class="rounded-lg border border-slate-200 overflow-hidden">
                        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                            <p class="text-sm font-semibold text-slate-700">{{ eventLabel }}</p>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <div
                                v-for="(tpl, tplIdx) in templatesForm.templates.filter(t => t.event === eventKey)"
                                :key="`${eventKey}-${tpl.channel}`"
                                class="px-4 py-3 space-y-2"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ tpl.channel === 'email' ? '📧 Email' : '💬 SMS' }}</span>
                                    <label class="flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer">
                                        <input type="checkbox" v-model="tpl.is_active" class="rounded" />
                                        Active
                                    </label>
                                </div>
                                <div v-if="tpl.channel === 'email'">
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Subject</label>
                                    <input v-model="tpl.subject" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Message body</label>
                                    <textarea v-model="tpl.body" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500 resize-none" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-2">
                        <button type="button" class="text-sm text-slate-500 hover:text-slate-700" @click="step = 3">← Back</button>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="text-sm text-slate-500 hover:text-slate-700 underline"
                                @click="skipTemplates"
                            >
                                Skip for now
                            </button>
                            <button
                                type="submit"
                                :disabled="templatesForm.processing"
                                class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 disabled:opacity-50 transition-colors"
                            >
                                Save &amp; Continue →
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ── Step 5: Branding ──────────────────────────────────────────── -->
            <div v-if="step === 5">
                <div class="flex items-start justify-between mb-1">
                    <h2 class="text-lg font-semibold text-slate-800">Branding</h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">Optional</span>
                </div>
                <p class="text-sm text-slate-500 mb-6">Set your brand color and the name shown to customers on invoices and portals.</p>

                <form @submit.prevent="saveBranding" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Customer-facing name</label>
                        <input
                            v-model="brandingForm.customer_facing_name"
                            type="text"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                            placeholder="e.g. Acme Cleaning Co."
                        />
                        <p class="mt-1 text-xs text-slate-400">Leave blank to use your company name.</p>
                        <p v-if="brandingForm.errors.customer_facing_name" class="mt-1 text-xs text-rose-600">{{ brandingForm.errors.customer_facing_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Brand color</label>
                        <div class="flex items-center gap-3">
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="c in PRESET_COLORS"
                                    :key="c"
                                    class="h-8 w-8 rounded-full cursor-pointer border-2 transition-all"
                                    :class="brandingForm.brand_color === c ? 'border-slate-900 scale-110' : 'border-transparent hover:border-slate-300'"
                                    :style="{ background: c }"
                                    @click="brandingForm.brand_color = c"
                                />
                            </div>
                            <div class="flex items-center gap-2">
                                <input
                                    v-model="brandingForm.brand_color"
                                    type="color"
                                    class="h-8 w-10 rounded cursor-pointer border border-slate-300 p-0.5"
                                />
                                <span class="text-sm text-slate-500 font-mono">{{ brandingForm.brand_color }}</span>
                            </div>
                        </div>
                        <p v-if="brandingForm.errors.brand_color" class="mt-1 text-xs text-rose-600">{{ brandingForm.errors.brand_color }}</p>
                    </div>

                    <div class="flex justify-between pt-2">
                        <button type="button" class="text-sm text-slate-500 hover:text-slate-700" @click="step = 4">← Back</button>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="text-sm text-slate-500 hover:text-slate-700 underline"
                                @click="skipBranding"
                            >
                                Skip for now
                            </button>
                            <button
                                type="submit"
                                :disabled="brandingForm.processing"
                                class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 disabled:opacity-50 transition-colors"
                            >
                                Save &amp; Continue →
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ── Step 6: Payment Setup ──────────────────────────────────────── -->
            <div v-if="step === 6">
                <div class="flex items-start justify-between mb-1">
                    <h2 class="text-lg font-semibold text-slate-800">Payment Setup</h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">Optional</span>
                </div>
                <p class="text-sm text-slate-500 mb-6">Connect a Stripe account to accept online payments from customers via invoice links.</p>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-6 text-center space-y-4">
                    <div class="flex justify-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600">Connect your Stripe account to start accepting card payments. You can also do this later from <strong>Settings → Integrations</strong>.</p>
                    <a
                        href="/owner/settings/integrations"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                        @click.prevent="markPaymentDone"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Set Up Stripe Payments
                    </a>
                </div>

                <div class="flex justify-between pt-6">
                    <button type="button" class="text-sm text-slate-500 hover:text-slate-700" @click="step = 5">← Back</button>
                    <div class="flex gap-3 items-center">
                        <button
                            type="button"
                            class="text-sm text-slate-500 hover:text-slate-700 underline"
                            :disabled="paymentProcessing"
                            @click="skipPayment"
                        >
                            Skip for now
                        </button>
                        <button
                            v-if="canFinish"
                            type="button"
                            :disabled="finishing"
                            class="rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-600 disabled:opacity-50 transition-colors"
                            @click="finish"
                        >
                            {{ finishing ? 'Finishing…' : 'Finish Setup ✓' }}
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Finish button when all required steps are done but not on last step -->
        <div v-if="canFinish && step < 6" class="w-full max-w-2xl mt-4 flex justify-end">
            <button
                type="button"
                :disabled="finishing"
                class="rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-600 disabled:opacity-50 transition-colors"
                @click="finish"
            >
                {{ finishing ? 'Finishing…' : 'Finish Setup ✓' }}
            </button>
        </div>

        <p class="mt-6 text-xs text-slate-400">
            You can update all of this later in <strong>Settings</strong>.
        </p>
    </div>
</template>
