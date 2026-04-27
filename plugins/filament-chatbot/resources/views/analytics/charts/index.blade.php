<div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
    @include('chatbot::analytics.charts.new-conversations', [
        'chartData' => $newConversationsData['chart_data'],
        'months' => $newConversationsData['months'],
    ])
    @include('chatbot::analytics.charts.agent-replies', [
        'chartData' => $agentRepliesData['chart_data'],
        'months' => $agentRepliesData['months'],
    ])
</div>
