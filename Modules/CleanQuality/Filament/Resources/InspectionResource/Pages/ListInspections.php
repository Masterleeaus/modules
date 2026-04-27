<?php

namespace Modules\CleanQuality\Filament\Resources\InspectionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\CleanQuality\Filament\Resources\InspectionResource;

class ListInspections extends ListRecords
{
    protected static string $resource = InspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
