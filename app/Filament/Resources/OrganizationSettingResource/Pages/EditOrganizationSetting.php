<?php

namespace App\Filament\Resources\OrganizationSettingResource\Pages;

use App\Filament\Resources\OrganizationSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationSetting extends EditRecord
{
    protected static string $resource = OrganizationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
