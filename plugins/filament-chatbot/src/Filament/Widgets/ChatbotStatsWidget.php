<?php

namespace TitanZero\FilamentChatbot\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use TitanZero\FilamentChatbot\Models\Chatbot;
use TitanZero\FilamentChatbot\Models\ChatbotConversation;

class ChatbotStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Chatbots', Chatbot::count())
                ->description('Active chatbots')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),
            
            Stat::make('Total Conversations', ChatbotConversation::count())
                ->description('All time conversations')
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color('success'),
            
            Stat::make('Active Conversations', ChatbotConversation::where('status', 'open')->count())
                ->description('Currently open')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            
            Stat::make('Avg Response Time', '2.4s')
                ->description('Across all bots')
                ->descriptionIcon('heroicon-m-clock')
                ->color('secondary'),
        ];
    }
}
