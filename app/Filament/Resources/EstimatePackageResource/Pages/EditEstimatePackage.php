<?php

namespace App\Filament\Resources\EstimatePackageResource\Pages;

use App\Filament\Resources\EstimatePackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstimatePackage extends EditRecord
{
    protected static string $resource = EstimatePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
