<?php

namespace Modules\ZeroPay\Filament\Resources\EstimateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\ZeroPay\Filament\Resources\EstimateResource;

class ListEstimates extends ListRecords
{
    protected static string $resource = EstimateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
