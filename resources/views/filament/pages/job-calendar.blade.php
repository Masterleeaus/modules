<x-filament-panels::page>
    <div
        x-data="jobCalendar()"
        x-init="init()"
        class="h-[calc(100vh-12rem)]"
    >
        <div id="filament-job-calendar" class="h-full rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"></div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        function jobCalendar() {
            return {
                calendar: null,
                init() {
                    const el = document.getElementById('filament-job-calendar');
                    if (!el || !window.FullCalendar) {
                        setTimeout(() => this.init(), 300);
                        return;
                    }
                    this.calendar = new FullCalendar.Calendar(el, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left:   'prev,next today',
                            center: 'title',
                            right:  'dayGridMonth,timeGridWeek,listWeek',
                        },
                        events: (info, successCallback, failureCallback) => {
                            fetch(`/owner/calendar/events?start=${info.startStr}&end=${info.endStr}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                                },
                            })
                            .then(r => r.json())
                            .then(data => successCallback(data))
                            .catch(() => failureCallback());
                        },
                        eventClick: (info) => {
                            if (info.event.url) {
                                info.jsEvent.preventDefault();
                                window.open(info.event.url, '_blank');
                            }
                        },
                        height: '100%',
                    });
                    this.calendar.render();
                },
            };
        }
    </script>
    @endpush
</x-filament-panels::page>
