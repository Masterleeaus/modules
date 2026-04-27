<?php

declare(strict_types=1);

namespace App\Extensions\Chatbot\System\Services;

use App\Extensions\Chatbot\System\Models\ChatbotConversation;
use App\Extensions\Chatbot\System\Models\ChatbotHistory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChatbotAnalyticsService
{
    /**
     * @param  array<int>  $chatbotIds
     *
     * @return array{total_conversations: int, avg_rating: float, avg_response_time: string, avg_conversation_duration: string}
     */
    public function getStatCards(array $chatbotIds): array
    {
        return [
            'total_conversations'       => $this->totalConversations($chatbotIds),
            'avg_rating'                => $this->averageRating($chatbotIds),
            'avg_response_time'         => $this->averageResponseTime($chatbotIds),
            'avg_conversation_duration' => $this->averageConversationDuration($chatbotIds),
        ];
    }

    public function totalConversations(array $chatbotIds): int
    {
        return ChatbotConversation::query()
            ->whereIn('chatbot_id', $chatbotIds)
            ->count();
    }

    /**
     * Average rating based on review_selected_response (numeric ratings stored as 1-5).
     */
    public function averageRating(array $chatbotIds): float
    {
        $avg = ChatbotConversation::query()
            ->whereIn('chatbot_id', $chatbotIds)
            ->whereNotNull('review_submitted_at')
            ->whereNotNull('review_selected_response')
            ->get()
            ->avg(fn ($conversation) => (float) $conversation->review_selected_response);

        return round((float) $avg, 2);
    }

    /**
     * Average time a human agent takes to first reply after connect_agent_at.
     *
     * After agent takeover, the first assistant message following connect_agent_at
     * is treated as the agent's first reply. Conversations with response times
     * exceeding 1 hour are excluded as stale/abandoned.
     */
    public function averageResponseTime(array $chatbotIds): string
    {
        $maxResponseSeconds = 3600; // 1-hour threshold

        $avgSeconds = ChatbotConversation::query()
            ->whereIn('chatbot_id', $chatbotIds)
            ->whereNotNull('connect_agent_at')
            ->get()
            ->map(function (ChatbotConversation $conversation) {
                $agentConnectedAt = $conversation->connect_agent_at;

                $firstAgentReply = $conversation->histories()
                    ->where('role', 'assistant')
                    ->where('created_at', '>', $agentConnectedAt)
                    ->orderBy('created_at')
                    ->value('created_at');

                if (! $firstAgentReply) {
                    return null;
                }

                return Carbon::parse($agentConnectedAt)->diffInSeconds(Carbon::parse($firstAgentReply));
            })
            ->filter(fn ($v) => $v !== null && $v <= $maxResponseSeconds)
            ->avg();

        return $this->formatDuration((int) round((float) $avgSeconds));
    }

    /**
     * Average active time users spent during each conversation.
     *
     * Uses a session-timeout approach: only gaps between consecutive messages
     * that are ≤ 5 minutes are counted as active conversation time. Longer
     * gaps (idle/abandoned sessions) are excluded from the duration.
     */
    public function averageConversationDuration(array $chatbotIds): string
    {
        $maxGapSeconds = 300; // 5-minute session timeout

        $avgSeconds = ChatbotConversation::query()
            ->whereIn('chatbot_id', $chatbotIds)
            ->whereHas('histories')
            ->with(['histories' => fn ($q) => $q->select('id', 'conversation_id', 'created_at')->orderBy('created_at')])
            ->get()
            ->map(function (ChatbotConversation $conversation) use ($maxGapSeconds) {
                $timestamps = $conversation->histories->pluck('created_at');

                if ($timestamps->count() < 2) {
                    return 0;
                }

                $duration = 0;

                for ($i = 1; $i < $timestamps->count(); $i++) {
                    $gap = Carbon::parse($timestamps[$i - 1])->diffInSeconds(Carbon::parse($timestamps[$i]));

                    if ($gap <= $maxGapSeconds) {
                        $duration += $gap;
                    }
                }

                return $duration;
            })
            ->avg();

        return $this->formatDuration((int) round((float) $avgSeconds));
    }

    /**
     * New conversations grouped by month, filterable by channel.
     *
     * @param  array<int>  $chatbotIds
     *
     * @return array<int, array{label: string, id: string, chart_series: array{name: string, data: array<int>}, today_count: int}>
     */
    public function getNewConversationsChartData(array $chatbotIds): array
    {
        $months = $this->buildMonthRange(6);
        $channels = $this->getActiveChannels($chatbotIds);

        $chartData = [];

        $allSeries = $this->buildConversationSeriesForChannel($chatbotIds, $months, null);
        $todayCount = ChatbotConversation::query()
            ->whereIn('chatbot_id', $chatbotIds)
            ->whereDate('created_at', today())
            ->count();

        $chartData[] = [
            'label'        => __('All'),
            'id'           => 'all',
            'chart_series' => [
                'name' => 'all',
                'data' => $allSeries,
            ],
            'today_count' => $todayCount,
        ];

        foreach ($channels as $channel) {
            $series = $this->buildConversationSeriesForChannel($chatbotIds, $months, $channel);
            $channelTodayCount = ChatbotConversation::query()
                ->whereIn('chatbot_id', $chatbotIds)
                ->where('chatbot_channel', $channel)
                ->whereDate('created_at', today())
                ->count();

            $chartData[] = [
                'label'        => __(ucfirst($channel)),
                'id'           => $channel,
                'chart_series' => [
                    'name' => $channel,
                    'data' => $series,
                ],
                'today_count' => $channelTodayCount,
            ];
        }

        return [
            'chart_data' => $chartData,
            'months'     => $months,
        ];
    }

    /**
     * Agent replies chart data (monthly).
     *
     * @param  array<int>  $chatbotIds
     *
     * @return array{chart_data: array<int, array{name: string, data: array<int>}>, months: array<Carbon>}
     */
    public function getAgentRepliesChartData(array $chatbotIds): array
    {
        $months = $this->buildMonthRange(6);

        $data = [];
        foreach ($months as $month) {
            $start = Carbon::parse($month)->startOfMonth();
            $end = Carbon::parse($month)->endOfMonth();

            $count = ChatbotHistory::query()
                ->whereIn('chatbot_id', $chatbotIds)
                ->whereNotNull('user_id')
                ->where('role', 'assistant')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $data[] = $count;
        }

        return [
            'chart_data' => [
                [
                    'name' => 'agent_replies',
                    'data' => $data,
                ],
            ],
            'months' => $months,
        ];
    }

    /**
     * Top 5 chatbot agents based on number of conversations.
     *
     * @param  array<int>  $chatbotIds
     *
     * @return Collection<int, array{name: string, avatar: string|null, conversations_count: int}>
     */
    public function getTopAgents(array $chatbotIds): Collection
    {
        return ChatbotConversation::query()
            ->whereIn('chatbot_id', $chatbotIds)
            ->select('chatbot_id', DB::raw('COUNT(*) as conversations_count'))
            ->groupBy('chatbot_id')
            ->orderByDesc('conversations_count')
            ->limit(5)
            ->with('chatbot:id,title,logo')
            ->get()
            ->map(function ($item) {
                return [
                    'name'                => $item->chatbot?->title ?? __('Unknown'),
                    'avatar'              => $item->chatbot?->logo,
                    'conversations_count' => (int) $item->conversations_count,
                ];
            });
    }

    /**
     * Top channels by number of conversations.
     *
     * @param  array<int>  $chatbotIds
     *
     * @return Collection<int, array{channel: string, conversations_count: int}>
     */
    public function getTopChannels(array $chatbotIds): Collection
    {
        return ChatbotConversation::query()
            ->whereIn('chatbot_id', $chatbotIds)
            ->whereNotNull('chatbot_channel')
            ->select('chatbot_channel', DB::raw('COUNT(*) as conversations_count'))
            ->groupBy('chatbot_channel')
            ->orderByDesc('conversations_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'channel'             => ucfirst($item->chatbot_channel ?? 'livechat'),
                    'conversations_count' => (int) $item->conversations_count,
                ];
            });
    }

    /**
     * @return array<Carbon>
     */
    private function buildMonthRange(int $months): array
    {
        return collect(range(0, $months - 1))
            ->map(fn ($i) => now()->copy()->subMonths($months - 1 - $i)->startOfMonth())
            ->all();
    }

    /**
     * @param  array<int>  $chatbotIds
     * @param  array<Carbon>  $months
     *
     * @return array<int>
     */
    private function buildConversationSeriesForChannel(array $chatbotIds, array $months, ?string $channel): array
    {
        $data = [];

        foreach ($months as $month) {
            $start = Carbon::parse($month)->startOfMonth();
            $end = Carbon::parse($month)->endOfMonth();

            $query = ChatbotConversation::query()
                ->whereIn('chatbot_id', $chatbotIds)
                ->whereBetween('created_at', [$start, $end]);

            if ($channel) {
                $query->where('chatbot_channel', $channel);
            }

            $data[] = $query->count();
        }

        return $data;
    }

    /**
     * @param  array<int>  $chatbotIds
     *
     * @return array<string>
     */
    private function getActiveChannels(array $chatbotIds): array
    {
        return ChatbotConversation::query()
            ->whereIn('chatbot_id', $chatbotIds)
            ->whereNotNull('chatbot_channel')
            ->distinct()
            ->pluck('chatbot_channel')
            ->toArray();
    }

    private function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . 's';
        }

        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 60) {
            return $remainingSeconds > 0
                ? $minutes . 'm ' . $remainingSeconds . 's'
                : $minutes . 'm';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return $hours . 'h ' . $remainingMinutes . 'm';
    }
}
