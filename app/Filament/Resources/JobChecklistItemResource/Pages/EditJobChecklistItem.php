<?php

namespace App\Filament\Resources\JobChecklistItemResource\Pages;

use App\Filament\Resources\JobChecklistItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobChecklistItem extends EditRecord
{
    protected static string $resource = JobChecklistItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
