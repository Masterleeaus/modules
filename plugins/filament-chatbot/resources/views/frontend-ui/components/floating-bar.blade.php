<div
    class="lqd-ext-chatbot-floating-bar absolute inset-x-[18px] bottom-14 z-10 grid rounded-full border px-3 py-1 shadow-2xl transition-all before:pointer-events-none before:absolute before:inset-0 before:-z-1 before:rounded-[inherit] before:backdrop-blur-md after:pointer-events-none after:absolute after:-inset-px after:rounded-[inherit] after:border after:border-black/5 after:opacity-0 after:transition-opacity after:[mask-image:linear-gradient(to_bottom,black,transparent)] sm:px-4"
    :class="{
        'h-[74px] border-black/10': currentView !== 'conversation-messages',
        'border-transparent before:opacity-100': currentView === 'conversation-messages'
    }"
>
    <div
        @class([
            'col-start-1 col-end-1 row-start-1 row-end-1 flex w-full gap-2',
            'justify-between' => isset($chatbot) && $chatbot->is_articles,
            'justify-around' => isset($chatbot) && !$chatbot->is_articles,
        ])
        @if ($is_editor) :class="{'justify-between': activeChatbot.is_articles, 'justify-around': !activeChatbot.is_articles}" @endif
        x-show="currentView !== 'conversation-messages'"
        x-transition
    >
        <button
            class="group flex flex-col items-center justify-center gap-2 px-3 py-1 text-center text-[12px]/none font-medium transition-all sm:px-4 [&.active]:underline [&.active]:underline-offset-2"
            :class="{ 'active': currentView === 'welcome' }"
            type="button"
            @click.prevent="toggleView('welcome')"
        >
            <svg
                class="transition-transform group-hover:-translate-y-0.5"
                width="18"
                height="19"
                viewBox="0 0 18 19"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path
                    d="M12.97 11.9998C10.76 13.3328 7.17797 13.3328 4.97 11.9998M15.97 5.70977L10.637 1.56177C10.169 1.19768 9.59297 1 8.99997 1C8.40707 1 7.83097 1.19768 7.36297 1.56177L2.029 5.70977C1.70844 5.95906 1.44909 6.2783 1.27075 6.64312C1.09242 7.00796 0.99981 7.40866 1 7.81476V15.0148C1 15.5452 1.21071 16.054 1.58579 16.429C1.96086 16.8041 2.46957 17.0148 3 17.0148H15C15.5305 17.0148 16.0392 16.8041 16.4142 16.429C16.7893 16.054 17 15.5452 17 15.0148V7.81476C17 6.99177 16.62 6.21477 15.97 5.70977Z"
                />
            </svg>
            {{ __('Home') }}
        </button>

        <button
            class="group flex flex-col items-center justify-center gap-2 px-3 py-1 text-center text-[12px]/none font-medium transition-all sm:px-4 [&.active]:underline [&.active]:underline-offset-2"
            :class="{ 'active': currentView === 'conversations-list' }"
            type="button"
            @click.prevent="toggleView('conversations-list')"
        >
            <svg
                class="transition-transform group-hover:-translate-y-0.5"
                width="20"
                height="18"
                viewBox="0 0 20 18"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M13 7L9 11L15 17L19 1L1 8L5 10L7 16L10 12" />
            </svg>
            {{ __('Chat') }}
        </button>

        @if ($is_editor || (isset($chatbot) && $chatbot->is_articles))
            <button
                class="group flex flex-col items-center justify-center gap-2 px-3 py-1 text-center text-[12px]/none font-medium transition-all sm:px-4 [&.active]:underline [&.active]:underline-offset-2"
                @if ($is_editor) x-show="activeChatbot?.is_articles" @endif
                :class="{ 'active': currentView === 'articles-list' }"
                type="button"
                @click.prevent="toggleView('articles-list')"
            >
                <svg
                    class="transition-transform group-hover:-translate-y-0.5"
                    width="20"
                    height="20"
                    viewBox="0 0 20 20"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path
                        d="M10 15V15.01M10 11.5C9.98159 11.1754 10.0692 10.8536 10.2495 10.583C10.4299 10.3125 10.6933 10.1079 11 10C11.3759 9.85626 11.7132 9.62724 11.9856 9.33095C12.2579 9.03467 12.4577 8.67922 12.5693 8.29259C12.6809 7.90595 12.7013 7.49869 12.6287 7.10287C12.5562 6.70704 12.3928 6.33345 12.1513 6.01151C11.9099 5.68958 11.597 5.42808 11.2373 5.24762C10.8776 5.06715 10.4809 4.97264 10.0785 4.97152C9.67611 4.97041 9.27892 5.06272 8.91824 5.24119C8.55756 5.41965 8.24323 5.67941 8 6M1 10C1 11.1819 1.23279 12.3522 1.68508 13.4442C2.13738 14.5361 2.80031 15.5282 3.63604 16.364C4.47177 17.1997 5.46392 17.8626 6.55585 18.3149C7.64778 18.7672 8.8181 19 10 19C11.1819 19 12.3522 18.7672 13.4442 18.3149C14.5361 17.8626 15.5282 17.1997 16.364 16.364C17.1997 15.5282 17.8626 14.5361 18.3149 13.4442C18.7672 12.3522 19 11.1819 19 10C19 8.8181 18.7672 7.64778 18.3149 6.55585C17.8626 5.46392 17.1997 4.47177 16.364 3.63604C15.5282 2.80031 14.5361 2.13738 13.4442 1.68508C12.3522 1.23279 11.1819 1 10 1C8.8181 1 7.64778 1.23279 6.55585 1.68508C5.46392 2.13738 4.47177 2.80031 3.63604 3.63604C2.80031 4.47177 2.13738 5.46392 1.68508 6.55585C1.23279 7.64778 1 8.8181 1 10Z"
                    />
                </svg>
                {{ __('Help') }}
            </button>
        @endif
    </div>

    <div
        class="pointer-events-none absolute bottom-full end-0 start-[15%] mb-2 flex flex-row-reverse flex-wrap gap-2"
        x-cloak
        x-show="currentView === 'conversation-messages' && suggestedPrompts.length && !messages.find(m => m.role === 'user')"
        x-transition
        x-ref="suggestedPromptsContainer"
    >
        <template
            x-for="(prompt, index) in suggestedPrompts"
            :key="'floating-bar-suggested-prompt-' + index"
        >
            <button
                class="pointer-events-auto relative z-1 overflow-hidden rounded-xl px-5 py-3 text-xs text-[--lqd-ext-chat-primary] backdrop-blur-lg transition before:absolute before:inset-0 before:-z-1 before:bg-[--lqd-ext-chat-primary] before:opacity-10 hover:bg-[--lqd-ext-chat-primary] hover:text-[--lqd-ext-chat-primary-foreground]"
                type="button"
                @click="applySuggestedPrompt(prompt)"
                x-text="getSuggestedPromptLabel(prompt, index)"
            >
            </button>
        </template>
    </div>

    <div
        class="col-start-1 col-end-1 row-start-1 row-end-1 flex"
        x-cloak
        x-show="currentView === 'conversation-messages'"
        x-transition
    >
        @include('chatbot::frontend-ui.components.conversation-form')
    </div>
</div>
