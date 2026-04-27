<script setup lang="ts">
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

// ── Image compression ─────────────────────────────────────────────────────────
async function compressImage(file: File, maxKB = 800): Promise<Blob> {
    return new Promise((resolve) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            const canvas = document.createElement('canvas');
            const MAX_DIM = 1920;
            let { width, height } = img;
            if (width > MAX_DIM || height > MAX_DIM) {
                if (width > height) { height = Math.round((height * MAX_DIM) / width); width = MAX_DIM; }
                else { width = Math.round((width * MAX_DIM) / height); height = MAX_DIM; }
            }
            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d')!.drawImage(img, 0, 0, width, height);
            const tryQuality = (q: number) => {
                canvas.toBlob((blob) => {
                    if (!blob) { resolve(new Blob()); return; }
                    if (blob.size <= maxKB * 1024 || q <= 0.3) { resolve(blob); return; }
                    tryQuality(Math.round((q - 0.1) * 10) / 10);
                }, 'image/jpeg', q);
            };
            tryQuality(0.85);
        };
        img.src = url;
    });
}

const props = defineProps<{
    jobId: number;
    tag?: 'before' | 'after' | null;
}>();

type PhotoTag = 'before' | 'after';

interface CapturedPhoto {
    id: string;
    url: string;
    tag: PhotoTag;
    file?: File;
    uploaded: boolean;
    error?: string;
}

const selectedTag = ref<PhotoTag>(props.tag ?? 'before');
const capturedPhotos = ref<CapturedPhoto[]>([]);
const isCapturing = ref(false);
const videoRef = ref<HTMLVideoElement | null>(null);
const stream = ref<MediaStream | null>(null);
const useCamera = ref(false);

async function startCamera() {
    try {
        stream.value = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' },
        });
        if (videoRef.value) videoRef.value.srcObject = stream.value;
        useCamera.value = true;
    } catch {
        // Fall back to file input
        useCamera.value = false;
    }
}

function stopCamera() {
    stream.value?.getTracks().forEach((t) => t.stop());
    stream.value = null;
    useCamera.value = false;
}

async function captureFrame() {
    if (!videoRef.value) return;
    const canvas = document.createElement('canvas');
    canvas.width = videoRef.value.videoWidth;
    canvas.height = videoRef.value.videoHeight;
    canvas.getContext('2d')!.drawImage(videoRef.value, 0, 0);
    const blob = await new Promise<Blob>((res) => canvas.toBlob((b) => res(b!), 'image/jpeg', 0.9));
    const file = new File([blob], `capture_${Date.now()}.jpg`, { type: 'image/jpeg' });
    await queuePhoto(file);
}

async function handleFileInput(e: Event) {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;
    input.value = '';
    await queuePhoto(file);
}

async function queuePhoto(file: File) {
    const compressed = await compressImage(file);
    const compressedFile = new File([compressed], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' });
    const localUrl = URL.createObjectURL(compressed);
    const id = `local_${Date.now()}`;

    capturedPhotos.value.push({
        id,
        url: localUrl,
        tag: selectedTag.value,
        file: compressedFile,
        uploaded: false,
    });

    await uploadPhoto(id, compressedFile);
}

async function uploadPhoto(id: string, file: File) {
    const formData = new FormData();
    formData.append('photo', file);
    formData.append('tag', selectedTag.value);

    const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', `/api/technician/jobs/${props.jobId}/photos`);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');
    if (csrfMeta) xhr.setRequestHeader('X-CSRF-TOKEN', csrfMeta.content);

    xhr.onload = () => {
        const photo = capturedPhotos.value.find((p) => p.id === id);
        if (photo) {
            photo.uploaded = xhr.status === 201;
            if (xhr.status !== 201) photo.error = 'Upload failed';
        }
    };
    xhr.onerror = () => {
        const photo = capturedPhotos.value.find((p) => p.id === id);
        if (photo) photo.error = 'Upload failed';
    };
    xhr.send(formData);
}

function removePhoto(id: string) {
    const idx = capturedPhotos.value.findIndex((p) => p.id === id);
    if (idx !== -1) capturedPhotos.value.splice(idx, 1);
}
</script>

<template>
    <TechnicianLayout title="Capture Photo">
        <Head title="Capture Photo" />

        <div class="p-4">
            <!-- Tag selector -->
            <div class="mb-4 flex gap-2">
                <button
                    v-for="t in ['before', 'after'] as PhotoTag[]"
                    :key="t"
                    type="button"
                    class="flex-1 rounded-xl py-2.5 text-sm font-semibold transition"
                    :class="selectedTag === t
                        ? 'bg-slate-900 text-white'
                        : 'bg-slate-100 text-slate-600'"
                    @click="selectedTag = t"
                >
                    {{ t.charAt(0).toUpperCase() + t.slice(1) }}
                </button>
            </div>

            <!-- Camera view -->
            <div v-if="useCamera" class="mb-4">
                <video ref="videoRef" autoplay playsinline class="w-full rounded-xl bg-black" />
                <div class="mt-3 flex gap-2">
                    <button
                        type="button"
                        class="flex-1 rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white active:bg-slate-700"
                        @click="captureFrame"
                    >
                        📷 Capture
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-slate-100 px-4 py-3 text-sm text-slate-600"
                        @click="stopCamera"
                    >
                        Cancel
                    </button>
                </div>
            </div>

            <!-- File input fallback / launcher -->
            <div v-else class="mb-4 grid grid-cols-2 gap-3">
                <button
                    type="button"
                    class="flex flex-col items-center justify-center gap-2 rounded-xl bg-slate-900 py-5 text-sm font-semibold text-white active:bg-slate-700"
                    @click="startCamera"
                >
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                    Use Camera
                </button>

                <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl bg-slate-100 py-5 text-sm font-semibold text-slate-700 active:bg-slate-200">
                    <input type="file" accept="image/*" capture="environment" class="sr-only" @change="handleFileInput" />
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    From Gallery
                </label>
            </div>

            <!-- Captured photos -->
            <div v-if="capturedPhotos.length" class="mt-4">
                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Captured ({{ capturedPhotos.length }})
                </h3>
                <div class="grid grid-cols-3 gap-2">
                    <div
                        v-for="photo in capturedPhotos"
                        :key="photo.id"
                        class="relative aspect-square overflow-hidden rounded-lg"
                    >
                        <img :src="photo.url" :alt="`${photo.tag} photo`" class="h-full w-full object-cover" />
                        <!-- Upload status indicator -->
                        <div class="absolute bottom-0 left-0 right-0 flex justify-center bg-black/40 py-0.5">
                            <span v-if="photo.error" class="text-xs text-red-400">Failed</span>
                            <span v-else-if="photo.uploaded" class="text-xs text-green-400">✓</span>
                            <span v-else class="text-xs text-white">…</span>
                        </div>
                        <!-- Tag badge -->
                        <span
                            class="absolute left-1 top-1 rounded px-1.5 py-0.5 text-xs font-medium"
                            :class="photo.tag === 'before' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white'"
                        >
                            {{ photo.tag }}
                        </span>
                        <button
                            type="button"
                            class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white"
                            @click="removePhoto(photo.id)"
                        >
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <path d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </TechnicianLayout>
</template>
