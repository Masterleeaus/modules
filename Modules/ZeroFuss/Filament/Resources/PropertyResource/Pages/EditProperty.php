<?php

namespace Modules\ZeroFuss\Filament\Resources\PropertyResource\Pages;

use Modules\ZeroFuss\Filament\Resources\PropertyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
