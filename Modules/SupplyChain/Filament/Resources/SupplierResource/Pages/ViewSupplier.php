<?php

namespace Modules\SupplyChain\Filament\Resources\SupplierResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\SupplyChain\Filament\Resources\SupplierResource;

class ViewSupplier extends ViewRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
