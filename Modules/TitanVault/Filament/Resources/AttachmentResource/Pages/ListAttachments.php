<?php

namespace Modules\TitanVault\Filament\Resources\AttachmentResource\Pages;

use Modules\TitanVault\Filament\Resources\AttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttachments extends ListRecords
{
    protected static string $resource = AttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
