<script setup lang="ts">
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    jobId: number;
}>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
const isDrawing = ref(false);
const saved = ref(false);
const saving = ref(false);
const errorMsg = ref('');

function getCtx(): CanvasRenderingContext2D | null {
    return canvasRef.value?.getContext('2d') ?? null;
}

function startDraw(e: MouseEvent | TouchEvent) {
    isDrawing.value = true;
    const ctx = getCtx();
    if (!ctx) return;
    const pt = 'touches' in e ? e.touches[0] : e;
    const rect = (e.target as HTMLCanvasElement).getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo(pt.clientX - rect.left, pt.clientY - rect.top);
}

function draw(e: MouseEvent | TouchEvent) {
    if (!isDrawing.value) return;
    e.preventDefault();
    const ctx = getCtx();
    if (!ctx) return;
    const pt = 'touches' in e ? e.touches[0] : e;
    const rect = (e.target as HTMLCanvasElement).getBoundingClientRect();
    ctx.lineTo(pt.clientX - rect.left, pt.clientY - rect.top);
    ctx.strokeStyle = '#0f172a';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.stroke();
}

function stopDraw() { isDrawing.value = false; }

function clearCanvas() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    getCtx()?.clearRect(0, 0, canvas.width, canvas.height);
    saved.value = false;
    errorMsg.value = '';
}

async function saveSignature() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    saving.value = true;
    errorMsg.value = '';
    const dataUri = canvas.toDataURL('image/png');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;

    try {
        const res = await fetch(`/api/technician/jobs/${props.jobId}/signature`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {}),
            },
            body: JSON.stringify({ signature: dataUri }),
        });

        if (res.ok) {
            saved.value = true;
        } else {
            const json = await res.json().catch(() => ({}));
            errorMsg.value = (json as { message?: string }).message ?? 'Failed to save signature.';
        }
    } catch {
        errorMsg.value = 'Network error. Please try again.';
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <TechnicianLayout title="Customer Signature">
        <Head title="Customer Signature" />

        <div class="p-4">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="mb-1 text-sm font-medium text-slate-700">Customer Signature</p>
                <p class="mb-4 text-xs text-slate-500">
                    Please hand the device to the customer to sign below.
                </p>

                <div v-if="saved" class="rounded-lg bg-green-50 p-6 text-center">
                    <svg class="mx-auto mb-3 h-10 w-10 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm font-semibold text-green-700">Signature saved successfully</p>
                    <button
                        type="button"
                        class="mt-3 text-xs text-green-600 underline"
                        @click="saved = false; clearCanvas()"
                    >
                        Re-capture signature
                    </button>
                </div>

                <template v-else>
                    <!-- Signature canvas -->
                    <div class="rounded-lg border-2 border-slate-300 bg-slate-50">
                        <canvas
                            ref="canvasRef"
                            :width="360"
                            :height="200"
                            class="w-full touch-none rounded-lg"
                            style="cursor: crosshair; display: block"
                            @mousedown="startDraw"
                            @mousemove="draw"
                            @mouseup="stopDraw"
                            @mouseleave="stopDraw"
                            @touchstart.prevent="startDraw"
                            @touchmove.prevent="draw"
                            @touchend="stopDraw"
                        />
                    </div>

                    <p v-if="errorMsg" class="mt-2 text-xs text-red-600">{{ errorMsg }}</p>

                    <!-- Signature line label -->
                    <p class="mt-1 text-center text-xs text-slate-400">✕ Sign above</p>

                    <div class="mt-4 flex gap-2">
                        <button
                            type="button"
                            class="flex-1 rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white active:bg-slate-700 disabled:opacity-50"
                            :disabled="saving"
                            @click="saveSignature"
                        >
                            {{ saving ? 'Saving…' : 'Save Signature' }}
                        </button>
                        <button
                            type="button"
                            class="rounded-xl bg-slate-100 px-5 py-3 text-sm font-medium text-slate-600"
                            @click="clearCanvas"
                        >
                            Clear
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </TechnicianLayout>
</template>
