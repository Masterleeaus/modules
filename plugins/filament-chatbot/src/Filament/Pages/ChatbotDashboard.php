<?php

namespace TitanZero\FilamentChatbot\Filament\Pages;

use Filament\Pages\Dashboard;
use Filament\Widgets\Widget;

class ChatbotDashboard extends Dashboard
{
    protected static ?string $title = 'Chatbot Analytics';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Chatbot';

    protected static ?int $navigationSort = 0;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('viewAny', \TitanZero\FilamentChatbot\Models\Chatbot::class) ?? false;
    }

    /**
     * @return array<class-string<Widget>|string>
     */
    public function getWidgets(): array
    {
        return [
            \TitanZero\FilamentChatbot\Filament\Widgets\ChatbotStatsWidget::class,
            \TitanZero\FilamentChatbot\Filament\Widgets\ConversationChartWidget::class,
        ];
    }
}
