<?php

namespace App\Filament\Resources\DriverLocationResource\Pages;

use App\Filament\Resources\DriverLocationResource;
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
