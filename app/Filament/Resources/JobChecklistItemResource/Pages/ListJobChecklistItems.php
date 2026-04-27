<?php

namespace App\Filament\Resources\JobChecklistItemResource\Pages;

use App\Filament\Resources\JobChecklistItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobChecklistItems extends ListRecords
{
    protected static string $resource = JobChecklistItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
