<script setup lang="ts">
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';
import { useImageCompression } from '@/composables/useImageCompression';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

const { compressImage } = useImageCompression();

// ── Types ─────────────────────────────────────────────────────────────────────
interface ChecklistItem {
    id: number;
    label: string;
    sort_order: number;
    is_required: boolean;
    completed_at: string | null;
}

interface Attachment {
    id: number;
    filename: string;
    url: string;
    tag: 'before' | 'after' | 'client_signature' | null;
    mime_type: string | null;
}

interface LineItem {
    id: number;
    item_id: number | null;
    name: string;
    unit_price: string;
    quantity: string;
    total: string;
    sort_order: number;
}

interface CatalogItem {
    id: number;
    name: string;
    unit_price: string;
    unit: string;
    sku: string | null;
}

interface Job {
    id: number;
    title: string;
    description: string | null;
    status: string;
    scheduled_at: string | null;
    started_at: string | null;
    arrived_at: string | null;
    completed_at: string | null;
    office_notes: string | null;
    technician_notes: string | null;
    customer_notes: string | null;
    customer: { id: number; first_name: string; last_name: string; phone?: string } | null;
    property: { id: number; address_line1: string; city: string; state: string; postal_code: string } | null;
    job_type: { id: number; name: string; color: string } | null;
    checklist_items: ChecklistItem[];
    attachments: Attachment[];
    line_items: LineItem[];
}

const props = defineProps<{
    job: Job;
    statuses: Record<string, string>;
}>();

// ── Tabs ──────────────────────────────────────────────────────────────────────
type Tab = 'checklist' | 'photos' | 'line_items' | 'notes' | 'signature';
const activeTab = ref<Tab>('checklist');

const tabs: { key: Tab; label: string }[] = [
    { key: 'checklist',  label: 'Checklist' },
    { key: 'photos',     label: 'Photos' },
    { key: 'line_items', label: 'Line Items' },
    { key: 'notes',      label: 'Notes' },
    { key: 'signature',  label: 'Signature' },
];

// ── Status ────────────────────────────────────────────────────────────────────
const STATUS_CLASSES: Record<string, string> = {
    scheduled:   'bg-blue-100 text-blue-700',
    en_route:    'bg-purple-100 text-purple-700',
    in_progress: 'bg-amber-100 text-amber-700',
    completed:   'bg-green-100 text-green-700',
    cancelled:   'bg-slate-100 text-slate-500',
    on_hold:     'bg-orange-100 text-orange-700',
};

const TECHNICIAN_ACTIONS: { key: string; label: string }[] = [
    { key: 'en_route',    label: 'On my Way' },
    { key: 'in_progress', label: 'Arrived' },
    { key: 'completed',   label: 'Complete' },
];

const currentStatus = ref(props.job.status);
const updatingStatus = ref(false);

function updateStatus(key: string) {
    updatingStatus.value = true;
    router.patch(`/api/technician/jobs/${props.job.id}/status`, { status: key }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => { currentStatus.value = key; },
        onFinish: () => { updatingStatus.value = false; },
    });
}

// ── Checklist ─────────────────────────────────────────────────────────────────
const checklistState = reactive(
    Object.fromEntries(
        (props.job.checklist_items ?? []).map((item) => [item.id, item.completed_at !== null]),
    ) as Record<number, boolean>,
);
const togglingItem = ref<number | null>(null);

function toggleChecklist(item: ChecklistItem) {
    if (togglingItem.value) return;
    togglingItem.value = item.id;
    const completed = !checklistState[item.id];
    router.patch(`/api/technician/jobs/${props.job.id}/checklist/${item.id}`, { completed }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => { checklistState[item.id] = completed; },
        onFinish: () => { togglingItem.value = null; },
    });
}

const checklistProgress = computed(() => {
    const total = props.job.checklist_items?.length ?? 0;
    if (total === 0) return null;
    const done = Object.values(checklistState).filter(Boolean).length;
    return { done, total, pct: Math.round((done / total) * 100) };
});

// ── Photos ────────────────────────────────────────────────────────────────────
const photos = reactive<Attachment[]>(
    (props.job.attachments ?? []).filter((a) => a.tag === 'before' || a.tag === 'after'),
);
const uploadingTag = ref<'before' | 'after' | null>(null);
const uploadProgress = ref(0);
const deletingPhotoId = ref<number | null>(null);

const beforePhotos = computed(() => photos.filter((p) => p.tag === 'before'));
const afterPhotos  = computed(() => photos.filter((p) => p.tag === 'after'));

async function handlePhotoCapture(e: Event, tag: 'before' | 'after') {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;
    input.value = '';

    uploadingTag.value = tag;
    uploadProgress.value = 0;

    const blob = await compressImage(file);
    const compressed = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' });

    const formData = new FormData();
    formData.append('photo', compressed);
    formData.append('tag', tag);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', `/api/technician/jobs/${props.job.id}/photos`);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');

    const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    if (csrfMeta) xhr.setRequestHeader('X-CSRF-TOKEN', csrfMeta.content);

    xhr.upload.onprogress = (ev) => {
        if (ev.lengthComputable) uploadProgress.value = Math.round((ev.loaded / ev.total) * 100);
    };
    xhr.onload = () => {
        uploadingTag.value = null;
        uploadProgress.value = 0;
        if (xhr.status === 201) {
            const resp = JSON.parse(xhr.responseText);
            photos.push(resp.data);
        }
    };
    xhr.onerror = () => { uploadingTag.value = null; uploadProgress.value = 0; };
    xhr.send(formData);
}

function deletePhoto(photo: Attachment) {
    deletingPhotoId.value = photo.id;
    router.delete(`/api/technician/jobs/${props.job.id}/photos/${photo.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            const idx = photos.findIndex((p) => p.id === photo.id);
            if (idx !== -1) photos.splice(idx, 1);
        },
        onFinish: () => { deletingPhotoId.value = null; },
    });
}

// ── Line items ────────────────────────────────────────────────────────────────
const lineItems = reactive<LineItem[]>([...(props.job.line_items ?? [])]);
const lineItemTotal = computed(() =>
    lineItems.reduce((sum, li) => sum + parseFloat(li.unit_price) * parseFloat(li.quantity), 0),
);
const showAddLineItem = ref(false);
const addForm = reactive({ name: '', unit_price: '', quantity: '1', item_id: null as number | null });
const addingLineItem = ref(false);
const catalogQuery = ref('');
const catalogResults = ref<CatalogItem[]>([]);
const catalogLoading = ref(false);
let catalogDebounce: ReturnType<typeof setTimeout> | null = null;

function searchCatalog() {
    if (catalogDebounce) clearTimeout(catalogDebounce);
    if (!catalogQuery.value.trim()) { catalogResults.value = []; return; }
    catalogDebounce = setTimeout(async () => {
        catalogLoading.value = true;
        try {
            const res = await fetch(`/api/technician/catalog?q=${encodeURIComponent(catalogQuery.value)}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const json = await res.json();
            catalogResults.value = json.data ?? [];
        } finally {
            catalogLoading.value = false;
        }
    }, 300);
}

function selectCatalogItem(item: CatalogItem) {
    addForm.item_id = item.id;
    addForm.name = item.name;
    addForm.unit_price = parseFloat(item.unit_price).toFixed(2);
    catalogQuery.value = item.name;
    catalogResults.value = [];
}

async function addLineItem() {
    addingLineItem.value = true;
    const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    try {
        const res = await fetch(`/api/technician/jobs/${props.job.id}/line-items`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {}),
            },
            body: JSON.stringify(addForm),
        });
        if (res.ok) {
            const json = await res.json();
            lineItems.push(json.data);
            Object.assign(addForm, { name: '', unit_price: '', quantity: '1', item_id: null });
            catalogQuery.value = '';
            showAddLineItem.value = false;
        }
    } finally {
        addingLineItem.value = false;
    }
}

async function deleteLineItem(li: LineItem) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    await fetch(`/api/technician/jobs/${props.job.id}/line-items/${li.id}`, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {}),
        },
    });
    const idx = lineItems.findIndex((l) => l.id === li.id);
    if (idx !== -1) lineItems.splice(idx, 1);
}

// ── Notes ─────────────────────────────────────────────────────────────────────
const notesForm = useForm({ technician_notes: props.job.technician_notes ?? '' });
const editingNotes = ref(false);

function saveNotes() {
    notesForm.patch(`/api/technician/jobs/${props.job.id}/notes`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => { editingNotes.value = false; },
    });
}

const customerNotesForm = useForm({ customer_notes: props.job.customer_notes ?? '' });
const editingCustomerNotes = ref(false);

function saveCustomerNotes() {
    customerNotesForm.patch(`/api/technician/jobs/${props.job.id}/customer-notes`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => { editingCustomerNotes.value = false; },
    });
}

// ── Signature ─────────────────────────────────────────────────────────────────
const signatureCanvas = ref<HTMLCanvasElement | null>(null);
const isDrawing = ref(false);
const hasSig = ref(
    props.job.attachments.some((a) => a.tag === 'client_signature'),
);
const savingSig = ref(false);
const sigSaved = ref(hasSig.value);

function getCtx(): CanvasRenderingContext2D | null {
    return signatureCanvas.value?.getContext('2d') ?? null;
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
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.stroke();
}

function stopDraw() { isDrawing.value = false; }

function clearSignature() {
    const canvas = signatureCanvas.value;
    if (!canvas) return;
    getCtx()?.clearRect(0, 0, canvas.width, canvas.height);
    sigSaved.value = false;
}

async function saveSignature() {
    const canvas = signatureCanvas.value;
    if (!canvas) return;
    savingSig.value = true;
    const dataUri = canvas.toDataURL('image/png');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    try {
        const res = await fetch(`/api/technician/jobs/${props.job.id}/signature`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {}),
            },
            body: JSON.stringify({ signature: dataUri }),
        });
        if (res.ok) { sigSaved.value = true; }
    } finally {
        savingSig.value = false;
    }
}

// ── Navigation ────────────────────────────────────────────────────────────────
function openDirections() {
    const addr = props.job.property;
    if (!addr) return;
    const query = encodeURIComponent(`${addr.address_line1}, ${addr.city}, ${addr.state}`);
    const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
    const url = isIos
        ? `maps://maps.apple.com/?q=${query}`
        : `https://www.google.com/maps/search/?api=1&query=${query}`;
    window.open(url, '_blank');
}
</script>

<template>
    <TechnicianLayout :title="job.title">
        <Head :title="job.title" />

        <!-- ── Header ──────────────────────────────────────────────────────── -->
        <div class="bg-white px-4 pb-3 pt-4 shadow-sm">
            <div class="mb-1 flex items-start justify-between gap-2">
                <h1 class="text-base font-semibold text-slate-900">{{ job.title }}</h1>
                <span
                    class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="STATUS_CLASSES[currentStatus] ?? 'bg-slate-100 text-slate-600'"
                >
                    {{ statuses[currentStatus] ?? currentStatus }}
                </span>
            </div>

            <p v-if="job.customer" class="text-sm text-slate-500">
                {{ job.customer.first_name }} {{ job.customer.last_name }}
            </p>
            <p v-if="job.property" class="text-sm text-slate-500">
                {{ job.property.address_line1 }}, {{ job.property.city }}
            </p>

            <!-- Get Directions -->
            <button
                v-if="job.property"
                type="button"
                class="mt-2 flex items-center gap-1.5 text-xs font-medium text-blue-600"
                @click="openDirections"
            >
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18m-7-7 7 7-7 7" />
                </svg>
                Get Directions
            </button>

            <!-- Status action buttons -->
            <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                <button
                    v-for="action in TECHNICIAN_ACTIONS"
                    :key="action.key"
                    type="button"
                    class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-semibold transition"
                    :class="currentStatus === action.key
                        ? 'bg-slate-900 text-white'
                        : 'bg-slate-100 text-slate-600 active:bg-slate-200'"
                    :disabled="updatingStatus"
                    @click="updateStatus(action.key)"
                >
                    {{ action.label }}
                </button>
            </div>
        </div>

        <!-- ── Tabs ────────────────────────────────────────────────────────── -->
        <div class="sticky top-14 z-10 bg-white shadow-sm" style="top: calc(3.5rem + env(safe-area-inset-top, 0px))">
            <div class="flex overflow-x-auto border-b border-slate-200">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    type="button"
                    class="shrink-0 whitespace-nowrap px-4 py-3 text-sm font-medium transition"
                    :class="activeTab === tab.key
                        ? 'border-b-2 border-slate-900 text-slate-900'
                        : 'text-slate-500'"
                    @click="activeTab = tab.key"
                >
                    {{ tab.label }}
                </button>
            </div>
        </div>

        <div class="p-4">
            <!-- ── Checklist tab ───────────────────────────────────────────── -->
            <template v-if="activeTab === 'checklist'">
                <!-- Progress bar -->
                <div v-if="checklistProgress" class="mb-4">
                    <div class="mb-1 flex justify-between text-xs text-slate-500">
                        <span>{{ checklistProgress.done }} / {{ checklistProgress.total }} complete</span>
                        <span>{{ checklistProgress.pct }}%</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                        <div
                            class="h-full rounded-full bg-green-500 transition-all"
                            :style="{ width: checklistProgress.pct + '%' }"
                        />
                    </div>
                </div>

                <div v-if="!job.checklist_items?.length" class="rounded-xl bg-white py-8 text-center shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm text-slate-500">No checklist items for this job.</p>
                </div>

                <ul v-else class="space-y-2">
                    <li
                        v-for="item in job.checklist_items"
                        :key="item.id"
                        class="flex items-center gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200"
                        @click="toggleChecklist(item)"
                    >
                        <div
                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 transition"
                            :class="checklistState[item.id]
                                ? 'border-green-500 bg-green-500 text-white'
                                : 'border-slate-300 bg-white'"
                        >
                            <svg v-if="checklistState[item.id]" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span
                            class="flex-1 text-sm"
                            :class="checklistState[item.id] ? 'text-slate-400 line-through' : 'text-slate-800'"
                        >
                            {{ item.label }}
                            <span v-if="item.is_required" class="ml-1 text-xs text-red-500">*</span>
                        </span>
                        <span v-if="togglingItem === item.id" class="text-xs text-slate-400">…</span>
                    </li>
                </ul>
            </template>

            <!-- ── Photos tab ─────────────────────────────────────────────── -->
            <template v-else-if="activeTab === 'photos'">
                <!-- Upload progress -->
                <div v-if="uploadingTag" class="mb-4 rounded-lg bg-slate-100 px-4 py-3">
                    <p class="mb-1 text-sm text-slate-600">Uploading {{ uploadingTag }} photo… {{ uploadProgress }}%</p>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-300">
                        <div class="h-full rounded-full bg-blue-500 transition-all" :style="{ width: uploadProgress + '%' }" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Before photos -->
                    <div>
                        <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Before</h3>
                        <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-3">
                            <div
                                v-for="photo in beforePhotos"
                                :key="photo.id"
                                class="relative aspect-square overflow-hidden rounded-lg bg-slate-100"
                            >
                                <img :src="photo.url" :alt="photo.filename" class="h-full w-full object-cover" />
                                <button
                                    type="button"
                                    class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white"
                                    :disabled="deletingPhotoId === photo.id"
                                    @click="deletePhoto(photo)"
                                >
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <path d="M18 6 6 18M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <label class="mt-2 flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-slate-300 p-3 text-xs font-medium text-slate-500 active:bg-slate-50">
                            <input type="file" accept="image/*" capture="environment" class="sr-only" @change="(e) => handlePhotoCapture(e, 'before')" />
                            + Add Before Photo
                        </label>
                    </div>

                    <!-- After photos -->
                    <div>
                        <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">After</h3>
                        <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-3">
                            <div
                                v-for="photo in afterPhotos"
                                :key="photo.id"
                                class="relative aspect-square overflow-hidden rounded-lg bg-slate-100"
                            >
                                <img :src="photo.url" :alt="photo.filename" class="h-full w-full object-cover" />
                                <button
                                    type="button"
                                    class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white"
                                    :disabled="deletingPhotoId === photo.id"
                                    @click="deletePhoto(photo)"
                                >
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <path d="M18 6 6 18M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <label class="mt-2 flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-slate-300 p-3 text-xs font-medium text-slate-500 active:bg-slate-50">
                            <input type="file" accept="image/*" capture="environment" class="sr-only" @change="(e) => handlePhotoCapture(e, 'after')" />
                            + Add After Photo
                        </label>
                    </div>
                </div>
            </template>

            <!-- ── Line items tab ─────────────────────────────────────────── -->
            <template v-else-if="activeTab === 'line_items'">
                <ul class="mb-4 space-y-2">
                    <li
                        v-for="li in lineItems"
                        :key="li.id"
                        class="flex items-center gap-3 rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-200"
                    >
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-800">{{ li.name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ parseFloat(li.quantity) }} × ${{ parseFloat(li.unit_price).toFixed(2) }}
                            </p>
                        </div>
                        <p class="text-sm font-semibold text-slate-900">
                            ${{ (parseFloat(li.unit_price) * parseFloat(li.quantity)).toFixed(2) }}
                        </p>
                        <button type="button" class="text-xs text-red-500 active:text-red-700" @click="deleteLineItem(li)">
                            Remove
                        </button>
                    </li>
                </ul>

                <div class="mb-4 flex justify-end text-sm font-semibold text-slate-800">
                    Total: ${{ lineItemTotal.toFixed(2) }}
                </div>

                <!-- Add line item form -->
                <div v-if="!showAddLineItem">
                    <button
                        type="button"
                        class="w-full rounded-xl border-2 border-dashed border-slate-300 py-3 text-sm font-medium text-slate-500 active:bg-slate-50"
                        @click="showAddLineItem = true"
                    >
                        + Add Line Item
                    </button>
                </div>

                <div v-else class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <!-- Catalog search -->
                    <input
                        v-model="catalogQuery"
                        type="text"
                        placeholder="Search catalog…"
                        class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        @input="searchCatalog"
                    />
                    <ul v-if="catalogResults.length" class="mb-2 rounded-lg border border-slate-200 bg-white divide-y divide-slate-100 text-sm">
                        <li
                            v-for="item in catalogResults"
                            :key="item.id"
                            class="cursor-pointer px-3 py-2 hover:bg-slate-50 active:bg-slate-100"
                            @click="selectCatalogItem(item)"
                        >
                            {{ item.name }} — ${{ parseFloat(item.unit_price).toFixed(2) }}
                        </li>
                    </ul>

                    <input v-model="addForm.name" type="text" placeholder="Item name" class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                    <div class="mb-2 grid grid-cols-2 gap-2">
                        <input v-model="addForm.unit_price" type="number" step="0.01" placeholder="Unit price" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                        <input v-model="addForm.quantity" type="number" step="0.001" placeholder="Quantity" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="flex-1 rounded-lg bg-slate-900 py-2 text-sm font-semibold text-white active:bg-slate-700" :disabled="addingLineItem" @click="addLineItem">
                            Add
                        </button>
                        <button type="button" class="rounded-lg bg-slate-100 px-4 py-2 text-sm text-slate-600" @click="showAddLineItem = false">
                            Cancel
                        </button>
                    </div>
                </div>
            </template>

            <!-- ── Notes tab ──────────────────────────────────────────────── -->
            <template v-else-if="activeTab === 'notes'">
                <!-- Office notes (read-only) -->
                <div v-if="job.office_notes" class="mb-4 rounded-xl bg-amber-50 p-4 ring-1 ring-amber-200">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-amber-700">Office Notes</p>
                    <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ job.office_notes }}</p>
                </div>

                <!-- Technician notes -->
                <div class="mb-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">My Notes</p>
                        <button v-if="!editingNotes" type="button" class="text-xs text-blue-600" @click="editingNotes = true">Edit</button>
                    </div>
                    <template v-if="editingNotes">
                        <textarea
                            v-model="notesForm.technician_notes"
                            rows="4"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            placeholder="Add your notes…"
                        />
                        <div class="mt-2 flex gap-2">
                            <button type="button" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white" :disabled="notesForm.processing" @click="saveNotes">Save</button>
                            <button type="button" class="rounded-lg bg-slate-100 px-4 py-2 text-xs text-slate-600" @click="editingNotes = false">Cancel</button>
                        </div>
                    </template>
                    <p v-else class="text-sm text-slate-700 whitespace-pre-wrap">{{ notesForm.technician_notes || 'No notes yet.' }}</p>
                </div>

                <!-- Customer notes -->
                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Customer Notes</p>
                        <button v-if="!editingCustomerNotes" type="button" class="text-xs text-blue-600" @click="editingCustomerNotes = true">Edit</button>
                    </div>
                    <template v-if="editingCustomerNotes">
                        <textarea
                            v-model="customerNotesForm.customer_notes"
                            rows="4"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            placeholder="Notes visible to customer…"
                        />
                        <div class="mt-2 flex gap-2">
                            <button type="button" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white" :disabled="customerNotesForm.processing" @click="saveCustomerNotes">Save</button>
                            <button type="button" class="rounded-lg bg-slate-100 px-4 py-2 text-xs text-slate-600" @click="editingCustomerNotes = false">Cancel</button>
                        </div>
                    </template>
                    <p v-else class="text-sm text-slate-700 whitespace-pre-wrap">{{ customerNotesForm.customer_notes || 'No customer notes yet.' }}</p>
                </div>
            </template>

            <!-- ── Signature tab ──────────────────────────────────────────── -->
            <template v-else-if="activeTab === 'signature'">
                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <p class="mb-3 text-sm font-medium text-slate-700">Customer Signature</p>

                    <div v-if="sigSaved" class="rounded-lg bg-green-50 p-4 text-center">
                        <svg class="mx-auto mb-2 h-8 w-8 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-medium text-green-700">Signature saved</p>
                        <button type="button" class="mt-2 text-xs text-green-600 underline" @click="sigSaved = false; clearSignature()">
                            Re-capture
                        </button>
                    </div>

                    <template v-else>
                        <p class="mb-2 text-xs text-slate-500">Have the customer sign in the box below.</p>
                        <canvas
                            ref="signatureCanvas"
                            width="360"
                            height="180"
                            class="w-full touch-none rounded-lg border-2 border-slate-300 bg-slate-50"
                            style="cursor: crosshair"
                            @mousedown="startDraw"
                            @mousemove="draw"
                            @mouseup="stopDraw"
                            @mouseleave="stopDraw"
                            @touchstart.prevent="startDraw"
                            @touchmove.prevent="draw"
                            @touchend="stopDraw"
                        />
                        <div class="mt-3 flex gap-2">
                            <button
                                type="button"
                                class="flex-1 rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white active:bg-slate-700"
                                :disabled="savingSig"
                                @click="saveSignature"
                            >
                                {{ savingSig ? 'Saving…' : 'Save Signature' }}
                            </button>
                            <button
                                type="button"
                                class="rounded-lg bg-slate-100 px-4 py-2.5 text-sm text-slate-600"
                                @click="clearSignature"
                            >
                                Clear
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </TechnicianLayout>
</template>
