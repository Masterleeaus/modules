<?php

namespace TitanZero\FilamentChatbot\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotResource;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotConversationResource;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotChannelResource;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotCustomerResource;
use TitanZero\FilamentChatbot\Filament\Pages\ChatbotDashboard;

class ChatbotPlugin implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ChatbotResource::class,
                ChatbotConversationResource::class,
                ChatbotChannelResource::class,
                ChatbotCustomerResource::class,
            ])
            ->pages([
                ChatbotDashboard::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
