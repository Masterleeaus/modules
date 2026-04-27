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
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Job Type</label>
            <select wire:model="jobTypeId" class="block rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <option value="">All types</option>
                @foreach($jobTypes as $jt)
                    <option value="{{ $jt['id'] ?? $jt->id }}">{{ $jt['name'] ?? $jt->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Technician</label>
            <select wire:model="technicianId" class="block rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <option value="">All technicians</option>
                @foreach($technicians as $tech)
                    <option value="{{ $tech['id'] ?? $tech->id }}">{{ $tech['name'] ?? $tech->name }}</option>
                @endforeach
            </select>
        </div>
        <x-filament::button type="submit" size="sm">Apply</x-filament::button>
    </form>

    @if(count($jobs) === 0)
        <p class="text-sm text-gray-400">No completed jobs found for the selected period.</p>
    @else
        <div class="mb-4 grid grid-cols-3 gap-4">
            @php
                $totalRevenue = collect($jobs)->sum('revenue');
                $totalParts   = collect($jobs)->sum('parts_cost');
                $totalMargin  = collect($jobs)->sum('margin');
            @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Revenue</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Parts Cost</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalParts, 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Gross Margin</p>
                <p class="mt-1 text-2xl font-bold {{ $totalMargin >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($totalMargin, 2) }}</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Job</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Completed</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Technician</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Revenue</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Parts</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Margin</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Margin %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                    @foreach($jobs as $job)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $job['title'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $job['completed_at'] }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($job['job_type'])
                                    <span class="inline-flex items-center gap-1">
                                        <span class="inline-block h-2 w-2 rounded-full" style="background: {{ $job['job_type']['color'] }}"></span>
                                        {{ $job['job_type']['name'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $job['technician']['name'] ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">${{ number_format($job['revenue'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">${{ number_format($job['parts_cost'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-sm font-medium {{ $job['margin'] >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($job['margin'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-sm {{ ($job['margin_pct'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $job['margin_pct'] !== null ? $job['margin_pct'].'%' : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
