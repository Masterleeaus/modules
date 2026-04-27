<x-filament-panels::page>
    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="4"
    />

    <div
        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"
        id="dispatch-board"
    >
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Real-Time Dispatch Board
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Drag jobs between technician columns to reassign.
            </p>
        </div>

        {{-- The Vue component for the interactive dispatch board is rendered
             via the Owner/Dispatch/DispatchBoard.vue Inertia page.
             The Filament page acts as the management entry point and displays
             live stats; operators using the full dispatch board should visit
             the Owner dispatch route directly. --}}
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Use the
            <a href="/owner/dispatch" class="font-medium text-primary-600 hover:underline dark:text-primary-400">
                Dispatch Map &amp; Board
            </a>
            for live drag-drop scheduling and real-time technician tracking.
        </p>
    </div>
</x-filament-panels::page>
