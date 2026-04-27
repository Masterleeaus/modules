<?php

namespace App\Filament\Resources\DriverLocationResource\Pages;

use App\Filament\Resources\DriverLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDriverLocations extends ListRecords
{
    protected static string $resource = DriverLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
