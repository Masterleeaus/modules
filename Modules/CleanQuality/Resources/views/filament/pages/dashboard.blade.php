<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Scoreboard widget rendered inline --}}
        @foreach ($this->getWidgets() as $widget)
            @livewire($widget)
        @endforeach

        {{-- Quick-action links --}}
        <x-filament::section heading="Quick Links">
            <div class="flex flex-wrap gap-3">
                @foreach ([
                    ['inspection_schedules.index',         'heroicon-o-clipboard-document-list', 'Inspection Schedules'],
                    ['schedule-inspection.index',          'heroicon-o-magnifying-glass',         'Inspections'],
                    ['recurring-inspection_schedules.index','heroicon-o-arrow-path',              'Recurring Schedules'],
                ] as [$routeName, $icon, $label])
                    @if (\Illuminate\Support\Facades\Route::has($routeName))
                        <x-filament::link
                            :href="route($routeName)"
                            :icon="$icon"
                            color="gray"
                        >{{ $label }}</x-filament::link>
                    @endif
                @endforeach
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>


