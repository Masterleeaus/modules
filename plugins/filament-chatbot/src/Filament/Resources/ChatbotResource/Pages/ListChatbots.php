<?php

namespace TitanZero\FilamentChatbot\Filament\Resources\ChatbotResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotResource;

class ListChatbots extends ListRecords
{
    protected static string $resource = ChatbotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
