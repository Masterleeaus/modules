<?php
namespace TitanZero\FilamentChatbot\Filament\Resources\ChatbotChannelResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotChannelResource;

class EditChatbotChannel extends EditRecord
{
    protected static string $resource = ChatbotChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
