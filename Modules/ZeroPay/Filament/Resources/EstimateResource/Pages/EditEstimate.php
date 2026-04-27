<?php

namespace Modules\ZeroPay\Filament\Resources\EstimateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\ZeroPay\Filament\Resources\EstimateResource;

class EditEstimate extends EditRecord
{
    protected static string $resource = EstimateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
