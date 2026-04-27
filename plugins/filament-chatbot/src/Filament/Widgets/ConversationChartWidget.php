<?php

namespace TitanZero\FilamentChatbot\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ConversationChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Conversations (Last 7 Days)';

    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $data = DB::table('chatbot_conversations')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $dates = collect(range(0, 6))
            ->map(fn ($i) => now()->subDays(6 - $i)->format('Y-m-d'))
            ->toArray();

        $counts = collect($dates)
            ->map(fn ($date) => $data->get($date) ?? 0)
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Conversations',
                    'data' => $counts,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => collect($dates)
                ->map(fn ($date) => date('M d', strtotime($date)))
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
