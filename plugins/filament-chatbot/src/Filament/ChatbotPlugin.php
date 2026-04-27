<?php

namespace TitanZero\FilamentChatbot\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotResource;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotConversationResource;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotChannelResource;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotCustomerResource;
use TitanZero\FilamentChatbot\Filament\Pages\ChatbotDashboard;

class ChatbotPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-chatbot';
    }

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

        // Inject the AI assistant sidebar into every panel page
        if (config('assistant.sidebar.enabled', true)) {
            $panel->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render("@auth\n    @livewire('chatbot-assistant-sidebar')\n@endauth"),
            );
        }
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
