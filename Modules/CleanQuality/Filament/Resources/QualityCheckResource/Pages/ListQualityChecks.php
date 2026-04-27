<?php

namespace Modules\CleanQuality\Filament\Resources\QualityCheckResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\CleanQuality\Filament\Resources\QualityCheckResource;

class ListQualityChecks extends ListRecords
{
    protected static string $resource = QualityCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
