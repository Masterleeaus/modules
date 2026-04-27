<?php

namespace App\Filament\Resources\JobMessageResource\Pages;

use App\Filament\Resources\JobMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobMessages extends ListRecords
{
    protected static string $resource = JobMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
