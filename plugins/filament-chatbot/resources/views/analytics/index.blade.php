@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Analytics'))
@section('titlebar_subtitle', __('View detailed conversation analytics across agents and platforms.'))
@section('titlebar_actions')
@endsection

@push('script')
    <script src="/themes/default/assets/libs/apexcharts/dist/apexcharts.min.js"></script>
@endpush

@section('content')
    <div class="py-10">
        @include('chatbot::analytics.stats-cards', ['stats' => $stats])
        @include('chatbot::analytics.charts.index', [
            'newConversationsData' => $newConversationsData,
            'agentRepliesData' => $agentRepliesData,
        ])
        @include('chatbot::analytics.top-lists', [
            'topAgents' => $topAgents,
            'topChannels' => $topChannels,
        ])
    </div>
@endsection
