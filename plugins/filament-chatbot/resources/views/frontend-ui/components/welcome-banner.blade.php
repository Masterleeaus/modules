<div class="lqd-ext-chatbot-window-welcome-banner">
    <div class="mb-28 flex items-center gap-2.5 text-white">
        @if ($is_editor)
            <img
                class="lqd-ext-chatbot-window-head-logo"
                :src="activeChatbot.logo"
                x-show="activeChatbot.show_logo && activeChatbot.logo"
                width="25"
                height="25"
            />
            <svg
                class="lqd-ext-chatbot-window-head-logo"
                width="25"
                height="25"
                viewBox="0 0 25 25"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
                x-show="activeChatbot.show_logo && !activeChatbot.logo"
            >
                <path
                    d="M18.2404 21.8333L14.1279 17.6917L15.7612 16.0583L18.2404 18.5375L23.1987 13.5792L24.832 15.2417L18.2404 21.8333ZM0.332031 24.1667V3.16668C0.332031 2.52501 0.560503 1.9757 1.01745 1.51876C1.47439 1.06182 2.0237 0.833344 2.66536 0.833344H21.332C21.9737 0.833344 22.523 1.06182 22.9799 1.51876C23.4369 1.9757 23.6654 2.52501 23.6654 3.16668V11.3333H11.9987V19.5H4.9987L0.332031 24.1667Z"
                />
            </svg>
            <h4
                class="lqd-ext-chatbot-window-head-title text-current"
                x-text="activeChatbot.title"
            ></h4>
        @else
            @if ($chatbot['logo'] && $chatbot['show_logo'])
                <img
                    class="lqd-ext-chatbot-window-head-logo"
                    alt="{{ $chatbot['title'] }}"
                    src="{{ $chatbot['logo'] }}"
                    width="25"
                    height="25"
                />
            @endif
            @if (!$chatbot['logo'] && $chatbot['show_logo'])
                <svg
                    class="lqd-ext-chatbot-window-head-logo"
                    width="25"
                    height="25"
                    viewBox="0 0 25 25"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M18.2404 21.8333L14.1279 17.6917L15.7612 16.0583L18.2404 18.5375L23.1987 13.5792L24.832 15.2417L18.2404 21.8333ZM0.332031 24.1667V3.16668C0.332031 2.52501 0.560503 1.9757 1.01745 1.51876C1.47439 1.06182 2.0237 0.833344 2.66536 0.833344H21.332C21.9737 0.833344 22.523 1.06182 22.9799 1.51876C23.4369 1.9757 23.6654 2.52501 23.6654 3.16668V11.3333H11.9987V19.5H4.9987L0.332031 24.1667Z"
                    />
                </svg>
            @endif
            <h4 class="lqd-ext-chatbot-window-head-title">
                {{ $chatbot['title'] }}
            </h4>
        @endif

    </div>

    <p class="mb-4 text-[27px]/none font-bold -tracking-wide text-white">
        <span class="block opacity-70">{{ __('Hi there 👋🏼') }}</span>
        {{ __('How can we help you?') }}
    </p>

    <button
        class="lqd-ext-chatbot-window-welcome-button relative flex w-full items-center gap-3.5 rounded-full bg-white/25 p-4 text-start text-white shadow-2xl backdrop-blur-md transition-all before:pointer-events-none before:absolute before:-inset-px before:z-0 before:rounded-full before:border before:border-white before:opacity-35 before:[mask-image:linear-gradient(to_bottom,black,transparent)] hover:-translate-y-1 hover:bg-white hover:text-black hover:shadow-white/20 sm:px-[22px] sm:py-[18px]"
        @click.prevent="startNewConversation"
    >
        <img
            class="size-[38px] shrink-0 rounded-full object-cover object-center"
            {{-- blade-formatter-disable --}}
				@if ($is_editor)
				:src="() => activeChatbot.avatar ? `${window.location.origin}/${activeChatbot.avatar}` : ''"
			@else
				src="/{{ $chatbot['avatar'] }}"
				alt="{{ $chatbot['title'] }}"
			@endif
			{{-- blade-formatter-enable --}}
        >
        <span class="text-xs/5 font-medium">
            {{ __('Ask me anything.') }}
            <span class="block opacity-50">
                {{ __('We usually reply in a few hours.') }}
            </span>
        </span>

        <svg
            class="ms-auto"
            width="18"
            height="15"
            viewBox="0 0 18 15"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M16.7559 6.29427L16.7503 6.2918L1.73575 0.0642261C1.60947 0.0113681 1.47205 -0.00936218 1.33578 0.00388969C1.19952 0.0171416 1.06867 0.0639623 0.954934 0.140164C0.834768 0.218903 0.736063 0.326281 0.667699 0.452638C0.599335 0.578995 0.563457 0.720366 0.563294 0.864031V4.84688C0.56336 5.04328 0.631939 5.23351 0.75721 5.38477C0.882481 5.53603 1.0566 5.63885 1.24954 5.67552L9.43849 7.1897C9.47067 7.1958 9.49971 7.21294 9.5206 7.23816C9.54149 7.26338 9.55292 7.29511 9.55292 7.32786C9.55292 7.36061 9.54149 7.39234 9.5206 7.41756C9.49971 7.44278 9.47067 7.45992 9.43849 7.46602L1.2499 8.9802C1.057 9.01677 0.882896 9.11946 0.75757 9.27058C0.632243 9.42171 0.56354 9.6118 0.563294 9.80813V13.7917C0.5632 13.9289 0.597168 14.0639 0.662149 14.1848C0.727129 14.3056 0.82109 14.4084 0.935598 14.4839C1.07334 14.5754 1.23499 14.6243 1.40036 14.6245C1.51533 14.6244 1.62912 14.6014 1.73505 14.5567L16.7492 8.36462L16.7559 8.36145C16.958 8.2746 17.1302 8.1304 17.2512 7.94669C17.3722 7.76299 17.4367 7.54784 17.4367 7.32786C17.4367 7.10788 17.3722 6.89273 17.2512 6.70903C17.1302 6.52532 16.958 6.38112 16.7559 6.29427Z"
            />
        </svg>
    </button>
</div>
