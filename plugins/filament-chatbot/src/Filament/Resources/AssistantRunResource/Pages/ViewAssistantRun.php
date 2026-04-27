<?php

namespace TitanZero\FilamentChatbot\Filament\Resources\AssistantRunResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use TitanZero\FilamentChatbot\Filament\Resources\AssistantRunResource;

class ViewAssistantRun extends ViewRecord
{
    protected static string $resource = AssistantRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
