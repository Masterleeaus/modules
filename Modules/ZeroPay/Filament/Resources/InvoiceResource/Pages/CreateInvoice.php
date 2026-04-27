<?php

namespace Modules\ZeroPay\Filament\Resources\InvoiceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ZeroPay\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
