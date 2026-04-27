<?php

namespace App\Filament\Resources\EstimatePackageResource\Pages;

use App\Filament\Resources\EstimatePackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstimatePackages extends ListRecords
{
    protected static string $resource = EstimatePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
