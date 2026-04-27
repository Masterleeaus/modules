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
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Basis</label>
                <select wire:model="basis" class="mt-1 block rounded-md border-gray-300 shadow-sm">
                    <option value="accrual">Accrual</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="filter" class="fi-btn fi-btn-color-primary fi-btn-size-md rounded-lg bg-primary-600 px-4 py-2 text-white shadow-sm hover:bg-primary-500">
                    Apply
                </button>
            </div>
        </div>

        @if(!empty($summary))
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs text-gray-500 uppercase">GST Collected</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">${{ number_format($summary['gst_collected'] ?? 0, 2) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs text-gray-500 uppercase">GST Paid</p>
                    <p class="mt-1 text-2xl font-semibold text-red-500">${{ number_format($summary['gst_paid'] ?? 0, 2) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs text-gray-500 uppercase">Net GST</p>
                    <p class="mt-1 text-2xl font-semibold {{ ($summary['net_gst'] ?? 0) >= 0 ? 'text-blue-600' : 'text-orange-500' }}">${{ number_format($summary['net_gst'] ?? 0, 2) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs text-gray-500 uppercase">Basis</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-700 dark:text-gray-300 capitalize">{{ $summary['basis'] ?? $basis }}</p>
                </div>
            </div>

            <div class="flex justify-end">
                <button wire:click="savePeriod" class="fi-btn fi-btn-color-success fi-btn-size-md rounded-lg bg-success-600 px-4 py-2 text-white shadow-sm hover:bg-success-500">
                    Save as BAS Period
                </button>
            </div>
        @endif

        @if(!empty($periods))
            <div>
                <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-white">Saved BAS Periods</h3>
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Period</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">GST Collected</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">GST Paid</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Net GST</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                            @foreach($periods as $period)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ $period['period_start'] }} — {{ $period['period_end'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">${{ number_format($period['gst_collected'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">${{ number_format($period['gst_paid'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-sm {{ $period['net_gst'] >= 0 ? 'text-blue-600' : 'text-orange-500' }}">${{ number_format($period['net_gst'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm capitalize">
                                        <span class="rounded-full px-2 py-1 text-xs {{ $period['status'] === 'lodged' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $period['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
