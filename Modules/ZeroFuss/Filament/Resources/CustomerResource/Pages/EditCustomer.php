<?php

namespace Modules\ZeroFuss\Filament\Resources\CustomerResource\Pages;

use Modules\ZeroFuss\Filament\Resources\CustomerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
