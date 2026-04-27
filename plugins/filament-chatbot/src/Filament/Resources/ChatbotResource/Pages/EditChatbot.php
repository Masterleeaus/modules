<?php

namespace TitanZero\FilamentChatbot\Filament\Resources\ChatbotResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotResource;

class EditChatbot extends EditRecord
{
    protected static string $resource = ChatbotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
