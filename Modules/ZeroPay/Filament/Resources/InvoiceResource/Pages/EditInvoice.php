<?php

namespace Modules\ZeroPay\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\ZeroPay\Filament\Resources\InvoiceResource;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
