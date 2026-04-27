<?php

namespace Modules\SupplyChain\Filament\Resources\PurchaseOrderResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\SupplyChain\Filament\Resources\PurchaseOrderResource;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
