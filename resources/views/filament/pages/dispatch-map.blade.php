<x-filament-panels::page>
    <div
        x-data="filamentDispatchMap()"
        x-init="init()"
        class="flex gap-4"
        style="height: calc(100vh - 12rem);"
    >
        {{-- Sidebar --}}
        <aside class="flex w-64 shrink-0 flex-col gap-3">
            <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Show Trails</span>
                <button
                    type="button"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                    :class="showTrails ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-600'"
                    @click="toggleTrails()"
                >
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition"
                        :class="showTrails ? 'translate-x-6' : 'translate-x-1'"
                    ></span>
                </button>
            </div>

            <div class="flex-1 space-y-2 overflow-y-auto">
                <template x-for="tech in techs" :key="tech.id">
                    <button
                        type="button"
                        class="w-full rounded-xl border bg-white px-4 py-3 text-left shadow-sm transition dark:bg-gray-800"
                        :class="focused && focused.id === tech.id ? 'border-primary-500' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700'"
                        @click="focused = tech"
                    >
                        <div class="flex items-center gap-2">
                            <span
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white"
                                :style="{ background: techStatusColor(tech) }"
                                x-text="tech.name.charAt(0)"
                            ></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-800 dark:text-white" x-text="tech.name"></p>
                                <p class="text-xs capitalize text-gray-500" x-text="techStatusLabel(tech)"></p>
                            </div>
                        </div>
                    </button>
                </template>
                <p x-show="techs.length === 0" class="py-4 text-center text-sm text-gray-400">No technicians found.</p>
            </div>
        </aside>

        {{-- Map --}}
        <div class="relative flex-1 overflow-hidden rounded-xl border border-gray-200 shadow-sm dark:border-gray-700">
            <div x-ref="mapEl" class="h-full w-full bg-gray-100 dark:bg-gray-800">
                <div x-show="!hasMapsKey" class="flex h-full items-center justify-center text-gray-400">
                    <p class="text-sm">Set <code class="rounded bg-gray-200 px-1 dark:bg-gray-700">VITE_GOOGLE_MAPS_API_KEY</code> to enable the live map.</p>
                </div>
            </div>

            {{-- Focus panel --}}
            <div
                x-show="focused"
                x-transition:enter="transition duration-200"
                x-transition:enter-start="translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition duration-150"
                x-transition:leave-start="translate-x-0 opacity-100"
                x-transition:leave-end="translate-x-full opacity-0"
                class="absolute right-0 top-0 h-full w-72 overflow-y-auto bg-white shadow-xl dark:bg-gray-900"
            >
                <template x-if="focused">
                    <div>
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white"
                                    :style="{ background: techStatusColor(focused) }"
                                    x-text="focused.name.charAt(0)"
                                ></span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white" x-text="focused.name"></p>
                                    <p class="text-xs capitalize text-gray-500" x-text="techStatusLabel(focused)"></p>
                                </div>
                            </div>
                            <button type="button" @click="focused = null" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </button>
                        </div>

                        <div x-show="focused.location" class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Location</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="focused.location ? focused.location.latitude.toFixed(4) + ', ' + focused.location.longitude.toFixed(4) : ''"></p>
                            <p class="text-xs text-gray-400" x-text="focused.location ? 'Updated ' + formatTime(focused.location.recorded_at) : ''"></p>
                        </div>

                        <div x-show="focused.current_job" class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Current Job</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="focused.current_job?.title"></p>
                            <p class="mt-0.5 text-xs text-gray-500" x-text="focused.current_job?.customer"></p>
                            <p class="mt-0.5 text-xs text-gray-500" x-text="focused.current_job?.address"></p>
                        </div>

                        <div x-show="focused.upcoming_jobs && focused.upcoming_jobs.length > 0" class="px-4 py-3">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Upcoming Today</p>
                            <div class="space-y-3">
                                <template x-for="job in focused.upcoming_jobs" :key="job.id">
                                    <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="job.title"></p>
                                        <p class="mt-0.5 text-xs text-gray-500" x-text="job.customer"></p>
                                        <p class="mt-0.5 text-xs text-gray-400" x-text="formatTime(job.scheduled_at)"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="focused && !focused.current_job && (!focused.upcoming_jobs || focused.upcoming_jobs.length === 0)" class="px-4 py-6 text-center">
                            <p class="text-sm text-gray-400">No active or upcoming jobs today.</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function filamentDispatchMap() {
            const POLL_INTERVAL = 15000;
            const STATUS_COLORS = {
                en_route:    '#7c3aed',
                in_progress: '#d97706',
                scheduled:   '#2563eb',
                completed:   '#16a34a',
                on_hold:     '#ea580c',
                cancelled:   '#94a3b8',
            };

            return {
                hasMapsKey: !!'{{ config("services.google.maps_key", "") }}',
                techs: [],
                focused: null,
                showTrails: false,
                map: null,
                markers: new Map(),
                trails: new Map(),
                pollTimer: null,

                init() {
                    this.hasMapsKey = !!'{{ config("services.google.maps_key", "") }}';
                    if (this.hasMapsKey) {
                        this.loadGoogleMaps();
                    } else {
                        this.fetchLocations();
                    }
                },

                loadGoogleMaps() {
                    const key = '{{ config("services.google.maps_key", "") }}';
                    if (!key) return;
                    if (window.google?.maps) {
                        this.initMap();
                        return;
                    }
                    const script = document.createElement('script');
                    script.src = `https://maps.googleapis.com/maps/api/js?key=${key}&callback=__filamentGmapsInit`;
                    script.async = true;
                    window.__filamentGmapsInit = () => this.initMap();
                    document.head.appendChild(script);
                },

                initMap() {
                    const el = this.$refs.mapEl;
                    if (!el || !window.google) return;
                    this.map = new google.maps.Map(el, {
                        center: { lat: 39.5, lng: -98.35 },
                        zoom: 5,
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: false,
                    });
                    this.fetchLocations();
                    this.pollTimer = setInterval(() => this.fetchLocations(), POLL_INTERVAL);
                },

                async fetchLocations() {
                    const res = await fetch('/owner/dispatch/technicians', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!res.ok) return;
                    const json = await res.json();
                    this.techs = json.data;
                    this.updateMarkers();
                },

                updateMarkers() {
                    if (!this.map) return;
                    this.techs.forEach(tech => {
                        if (!tech.location) {
                            const m = this.markers.get(tech.id);
                            if (m) { m.setMap(null); this.markers.delete(tech.id); }
                            return;
                        }
                        const pos = { lat: tech.location.latitude, lng: tech.location.longitude };
                        const color = this.techStatusColor(tech);
                        if (this.markers.has(tech.id)) {
                            const m = this.markers.get(tech.id);
                            m.setPosition(pos);
                            m.setIcon(this.makeIcon(color));
                        } else {
                            const m = new google.maps.Marker({
                                position: pos, map: this.map, title: tech.name, icon: this.makeIcon(color),
                            });
                            m.addListener('click', () => { this.focused = tech; });
                            this.markers.set(tech.id, m);
                        }
                    });
                    if (this.showTrails) this.refreshTrails();
                },

                makeIcon(color) {
                    return {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 18,
                        fillColor: color,
                        fillOpacity: 1,
                        strokeColor: '#fff',
                        strokeWeight: 2,
                    };
                },

                async toggleTrails() {
                    this.showTrails = !this.showTrails;
                    await this.refreshTrails();
                },

                async refreshTrails() {
                    this.trails.forEach(p => p.setMap(null));
                    this.trails.clear();
                    if (!this.showTrails || !this.map) return;
                    for (const tech of this.techs) {
                        if (!tech.location) continue;
                        const res = await fetch(`/owner/dispatch/technicians/${tech.id}/trail`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!res.ok) continue;
                        const points = (await res.json()).data;
                        if (points.length < 2) continue;
                        const poly = new google.maps.Polyline({
                            path: points, geodesic: true,
                            strokeColor: this.techStatusColor(tech),
                            strokeOpacity: 0.6, strokeWeight: 3, map: this.map,
                        });
                        this.trails.set(tech.id, poly);
                    }
                },

                techStatusColor(tech) {
                    const s = tech.current_job?.status;
                    return s ? (STATUS_COLORS[s] ?? '#94a3b8') : (tech.location ? '#16a34a' : '#94a3b8');
                },

                techStatusLabel(tech) {
                    const s = tech.current_job?.status;
                    const labels = { en_route: 'En Route', in_progress: 'In Progress', scheduled: 'Scheduled', completed: 'Completed', on_hold: 'On Hold', cancelled: 'Cancelled' };
                    return s ? (labels[s] ?? s) : (tech.location ? 'Available' : 'Offline');
                },

                formatTime(iso) {
                    if (!iso) return '—';
                    return new Date(iso).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
                },
            };
        }
    </script>
    @endpush
</x-filament-panels::page>
