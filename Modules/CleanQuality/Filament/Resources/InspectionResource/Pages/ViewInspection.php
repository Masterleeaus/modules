<?php

namespace Modules\CleanQuality\Filament\Resources\InspectionResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\CleanQuality\Filament\Resources\InspectionResource;

class ViewInspection extends ViewRecord
{
    protected static string $resource = InspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
