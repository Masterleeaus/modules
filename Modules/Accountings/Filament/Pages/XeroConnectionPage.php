<?php

namespace Modules\Accountings\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Modules\Accountings\Entities\ExternalSyncRecord;

class XeroConnectionPage extends Page
{
    protected static ?string $slug = 'accounting/xero-connection';

    protected static ?string $navigationLabel = 'Xero Connection';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cloud-arrow-up';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?int $navigationSort = 50;

    protected string $view = 'accountings::filament.pages.xero-connection';

    public bool $isConnected = false;
    public array $failedSyncs = [];

    public function mount(): void
    {
        $this->isConnected = $this->checkConnection();
        $this->loadFailedSyncs();
    }

    protected function checkConnection(): bool
    {
        $orgId = auth()->user()?->organization_id;
        if (! $orgId) {
            return false;
        }

        // Check if Xero credentials exist
        if (! \Illuminate\Support\Facades\Schema::hasTable('acc_accounting_settings')) {
            return false;
        }

        $settings = \Illuminate\Support\Facades\DB::table('acc_accounting_settings')
            ->where('company_id', $orgId)
            ->first();

        return $settings
            && ! empty($settings->xero_tenant_id)
            && ! empty($settings->xero_client_id)
            && ! empty($settings->xero_client_secret);
    }

    protected function loadFailedSyncs(): void
    {
        $orgId = auth()->user()?->organization_id;
        if (! $orgId) {
            $this->failedSyncs = [];
            return;
        }

        $this->failedSyncs = ExternalSyncRecord::where('organization_id', $orgId)
            ->where('status', 'failed')
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function retrySync(int $recordId): void
    {
        $record = ExternalSyncRecord::find($recordId);

        if (! $record) {
            Notification::make()->danger()->title('Sync record not found')->send();
            return;
        }

        $record->update(['status' => 'pending', 'last_error' => null]);

        Notification::make()->success()->title('Sync queued for retry')->send();

        $this->loadFailedSyncs();
    }

    protected function getViewData(): array
    {
        return [
            'isConnected' => $this->isConnected,
            'failedSyncs' => $this->failedSyncs,
        ];
    }
}
