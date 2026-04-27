<x-filament-panels::page>
    <div class="space-y-6">
        {{-- KPI row --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Scheduled Today</p>
                <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['scheduled_today'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Completed Today</p>
                <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['completed_today'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Avg Rating (week)</p>
                <p class="mt-2 text-3xl font-bold text-amber-500">
                    {{ $stats['avg_rating'] !== null ? $stats['avg_rating'].'★' : '—' }}
                </p>
            </div>
            <div class="rounded-xl border border-{{ $stats['unassigned'] > 0 ? 'amber' : 'gray' }}-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Unassigned Jobs</p>
                <p class="mt-2 text-3xl font-bold {{ $stats['unassigned'] > 0 ? 'text-amber-600' : 'text-gray-800 dark:text-white' }}">
                    {{ $stats['unassigned'] }}
                </p>
            </div>
        </div>

        {{-- Active jobs --}}
        <div>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Active Jobs</h2>
                <button wire:click="refresh" type="button" class="text-xs text-gray-400 hover:text-gray-600">↻ Refresh</button>
            </div>
            @if(count($activeJobs) === 0)
                <p class="rounded-xl border border-gray-200 bg-white px-5 py-4 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-800">
                    No active jobs right now.
                </p>
            @else
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($activeJobs as $job)
                        <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-1">
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $job['status'] === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-violet-100 text-violet-700' }}">
                                    {{ $job['status'] === 'in_progress' ? 'In Progress' : 'En Route' }}
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $job['title'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $job['customer'] }}</p>
                            <p class="text-xs text-gray-400">{{ $job['address'] }}</p>
                            <p class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-400">👤 {{ $job['technician'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Overdue recurring templates --}}
        @if(count($overdueTemplates) > 0)
        <div>
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Overdue Recurring Jobs</h2>
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Template</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Frequency</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Last Run</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                        @foreach($overdueTemplates as $tmpl)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $tmpl['title'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $tmpl['customer'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $tmpl['frequency'] }}</td>
                                <td class="px-4 py-3 text-sm text-rose-600">{{ $tmpl['last_run'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>
