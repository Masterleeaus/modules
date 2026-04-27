<x-filament-widgets::widget>
    <x-filament::section :heading="$this->getHeading()">
        <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">

            {{-- Inspections column --}}
            <div class="col-span-2 space-y-3">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                    Inspections (last {{ $period }})
                </p>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Total</span>
                    <span class="text-sm font-semibold">{{ $insp_total }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Pass Rate</span>
                    @if ($insp_pass_rate !== null)
                        <span class="text-sm font-semibold {{ $insp_pass_rate >= 80 ? 'text-success-600' : ($insp_pass_rate >= 60 ? 'text-warning-600' : 'text-danger-600') }}">
                            {{ $insp_pass_rate }}%
                        </span>
                    @else
                        <span class="text-sm text-gray-400">—</span>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Avg Score</span>
                    <span class="text-sm font-semibold">{{ $insp_avg_score ?? '—' }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Failed</span>
                    <span class="text-sm font-semibold text-danger-600">{{ $insp_failed }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Reclean Booked</span>
                    <span class="text-sm font-semibold text-warning-600">{{ $insp_reclean }}</span>
                </div>
            </div>

            {{-- QC Records column --}}
            <div class="col-span-2 space-y-3">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                    QC Records (last {{ $period }})
                </p>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Total</span>
                    <span class="text-sm font-semibold">{{ $qc_total }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Pass Rate</span>
                    @if ($qc_pass_rate !== null)
                        <span class="text-sm font-semibold {{ $qc_pass_rate >= 80 ? 'text-success-600' : ($qc_pass_rate >= 60 ? 'text-warning-600' : 'text-danger-600') }}">
                            {{ $qc_pass_rate }}%
                        </span>
                    @else
                        <span class="text-sm text-gray-400">—</span>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Avg Score</span>
                    <span class="text-sm font-semibold">{{ $qc_avg_score ?? '—' }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Failed</span>
                    <span class="text-sm font-semibold text-danger-600">{{ $qc_failed }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Reclean Triggered</span>
                    <span class="text-sm font-semibold text-warning-600">{{ $qc_reclean }}</span>
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>

