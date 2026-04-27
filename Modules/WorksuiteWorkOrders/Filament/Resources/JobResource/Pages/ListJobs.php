<?php

namespace Modules\WorksuiteWorkOrders\Filament\Resources\JobResource\Pages;

use Modules\WorksuiteWorkOrders\Filament\Resources\JobResource;
use Filament\Resources\Pages\ListRecords;

class ListJobs extends ListRecords
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
