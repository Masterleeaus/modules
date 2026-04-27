<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border p-6 {{ $isConnected ? 'border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-900/20' : 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20' }}">
            <div class="flex items-center gap-3">
                @if($isConnected)
                    <x-heroicon-o-check-circle class="h-8 w-8 text-green-600"/>
                    <div>
                        <p class="text-lg font-semibold text-green-700 dark:text-green-400">Connected to Xero</p>
                        <p class="text-sm text-green-600 dark:text-green-500">Your Xero integration is active. Invoices and payments are being synced automatically.</p>
                    </div>
                @else
                    <x-heroicon-o-x-circle class="h-8 w-8 text-red-600"/>
                    <div>
                        <p class="text-lg font-semibold text-red-700 dark:text-red-400">Not Connected</p>
                        <p class="text-sm text-red-600 dark:text-red-500">Configure your Xero API credentials in the accounting settings to enable sync.</p>
                    </div>
                @endif
            </div>
        </div>

        @if(count($failedSyncs) > 0)
            <div>
                <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-white">Failed Syncs</h3>
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Entity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Provider</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Error</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Retries</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                            @foreach($failedSyncs as $sync)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ class_basename($sync['syncable_type']) }} #{{ $sync['syncable_id'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm capitalize text-gray-700 dark:text-gray-300">{{ $sync['provider'] }}</td>
                                    <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">{{ \Illuminate\Support\Str::limit($sync['last_error'] ?? '', 60) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $sync['retry_count'] }}</td>
                                    <td class="px-4 py-3">
                                        <button
                                            wire:click="retrySync({{ $sync['id'] }})"
                                            class="rounded-lg bg-warning-100 px-3 py-1 text-xs font-medium text-warning-700 hover:bg-warning-200 dark:bg-warning-900/30 dark:text-warning-400"
                                        >
                                            Retry
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-gray-600">
                No failed syncs. Everything is up to date.
            </div>
        @endif
    </div>
</x-filament-panels::page>
