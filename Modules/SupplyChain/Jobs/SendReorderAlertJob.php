<?php

namespace Modules\SupplyChain\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\SupplyChain\Entities\StockLevel;
use Modules\SupplyChain\Notifications\ReorderAlertNotification;

class SendReorderAlertJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $stockLevelId)
    {
    }

    public function handle(): void
    {
        $stockLevel = StockLevel::with(['item', 'warehouse'])->find($this->stockLevelId);

        if (!$stockLevel) {
            return;
        }

        Log::info('SupplyChain reorder alert generated', [
            'stock_level_id' => $stockLevel->id,
            'item_id'        => $stockLevel->item_id,
            'warehouse_id'   => $stockLevel->warehouse_id,
            'qty_available'  => $stockLevel->qty_available,
            'min_qty'        => $stockLevel->min_qty,
        ]);

        $payload = [
            'item_id'        => $stockLevel->item_id,
            'item_name'      => optional($stockLevel->item)->name ?? 'Unknown',
            'warehouse_id'   => $stockLevel->warehouse_id,
            'warehouse_name' => optional($stockLevel->warehouse)->name ?? 'Unknown',
            'qty_available'  => $stockLevel->qty_available,
            'min_qty'        => $stockLevel->min_qty,
        ];

        try {
            $this->notifyAdmins($stockLevel->company_id, $payload);
        } catch (\Throwable $e) {
            Log::warning('SupplyChain reorder notification failed', [
                'error'          => $e->getMessage(),
                'stock_level_id' => $stockLevel->id,
            ]);
        }
    }

    private function notifyAdmins(?int $companyId, array $payload): void
    {
        if (!$companyId) {
            return;
        }

        $admins = User::where('company_id', $companyId)
            ->whereHas('roles', fn ($q) => $q->where('name', 'admin'))
            ->where('status', 'active')
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new ReorderAlertNotification($payload));
        }
    }
}
