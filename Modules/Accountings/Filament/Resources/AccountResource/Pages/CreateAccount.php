<?php

namespace Modules\Accountings\Filament\Resources\AccountResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Accountings\Filament\Resources\AccountResource;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()?->organization_id;

        return $data;
    }
}
