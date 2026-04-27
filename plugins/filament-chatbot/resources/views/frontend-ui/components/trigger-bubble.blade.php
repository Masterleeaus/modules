<div
    class="lqd-ext-chatbot-trigger-bubble"
    :data-style="activeChatbot.bubble_design"
    :data-interactive="['suggestions', 'links', 'promo_banner'].includes(activeChatbot.bubble_design) ? 'true' : 'false'"
    x-show="activeChatbot.bubble_design && activeChatbot.bubble_design !== 'blank'"
>
    {{-- Close button for interactive designs --}}
    <template x-if="['modern', 'suggestions', 'links', 'promo_banner'].includes(activeChatbot.bubble_design)">
        <button
            class="absolute bottom-full right-0 z-[2] mb-2.5 inline-grid size-[34px] cursor-pointer place-items-center rounded-full border-none bg-white/95 text-black backdrop-blur-[10px] transition-all hover:scale-110 hover:bg-black hover:text-white"
            style="pointer-events: auto"
            type="button"
            aria-label="Close"
        >
            <svg
                width="8"
                height="8"
                viewBox="0 0 8 8"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path d="M0.8 8L0 7.2L3.2 4L0 0.8L0.8 0L4 3.2L7.2 0L8 0.8L4.8 4L8 7.2L7.2 8L4 4.8L0.8 8Z" />
            </svg>
        </button>
    </template>

    {{-- PLAIN design --}}
    <template x-if="activeChatbot.bubble_design === 'plain' && activeChatbot.bubble_message.trim()">
        <p
            class="relative z-[1] m-0 rounded-lg bg-black/[0.04] px-5 py-3.5 text-sm font-medium text-black"
            x-text="activeChatbot.bubble_message"
        ></p>
    </template>

    {{-- MODERN design --}}
    <template x-if="activeChatbot.bubble_design === 'modern' && activeChatbot.bubble_message.trim()">
        <div class="rounded-lg bg-white/95 p-2 shadow-[0_2px_8px_rgba(0,0,0,0.1)] backdrop-blur-[10px]">
            <p
                class="m-0 rounded-lg bg-black/[0.04] px-5 py-3.5 text-sm font-medium text-black"
                x-text="activeChatbot.bubble_message"
            ></p>
        </div>
    </template>

    {{-- SUGGESTIONS design --}}
    <template x-if="activeChatbot.bubble_design === 'suggestions'">
        <div>
            <template x-if="activeChatbot.bubble_message && activeChatbot.bubble_message.trim()">
                <div class="rounded-lg bg-white/95 p-2 shadow-[0_2px_8px_rgba(0,0,0,0.1)] backdrop-blur-[10px]">
                    <p
                        class="m-0 rounded-lg bg-black/[0.04] px-5 py-3.5 text-sm font-medium text-black"
                        x-text="activeChatbot.bubble_message"
                    ></p>
                </div>
            </template>
            <div class="mt-2.5 flex min-w-[285px] flex-wrap gap-2">
                <template
                    x-for="(prompt, i) in (activeChatbot.suggested_prompts || []).slice(0, 3)"
                    :key="'bubble-prompt-' + i"
                >
                    <button
                        class="lqd-ext-chatbot-suggestion-btn relative z-[1] cursor-pointer overflow-hidden rounded-full border-none bg-transparent px-5 py-3 text-sm font-medium text-[--lqd-ext-chat-primary] shadow-none outline-none backdrop-blur-[12px] transition-all before:absolute before:inset-0 before:-z-1 before:bg-[--lqd-ext-chat-primary] before:opacity-10 before:transition hover:text-[--lqd-ext-chat-primary-foreground] hover:before:opacity-100"
                        type="button"
                        x-text="prompt.name || prompt.prompt || '{{ __('Prompt') }}'"
                    ></button>
                </template>
            </div>
        </div>
    </template>

    {{-- LINKS design --}}
    <template x-if="activeChatbot.bubble_design === 'links' && (activeChatbot.whatsapp_link || activeChatbot.telegram_link)">
        <div class="rounded-lg bg-white/95 p-2 shadow-[0_2px_8px_rgba(0,0,0,0.1)] backdrop-blur-[10px]">
            <template x-if="activeChatbot.whatsapp_link">
                <a
                    class="flex w-[270px] items-center justify-between rounded-xl bg-[#F4F5F5] px-6 py-4 text-xs font-medium text-black no-underline transition-all hover:bg-black hover:text-white"
                    :href="activeChatbot.whatsapp_link"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    WhatsApp
                    <svg
                        width="17"
                        height="17"
                        viewBox="0 0 17 17"
                        fill="currentColor"
                        fill-opacity="0.75"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M8.4375 0C3.78506 0 0 3.78479 0 8.4375C0 10.0745 0.469391 11.6526 1.36038 13.0232L0.0455933 16.0911C-0.0450439 16.3021 0.00219727 16.5476 0.164795 16.7102C0.272461 16.8179 0.416382 16.875 0.5625 16.875C0.637207 16.875 0.712738 16.8602 0.784149 16.8294L3.85208 15.5143C5.22235 16.4059 6.80054 16.875 8.4375 16.875C13.0902 16.875 16.875 13.0902 16.875 8.4375C16.875 3.78479 13.0902 0 8.4375 0ZM12.7683 11.4576C12.7683 11.4576 12.0668 12.3574 11.5598 12.5678C10.2711 13.1012 8.45178 12.5678 6.37921 10.4958C4.30719 8.42322 3.77353 6.60388 4.30719 5.31519C4.51758 4.80762 5.41736 4.10669 5.41736 4.10669C5.66125 3.91663 6.04028 3.94025 6.25891 4.15887L7.27679 5.17676C7.49542 5.39539 7.49542 5.75354 7.27679 5.97217L6.63794 6.61047C6.63794 6.61047 6.37921 7.38721 7.93323 8.94177C9.48724 10.4958 10.2645 10.2371 10.2645 10.2371L10.9028 9.59821C11.1215 9.37958 11.4796 9.37958 11.6982 9.59821L12.7161 10.6161C12.9348 10.8347 12.9584 11.2132 12.7683 11.4576Z"
                        />
                    </svg>
                </a>
            </template>
            <template x-if="activeChatbot.telegram_link">
                <a
                    class="mt-2 flex w-[270px] items-center justify-between rounded-xl bg-[#F4F5F5] px-6 py-4 text-xs font-medium text-black no-underline transition-all hover:bg-black hover:text-white"
                    :href="activeChatbot.telegram_link"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Telegram
                    <svg
                        width="18"
                        height="15"
                        viewBox="0 0 18 15"
                        fill="currentColor"
                        fill-opacity="0.75"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M15.6496 0.124822C13.3937 1.05902 3.72105 5.06564 1.04841 6.1577C-0.744034 6.85718 0.305186 7.51292 0.305186 7.51292C0.305186 7.51292 1.83519 8.03744 3.14667 8.43092C4.45815 8.8244 5.15763 8.38718 5.15763 8.38718L11.3215 4.23404C13.5073 2.74778 12.9828 3.97178 12.4582 4.4963C11.3215 5.633 9.44181 7.42526 7.86807 8.86796C7.16859 9.47996 7.51833 10.0047 7.82433 10.2669C8.96103 11.2287 12.0648 13.1959 12.2397 13.3271C13.1633 13.9809 14.9799 14.9221 15.2562 12.9336L16.3491 6.07022C16.6989 3.75326 17.0486 1.61108 17.0923 0.999082C17.2234 -0.487178 15.6496 0.124822 15.6496 0.124822Z"
                        />
                    </svg>
                </a>
            </template>
        </div>
    </template>

    {{-- PROMO BANNER design --}}
    <template x-if="activeChatbot.bubble_design === 'promo_banner'">
        <div class="w-[min(280px,calc(100vw-(var(--lqd-ext-chat-offset-x)*2)))] overflow-hidden rounded-2xl bg-white shadow-[0_22px_44px_rgba(0,0,0,0.05)]">
            <template x-if="activeChatbot.promo_banner_image">
                <div class="h-[155px] w-full overflow-hidden rounded-t-xl">
                    <img
                        class="block size-full max-w-full object-cover"
                        :src="activeChatbot.promo_banner_image"
                        alt=""
                    />
                </div>
            </template>
            <div class="px-6 py-3">
                <template x-if="activeChatbot.promo_banner_title">
                    <h4
                        class="m-0 mb-2.5 text-[15px] font-semibold leading-[1.2] text-black"
                        x-text="activeChatbot.promo_banner_title"
                    ></h4>
                </template>
                <template x-if="activeChatbot.promo_banner_description">
                    <p
                        class="m-0 text-sm leading-[1.42] text-black/50"
                        x-text="activeChatbot.promo_banner_description"
                    ></p>
                </template>
            </div>
            <template x-if="activeChatbot.promo_banner_btn_label">
                <div class="border-t border-black/5 text-center">
                    <a
                        class="block px-3.5 py-[18px] text-sm font-medium underline underline-offset-4 transition-all hover:opacity-80"
                        :style="{ color: activeChatbot.color || '#763ed1', textDecorationThickness: '0.65px' }"
                        :href="activeChatbot.promo_banner_btn_link || '#'"
                        target="_blank"
                        rel="noopener noreferrer"
                        x-text="activeChatbot.promo_banner_btn_label"
                    ></a>
                </div>
            </template>
        </div>
    </template>
</div>
