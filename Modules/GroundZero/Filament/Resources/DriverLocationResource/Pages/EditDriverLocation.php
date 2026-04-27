<?php

namespace Modules\GroundZero\Filament\Resources\DriverLocationResource\Pages;

use Modules\GroundZero\Filament\Resources\DriverLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriverLocation extends EditRecord
{
    protected static string $resource = DriverLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
