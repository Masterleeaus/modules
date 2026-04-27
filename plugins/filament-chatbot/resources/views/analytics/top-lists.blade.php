<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    {{-- Top Agents --}}
    <x-card class:body="px-6 pb-6">
        <x-slot:head
            class="flex items-center justify-between border-0 px-6 pt-6"
        >
            <h4 class="m-0 text-sm font-medium">
                @lang('Top Agents')
            </h4>
        </x-slot:head>

        <div class="flex flex-col gap-3">
            @forelse ($topAgents as $agent)
                <div class="flex items-center gap-3">
                    <div class="relative h-8 grow overflow-hidden rounded-lg bg-primary/10">
                        @php
                            $maxConvos = $topAgents->max('conversations_count') ?: 1;
                            $widthPercent = ($agent['conversations_count'] / $maxConvos) * 100;
                        @endphp
                        <div
                            class="absolute inset-y-0 start-0 rounded-lg bg-primary/20"
                            style="width: {{ $widthPercent }}%"
                        ></div>
                        <span class="relative z-1 flex h-full items-center px-3 text-xs font-semibold text-heading-foreground">
                            {{ $agent['name'] }}
                        </span>
                    </div>
                    <span class="min-w-[3rem] text-end text-xs font-semibold text-heading-foreground">
                        {{ number_format($agent['conversations_count']) }}
                    </span>
                </div>
            @empty
                <p class="py-4 text-center text-xs text-foreground/60">
                    @lang('No agent data yet.')
                </p>
            @endforelse
        </div>
    </x-card>

    {{-- Top Channels --}}
    <x-card class:body="px-6 pb-6">
        <x-slot:head
            class="flex items-center justify-between border-0 px-6 pt-6"
        >
            <h4 class="m-0 text-sm font-medium">
                @lang('Top Channels')
            </h4>
        </x-slot:head>

        <div class="flex flex-col gap-3">
            @forelse ($topChannels as $channel)
                <div class="flex items-center gap-3">
                    <div class="relative h-8 grow overflow-hidden rounded-lg bg-primary/10">
                        @php
                            $maxConversations = $topChannels->max('conversations_count') ?: 1;
                            $widthPercent = ($channel['conversations_count'] / $maxConversations) * 100;
                        @endphp
                        <div
                            class="absolute inset-y-0 start-0 rounded-lg bg-primary/20"
                            style="width: {{ $widthPercent }}%"
                        ></div>
                        <span class="relative z-1 flex h-full items-center px-3 text-xs font-semibold text-heading-foreground">
                            {{ $channel['channel'] }}
                        </span>
                    </div>
                    <span class="min-w-[3rem] text-end text-xs font-semibold text-heading-foreground">
                        {{ number_format($channel['conversations_count']) }}
                    </span>
                </div>
            @empty
                <p class="py-4 text-center text-xs text-foreground/60">
                    @lang('No channel data yet.')
                </p>
            @endforelse
        </div>
    </x-card>
</div>
