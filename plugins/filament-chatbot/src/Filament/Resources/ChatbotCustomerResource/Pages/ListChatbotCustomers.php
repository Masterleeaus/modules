<?php
namespace TitanZero\FilamentChatbot\Filament\Resources\ChatbotCustomerResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotCustomerResource;

class ListChatbotCustomers extends ListRecords
{
    protected static string $resource = ChatbotCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
