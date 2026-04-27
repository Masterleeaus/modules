<x-filament-panels::page>
    <form wire:submit="filter" class="mb-6 flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
            <input type="date" wire:model="from" class="block rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
            <input type="date" wire:model="to" class="block rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
        </div>
        <x-filament::button type="submit" size="sm">Apply</x-filament::button>
    </form>

    @if(count($rows) === 0)
        <p class="text-sm text-gray-400">No jobs found for the selected period.</p>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Job Type</th>
                        @foreach($statuses as $key => $label)
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                    @foreach($rows as $row)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center gap-2">
                                    <span class="inline-block h-3 w-3 rounded-full" style="background: {{ $row['type']['color'] ?? '#94a3b8' }}"></span>
                                    {{ $row['type']['name'] }}
                                </span>
                            </td>
                            @foreach($statuses as $key => $label)
                                <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                    {{ $row['statuses'][$key] ?? 0 }}
                                </td>
                            @endforeach
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $row['total'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
