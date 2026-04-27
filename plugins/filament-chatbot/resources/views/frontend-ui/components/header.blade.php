<div
    class="lqd-ext-chatbot-header sticky top-0 z-2 bg-cover bg-center p-2"
    {{-- blade-formatter-disable --}}
	@if($is_editor)
	:style="{
		backgroundColor: activeChatbot.header_bg_type === 'color' ? activeChatbot.header_bg_color : '',
		backgroundImage: activeChatbot.header_bg_type === 'gradient' ? activeChatbot.header_bg_gradient : activeChatbot.header_bg_type === 'image' ? `url(${activeChatbot.header_bg_image})` : '',
	}"
	@else
	style="
		@if(isset($chatbot['header_bg_type']) && $chatbot['header_bg_type'] === 'color' && isset($chatbot['header_bg_color']))
			background-color: {{ $chatbot['header_bg_color'] }};
		@elseif(isset($chatbot['header_bg_type']) && $chatbot['header_bg_type'] === 'gradient' && isset($chatbot['header_bg_gradient']))
			background-image: {{ $chatbot['header_bg_gradient'] }};
		@elseif(isset($chatbot['header_bg_type']) && $chatbot['header_bg_type'] === 'image' && isset($chatbot['header_bg_image']))
			background-image: url({{ $chatbot['header_bg_image'] }});
		@endif
	"
	@endif
	{{-- blade-formatter-enable --}}
>
    <x-progressive-blur dir="reverse" />

    <div
        class="relative z-1 grid min-h-14 w-full grid-cols-1 place-items-center rounded-full shadow-2xl backdrop-blur-md transition-all"
        :class='{ "bg-red-500/80 text-white": widgetStatus.type === "error", "bg-green-500/80 text-white": widgetStatus.type === "success" }'
    >
        <div
            class="col-start-1 col-end-1 row-start-1 row-end-1 flex w-full justify-between gap-1 p-2"
            x-show="!widgetStatus.type && (!$store.voiceCall || $store.voiceCall.status === 'idle' || $store.voiceCall.status === 'ended')"
            x-transition
        >
            <div class="w-1/3">
                <button
                    class="inline-grid size-10 place-items-center rounded-full transition active:scale-[0.85]"
                    type="button"
                    title="{{ __('Back') }}"
                    @click.prevent="toggleView('<')"
                >
                    <x-tabler-chevron-left class="size-5" />
                </button>
            </div>

            <div class="grid w-1/3 place-items-center text-xs font-semibold">
                <div
                    class="col-start-1 col-end-1 row-start-1 row-end-1 flex w-full items-center justify-center gap-2 overflow-hidden"
                    x-show="currentView === 'conversation-messages'"
                    x-transition
                >
                    <figure class="size-[38px] shrink-0">
                        <img
                            class="size-full object-cover object-center"
                            {{-- blade-formatter-disable --}}
							@if ($is_editor)
								:src="() => activeChatbot.avatar ? `${window.location.origin}/${activeChatbot.avatar}` : ''"
							@else
								src="/{{ $chatbot['avatar'] }}"
								alt="{{ $chatbot['title'] }}"
							@endif
							{{-- blade-formatter-enable --}}
                            width="38"
                            height="38"
                        />
                    </figure>
                    <span
                        class="block max-w-full truncate"
                        @if ($is_editor) x-text="activeChatbot?.title" @endif
                        title="{{ isset($chatbot) ? $chatbot['title'] : '' }}"
                    >
                        @if (isset($chatbot))
                            {{ $chatbot['title'] }}
                        @endif
                    </span>
                </div>
                <p
                    class="col-start-1 col-end-1 row-start-1 row-end-1 m-0"
                    x-show="currentView !== 'conversation-messages'"
                    x-transition
                    x-text="getViewLabel"
                ></p>
            </div>

            <div class="flex w-1/3 min-w-10 items-center justify-end">
                @if ($is_editor || ($chatbot['voice_call_enabled'] ?? false))
                    @includeIf('chatbot-voice-call::particles.header-call-button')
                @endif

                @if ($is_editor || ($chatbot['is_shop'] ?? false))
                    @includeIf('chatbot-ecommerce::frontend-ui.components.cart')
                @endif
                <div
                    class="group relative"
                    x-show="currentView === 'conversation-messages'"
                    @click.outside="showOptionsDropdown = false"
                >
                    <button
                        class="inline-grid size-10 place-items-center rounded-full transition active:translate-y-1"
                        type="button"
                        @click.prevent="showOptionsDropdown = !showOptionsDropdown"
                    >
                        <x-tabler-dots
                            class="size-5 transition"
                            ::class="{ 'rotate-90': showOptionsDropdown }"
                        />
                    </button>
                    <div
                        class="invisible absolute end-0 top-full mt-6 max-h-[calc(100vh-13rem)] min-w-56 origin-top-right scale-95 overflow-y-auto rounded-lg bg-[--lqd-ext-chat-window-bg] px-4 py-3 opacity-0 shadow-[0_4px_33px_hsl(0_0%_0%/6%)] transition [&.active]:visible [&.active]:scale-100 [&.active]:opacity-100"
                        :class="{ 'active': showOptionsDropdown }"
                    >
                        <label
                            class="relative flex cursor-pointer items-center gap-1 py-3 text-xs font-medium"
                            for="chatbot-options-sound"
                        >
                            {{ __('Enable Sounds') }}
                            <input
                                class="peer absolute left-0 top-0 h-full w-full cursor-pointer opacity-0"
                                type="checkbox"
                                @change="toggleSound"
                                checked="soundEnabled"
                            />
                            <span
                                class="relative ms-auto inline-flex h-[18px] w-[34px] rounded-full bg-black/10 transition after:absolute after:start-1 after:top-1/2 after:size-2 after:-translate-y-1/2 after:rounded after:bg-[--lqd-ext-chat-window-bg] after:transition-all peer-checked:bg-[--lqd-ext-chat-primary] peer-checked:after:translate-x-[calc(34px-0.5rem-0.5rem)]"
                            ></span>
                        </label>
                        <hr class="m-0">
                        <button
                            class="group m-0 flex w-full items-center justify-between gap-2 py-3 text-start text-xs font-medium"
                            type="button"
                            @click.prevent="showExportOptions = !showExportOptions"
                            :class="{ 'active': showExportOptions }"
                        >
                            {{ __('Download Conversation') }}

                            <x-tabler-chevron-down class="size-4 transition group-[&.active]:rotate-180" />
                        </button>
                        <div
                            x-cloak
                            x-show="showExportOptions"
                        >
                            @php
                                $exportFormats = [
                                    ['format' => 'txt', 'label' => __('Download as Text'), 'icon' => 'tabler-file-type-txt'],
                                    ['format' => 'csv', 'label' => __('Download as CSV'), 'icon' => 'tabler-file-type-csv'],
                                    ['format' => 'pdf', 'label' => __('Download as PDF'), 'icon' => 'tabler-file-type-pdf'],
                                    ['format' => 'json', 'label' => __('Download as JSON'), 'icon' => 'tabler-json'],
                                ];
                            @endphp
                            @foreach ($exportFormats as $exportFormat)
                                <a
                                    class="flex items-center gap-2 py-2 text-xs font-medium hover:underline"
                                    @if (isset($chatbot)) :href="`/api/v2/chatbot/{{ $chatbot->getAttribute('uuid') }}/session/{{ $session }}/conversation/${activeConversation}/export?format={{ $exportFormat['format'] }}`"
								@else
								href="#" @click.prevent.stop @endif
                                >
                                    <x-dynamic-component
                                        class="size-4"
                                        :component="$exportFormat['icon']"
                                    />

                                    {{ $exportFormat['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="col-start-1 col-end-1 row-start-1 row-end-1 flex w-full justify-between gap-2 px-4 py-2 text-2xs leading-[1.25em]"
            x-show="widgetStatus.type"
            x-transition
        >
            <p
                class="m-0 w-full text-center"
                x-text="widgetStatus.message"
            ></p>
        </div>

        @if ($is_editor || ($chatbot['voice_call_enabled'] ?? false))
            @includeIf('chatbot-voice-call::particles.voice-call-active-header')
        @endif
    </div>
</div>
