<div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    {{-- Total Conversations --}}
    <x-card class:body="px-6">
        <x-slot:head
            class="flex items-center justify-between border-0 px-6 pb-0 pt-6"
        >
            <h4 class="m-0 text-sm font-medium">
                @lang('Total Conversations')
            </h4>
            <x-info-tooltip text="{{ __('Total number of conversations across all chatbots.') }}" />
        </x-slot:head>

        <p class="flex items-center text-[24px] font-medium text-heading-foreground">
            <x-number-counter
                id="total-conversations-counter"
                value="{{ number_format($stats['total_conversations']) }}"
            />
        </p>
    </x-card>

    {{-- Avg. Rating --}}
    {{-- <x-card class:body="px-6">
        <x-slot:head
            class="flex items-center justify-between border-0 px-6 pb-0 pt-6"
        >
            <h4 class="m-0 text-sm font-medium">
                @lang('Avg. Rating')
            </h4>
            <x-info-tooltip text="{{ __('Average rating based on user feedback.') }}" />
        </x-slot:head>

        <p class="flex items-center text-[24px] font-medium text-heading-foreground">
            <x-number-counter
                id="avg-rating-counter"
                value="{{ $stats['avg_rating'] ?: '0' }}"
                :options="['delay' => 100]"
            />
        </p>
    </x-card> --}}

    {{-- Avg. Response Time --}}
    <x-card class:body="px-6">
        <x-slot:head
            class="flex items-center justify-between border-0 px-6 pb-0 pt-6"
        >
            <h4 class="m-0 text-sm font-medium">
                @lang('Avg. Response Time')
            </h4>
            <x-info-tooltip text="{{ __('Average time for a human agent to first reply.') }}" />
        </x-slot:head>

        <p class="flex items-center text-[24px] font-medium text-heading-foreground">
            {{ $stats['avg_response_time'] ?: '0s' }}
        </p>
    </x-card>

    {{-- Avg. Conversation Duration --}}
    <x-card class:body="px-6">
        <x-slot:head
            class="flex items-center justify-between border-0 px-6 pb-0 pt-6"
        >
            <h4 class="m-0 text-sm font-medium">
                @lang('Avg. Conversation Duration')
            </h4>
            <x-info-tooltip text="{{ __('Average time users spent during each conversation.') }}" />
        </x-slot:head>

        <p class="flex items-center text-[24px] font-medium text-heading-foreground">
            {{ $stats['avg_conversation_duration'] ?: '0s' }}
        </p>
    </x-card>
</div>
