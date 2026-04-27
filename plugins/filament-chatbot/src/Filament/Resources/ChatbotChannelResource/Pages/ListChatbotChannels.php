<?php
namespace TitanZero\FilamentChatbot\Filament\Resources\ChatbotChannelResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotChannelResource;

class ListChatbotChannels extends ListRecords
{
    protected static string $resource = ChatbotChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
