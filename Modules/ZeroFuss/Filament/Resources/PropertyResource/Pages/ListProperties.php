<?php

namespace Modules\ZeroFuss\Filament\Resources\PropertyResource\Pages;

use Modules\ZeroFuss\Filament\Resources\PropertyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
