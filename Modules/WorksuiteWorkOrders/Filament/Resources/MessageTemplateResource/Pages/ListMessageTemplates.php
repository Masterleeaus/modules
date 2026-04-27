<?php

namespace Modules\WorksuiteWorkOrders\Filament\Resources\MessageTemplateResource\Pages;

use Modules\WorksuiteWorkOrders\Filament\Resources\MessageTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMessageTemplates extends ListRecords
{
    protected static string $resource = MessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
