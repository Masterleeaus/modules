<?php

namespace Modules\CleanQuality\Filament\Resources\QualityCheckResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\CleanQuality\Filament\Resources\QualityCheckResource;

class ViewQualityCheck extends ViewRecord
{
    protected static string $resource = QualityCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
