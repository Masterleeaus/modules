<?php

namespace Modules\WorksuiteWorkOrders\Filament\Resources\MessageTemplateResource\Pages;

use Modules\WorksuiteWorkOrders\Filament\Resources\MessageTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMessageTemplate extends EditRecord
{
    protected static string $resource = MessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
