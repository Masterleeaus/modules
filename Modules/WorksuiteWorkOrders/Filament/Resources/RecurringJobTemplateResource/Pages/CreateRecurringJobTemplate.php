<?php

namespace Modules\WorksuiteWorkOrders\Filament\Resources\RecurringJobTemplateResource\Pages;

use Modules\WorksuiteWorkOrders\Filament\Resources\RecurringJobTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecurringJobTemplate extends CreateRecord
{
    protected static string $resource = RecurringJobTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()?->organization_id;
        return $data;
    }
}
