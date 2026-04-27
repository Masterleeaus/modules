<x-forms.input
    class="h-11 shrink-0 rounded-none !border-x-0 !border-t-0 bg-transparent px-4 !text-[11px] font-semibold uppercase tracking-wide text-foreground/70 focus:border-input-border focus:ring-0 xl:px-6"
    name="chatbot_channel"
    size="lg"
    type="select"
    x-model="filters.channel"
    @change.prevent="fetchChats"
>
    @foreach ($channel_filters as $key => $filter)
        <option
            class="bg-background text-foreground"
            value="{{ $key }}"
        >
            {{ $filter['label'] }}
        </option>
    @endforeach
</x-forms.input>

{{-- Conversation Agent Filter --}}

<x-forms.input
    class="h-11 shrink-0 rounded-none !border-x-0 bg-transparent px-4 !text-[11px] font-semibold uppercase tracking-wide text-foreground/70 focus:border-input-border focus:ring-0 xl:px-6"
    name="chatbot_agent"
    size="lg"
    type="select"
    x-model="filters.chatbot"
    @change.prevent="filterChatbot($event.target.value)"
>
    @foreach ($conversation_agent_filters as $key => $filter)
        <option
            class="bg-background text-foreground"
            value="{{ $key }}"
        >
            {{ $filter['label'] }}
        </option>
    @endforeach
</x-forms.input>
