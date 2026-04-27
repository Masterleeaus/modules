<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
                <input type="date" wire:model="from" class="mt-1 block rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
                <input type="date" wire:model="to" class="mt-1 block rounded-md border-gray-300 shadow-sm">
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    <input type="checkbox" wire:model="paidOnly"> Paid invoices only
                </label>
            </div>
            <div class="flex items-end">
                <button wire:click="filter" class="fi-btn fi-btn-color-primary fi-btn-size-md rounded-lg bg-primary-600 px-4 py-2 text-white shadow-sm hover:bg-primary-500">
                    Apply
                </button>
            </div>
        </div>

        @if(count($rows) > 0)
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Job Ref</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Revenue</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Cost</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Profit</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Margin %</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Lines</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                        @foreach($rows as $row)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['job_ref'] }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                    {{ $row['revenue'] !== null ? '$'.number_format($row['revenue'], 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                    ${{ number_format($row['total_cost'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm @if(isset($row['profit']) && $row['profit'] >= 0) text-green-600 @else text-red-600 @endif">
                                    {{ $row['profit'] !== null ? '$'.number_format($row['profit'], 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                    {{ $row['margin'] !== null ? $row['margin'].'%' : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-500">{{ $row['line_count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-gray-600">
                No job cost data found for the selected period.
            </div>
        @endif
    </div>
</x-filament-panels::page>
