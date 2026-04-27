<?php

namespace Modules\WorksuiteWorkOrders\Filament\Resources\JobTypeChecklistItemResource\Pages;

use Modules\WorksuiteWorkOrders\Filament\Resources\JobTypeChecklistItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobTypeChecklistItems extends ListRecords
{
    protected static string $resource = JobTypeChecklistItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
