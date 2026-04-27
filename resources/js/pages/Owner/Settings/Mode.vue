<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{ mode: 'solo' | 'team' }>();

const selected = ref<'solo' | 'team'>(props.mode);
const saving = ref(false);
const page = usePage();

function submit() {
    saving.value = true;
    router.post('/owner/settings/mode', { mode: selected.value }, {
        onFinish: () => { saving.value = false; },
    });
}
</script>

<template>
    <OwnerLayout title="Operation Mode">
        <Head title="Operation Mode" />

        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-800">Operation Mode</h2>
        </div>

        <div v-if="(page.props as any).flash?.success"
            class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ (page.props as any).flash.success }}
        </div>

        <div class="max-w-2xl space-y-4">
            <!-- Solo Mode Card -->
            <label
                class="flex cursor-pointer items-start gap-4 rounded-xl border-2 p-5 transition-colors"
                :class="selected === 'solo' ? 'border-violet-500 bg-violet-50' : 'border-slate-200 bg-white hover:border-slate-300'"
            >
                <input
                    v-model="selected"
                    type="radio"
                    value="solo"
                    class="mt-1 h-4 w-4 text-violet-600 focus:ring-violet-500"
                />
                <div class="flex-1">
                    <p class="font-semibold text-slate-800">Solo Mode</p>
                    <p class="mt-1 text-sm text-slate-500">
                        You work alone. Job creation auto-assigns to you. Team management,
                        dispatch board, and multi-technician features are hidden to keep things simple.
                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-slate-500">
                        <li class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-violet-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            Jobs auto-assigned to you
                        </li>
                        <li class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-violet-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            Simplified navigation (no dispatch, no team)
                        </li>
                        <li class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-violet-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            Max 1 technician seat
                        </li>
                    </ul>
                </div>
            </label>

            <!-- Team Mode Card -->
            <label
                class="flex cursor-pointer items-start gap-4 rounded-xl border-2 p-5 transition-colors"
                :class="selected === 'team' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-slate-300'"
            >
                <input
                    v-model="selected"
                    type="radio"
                    value="team"
                    class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500"
                />
                <div class="flex-1">
                    <p class="font-semibold text-slate-800">Team Mode</p>
                    <p class="mt-1 text-sm text-slate-500">
                        You manage a crew. Full dispatch board, technician assignment, team
                        management, and reporting are unlocked.
                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-slate-500">
                        <li class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-blue-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            Dispatch board with live technician map
                        </li>
                        <li class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-blue-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            Team member management &amp; invites
                        </li>
                        <li class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-blue-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            Multiple technician seats (plan-limited)
                        </li>
                        <li class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-blue-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            Advanced reports (technician performance)
                        </li>
                    </ul>
                </div>
            </label>

            <div v-if="mode === 'solo' && selected === 'team'" class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                <strong>Switching to Team Mode</strong> will unlock team features. Your existing job data is preserved.
                You'll be able to invite your first technician from Team → Add Member.
            </div>

            <div class="flex justify-end pt-2">
                <button
                    type="button"
                    :disabled="saving || selected === mode"
                    class="px-5 py-2 bg-slate-800 text-white text-sm rounded hover:bg-slate-700 disabled:opacity-60"
                    @click="submit"
                >
                    {{ saving ? 'Saving…' : 'Save Mode' }}
                </button>
            </div>
        </div>
    </OwnerLayout>
</template>
