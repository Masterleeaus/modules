<?php

namespace App\Filament\Resources\JobTypeChecklistItemResource\Pages;

use App\Filament\Resources\JobTypeChecklistItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobTypeChecklistItem extends EditRecord
{
    protected static string $resource = JobTypeChecklistItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
