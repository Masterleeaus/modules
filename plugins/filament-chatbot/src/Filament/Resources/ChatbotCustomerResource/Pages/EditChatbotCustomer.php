<?php
namespace TitanZero\FilamentChatbot\Filament\Resources\ChatbotCustomerResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotCustomerResource;

class EditChatbotCustomer extends EditRecord
{
    protected static string $resource = ChatbotCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
