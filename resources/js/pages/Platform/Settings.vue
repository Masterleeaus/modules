<script setup lang="ts">
import PlatformLayout from '@/layouts/PlatformLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type PlatformSettings = {
    app_name: string;
    logo_url: string | null;
    favicon_url: string | null;
    primary_color: string;
    support_email: string | null;
    footer_text: string | null;
};

const props = defineProps<{
    settings: PlatformSettings;
}>();

const page = usePage();
const flashSuccess = computed(() => (page.props.flash as { success?: string } | undefined)?.success);

const logoPreview = ref<string | null>(props.settings.logo_url);
const faviconPreview = ref<string | null>(props.settings.favicon_url);

const form = useForm({
    app_name: props.settings.app_name ?? 'FieldOps Hub',
    primary_color: props.settings.primary_color ?? '#2563eb',
    support_email: props.settings.support_email ?? '',
    footer_text: props.settings.footer_text ?? '',
    logo: null as File | null,
    favicon: null as File | null,
    remove_logo: false,
    remove_favicon: false,
});

function previewFile(event: Event, target: 'logo' | 'favicon'): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    if (target === 'logo') {
        form.logo = file;
        form.remove_logo = false;
        logoPreview.value = file ? URL.createObjectURL(file) : props.settings.logo_url;
        return;
    }

    form.favicon = file;
    form.remove_favicon = false;
    faviconPreview.value = file ? URL.createObjectURL(file) : props.settings.favicon_url;
}

function removeLogo(): void {
    form.logo = null;
    form.remove_logo = true;
    logoPreview.value = null;
}

function removeFavicon(): void {
    form.favicon = null;
    form.remove_favicon = true;
    faviconPreview.value = null;
}

function submit(): void {
    form.post('/platform/settings', {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <PlatformLayout title="Platform Settings">
        <Head title="Platform Settings" />

        <div class="mx-auto max-w-5xl space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Global SaaS branding</h2>
                <p class="mt-1 text-sm text-slate-500">Control the platform name, logo, favicon, support contact, and primary theme color.</p>

                <div v-if="flashSuccess" class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ flashSuccess }}
                </div>
            </div>

            <form class="grid grid-cols-1 gap-6 lg:grid-cols-3" @submit.prevent="submit">
                <div class="space-y-6 lg:col-span-2">
                    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-slate-900">Brand identity</h3>

                        <div class="mt-5 grid gap-5">
                            <label class="block text-sm">
                                <span class="font-medium text-slate-700">App name</span>
                                <input v-model="form.app_name" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" required />
                                <span v-if="form.errors.app_name" class="mt-1 block text-xs text-rose-600">{{ form.errors.app_name }}</span>
                            </label>

                            <label class="block text-sm">
                                <span class="font-medium text-slate-700">Primary color</span>
                                <div class="mt-1 flex gap-3">
                                    <input v-model="form.primary_color" type="color" class="h-10 w-14 rounded-md border border-slate-300 p-1" />
                                    <input v-model="form.primary_color" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="#2563eb" />
                                </div>
                                <span v-if="form.errors.primary_color" class="mt-1 block text-xs text-rose-600">{{ form.errors.primary_color }}</span>
                            </label>

                            <label class="block text-sm">
                                <span class="font-medium text-slate-700">Support email</span>
                                <input v-model="form.support_email" type="email" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" placeholder="support@example.com" />
                                <span v-if="form.errors.support_email" class="mt-1 block text-xs text-rose-600">{{ form.errors.support_email }}</span>
                            </label>

                            <label class="block text-sm">
                                <span class="font-medium text-slate-700">Footer text</span>
                                <input v-model="form.footer_text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" placeholder="© TitanZero" />
                                <span v-if="form.errors.footer_text" class="mt-1 block text-xs text-rose-600">{{ form.errors.footer_text }}</span>
                            </label>
                        </div>
                    </section>

                    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-slate-900">Assets</h3>

                        <div class="mt-5 grid gap-6 sm:grid-cols-2">
                            <div class="rounded-lg border border-slate-200 p-4">
                                <div class="flex h-24 items-center justify-center rounded-md bg-slate-50">
                                    <img v-if="logoPreview" :src="logoPreview" alt="Logo preview" class="max-h-16 max-w-full object-contain" />
                                    <span v-else class="text-sm text-slate-400">No logo</span>
                                </div>
                                <label class="mt-4 block text-sm font-medium text-slate-700">Logo</label>
                                <input type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="previewFile($event, 'logo')" />
                                <button type="button" class="mt-3 text-sm font-medium text-rose-600 hover:text-rose-700" @click="removeLogo">Remove logo</button>
                                <span v-if="form.errors.logo" class="mt-1 block text-xs text-rose-600">{{ form.errors.logo }}</span>
                            </div>

                            <div class="rounded-lg border border-slate-200 p-4">
                                <div class="flex h-24 items-center justify-center rounded-md bg-slate-50">
                                    <img v-if="faviconPreview" :src="faviconPreview" alt="Favicon preview" class="h-12 w-12 object-contain" />
                                    <span v-else class="text-sm text-slate-400">No favicon</span>
                                </div>
                                <label class="mt-4 block text-sm font-medium text-slate-700">Favicon</label>
                                <input type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="previewFile($event, 'favicon')" />
                                <button type="button" class="mt-3 text-sm font-medium text-rose-600 hover:text-rose-700" @click="removeFavicon">Remove favicon</button>
                                <span v-if="form.errors.favicon" class="mt-1 block text-xs text-rose-600">{{ form.errors.favicon }}</span>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-slate-900">Preview</h3>
                        <div class="mt-5 rounded-lg border border-slate-200 p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg text-white" :style="{ backgroundColor: form.primary_color }">
                                    <img v-if="logoPreview" :src="logoPreview" alt="Logo preview" class="h-10 w-10 object-contain" />
                                    <span v-else class="font-bold">{{ form.app_name.slice(0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ form.app_name }}</p>
                                    <p class="text-xs text-slate-500">{{ form.support_email || 'No support email set' }}</p>
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-slate-500">{{ form.footer_text || 'Footer text preview' }}</p>
                        </div>
                    </section>

                    <button type="submit" class="w-full rounded-lg bg-slate-950 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800" :disabled="form.processing">
                        {{ form.processing ? 'Saving…' : 'Save platform settings' }}
                    </button>
                </aside>
            </form>
        </div>
    </PlatformLayout>
</template>
