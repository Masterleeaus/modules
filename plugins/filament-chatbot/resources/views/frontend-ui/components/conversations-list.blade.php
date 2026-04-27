<div class="w-full">
    <div
        class="mb-4 flex flex-col items-center justify-center gap-4 text-center"
        x-show="!fetching && !conversations.length"
        x-cloak
    >
        <svg
            width="48"
            height="49"
            viewBox="0 0 48 49"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M48.0008 20C48.0008 9.26 37.2308 0.5 24.0008 0.5C18.8108 0.5 13.9808 1.85 10.0508 4.19L40.2008 34.34C45.0008 30.8 48.0008 25.67 48.0008 20Z"
                fill="black"
                fill-opacity="0.2"
            />
            <path
                d="M2.562 0.938018C1.977 0.353018 1.026 0.353018 0.441 0.938018C-0.144 1.52302 -0.144 2.47402 0.441 3.05902L5.25 7.87102C1.971 11.192 0 15.419 0 20C0 25.91 3.36 31.52 9 35.21V44C9 45.062 10.029 45.833 11.16 45.35L22.92 39.47C23.28 39.5 23.64 39.5 24 39.5C27.873 39.5 31.515 38.72 34.749 37.37L45.441 48.062C46.026 48.647 46.977 48.647 47.562 48.062C48.147 47.477 48.147 46.526 47.562 45.941L2.562 0.938018Z"
                fill="black"
                fill-opacity="0.2"
            />
        </svg>

        <h4 class="lqd-ext-chatbot-window-conversations-list-no-conversations mb-0 text-sm font-semibold">
            {{ __('No conversations yet.') }}
        </h4>
    </div>

    @if (!isset($recents))
        <a
            class="flex items-center justify-center gap-3 px-4 pb-4 text-center text-xs text-[--lqd-ext-chat-primary] underline underline-offset-4"
            href="#"
            @click.prevent="startNewConversation"
        >
            {{-- blade-formatter-disable --}}
		<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg" > <path d="M12.75 5C12.125 5 11.5938 4.78125 11.1562 4.34375C10.7188 3.90625 10.5 3.375 10.5 2.75C10.5 2.125 10.7188 1.59375 11.1562 1.15625C11.5938 0.71875 12.125 0.5 12.75 0.5C13.375 0.5 13.9062 0.71875 14.3438 1.15625C14.7812 1.59375 15 2.125 15 2.75C15 3.375 14.7812 3.90625 14.3438 4.34375C13.9062 4.78125 13.375 5 12.75 5ZM0 17V3.5C0 3.0875 0.146875 2.73438 0.440625 2.44063C0.734375 2.14688 1.0875 2 1.5 2H9.075C9.025 2.25 9 2.5 9 2.75C9 3 9.025 3.25 9.075 3.5C9.25 4.375 9.68125 5.09375 10.3687 5.65625C11.0562 6.21875 11.85 6.5 12.75 6.5C13.15 6.5 13.5438 6.4375 13.9313 6.3125C14.3188 6.1875 14.675 6 15 5.75V12.5C15 12.9125 14.8531 13.2656 14.5594 13.5594C14.2656 13.8531 13.9125 14 13.5 14H3L0 17Z" /> </svg>
		{{-- blade-formatter-enable --}}
            {{ __('Send new message') }}
        </a>
    @endif

    <template @if (!isset($take)) x-for="conversation in conversations"@else x-for="conversation in conversations.slice(0, {{ $take }})" @endif>
        <div
            class="lqd-ext-chatbot-window-conversations-list-item group relative flex gap-4 border-b border-black/5 py-4 transition-colors hover:border-transparent"
            :data-id="conversation.id"
            :key="conversation.id"
        >
            <figure class="lqd-ext-chatbot-window-conversations-list-item-fig relative z-1 shrink-0 place-self-center">
                <img
                    class="size-8 shrink-0 rounded-full object-cover object-center"
                    {{-- blade-formatter-disable --}}
						@if ($is_editor)
						:src="() => activeChatbot.avatar ? `${window.location.origin}/${activeChatbot.avatar}` : ''"
					@else
						src="/{{ $chatbot['avatar'] }}"
						alt="{{ $chatbot['title'] }}"
					@endif
					{{-- blade-formatter-enable --}}
                    width="27"
                    height="27"
                >
            </figure>
            <div class="lqd-ext-chatbot-window-conversations-list-item-info relative z-1 text-2xs">
                <p
                    class="lqd-ext-chatbot-window-conversations-list-item-info-name m-0 text-black"
                    x-text="activeChatbot.title"
                ></p>
                <p
                    class="lqd-ext-chatbot-window-conversations-list-item-info-last-message m-0 line-clamp-1 w-full overflow-hidden text-ellipsis opacity-70"
                    x-text="conversation.last_message"
                ></p>
            </div>
            <div
                class="lqd-ext-chatbot-window-conversations-list-item-time relative z-1 ms-auto flex flex-col items-end gap-0.5 whitespace-nowrap text-[12px]"
                x-data="{ diff: Math.floor((new Date() - new Date(conversation.updated_at || conversation.created_at)) / 1000) }"
            >
                <span
                    x-text="
					diff < 60 ? '{{ __('just now') }}' :
					diff < 3600 ? Math.floor(diff / 60) + '{{ __('m ago') }}' :
					diff < 86400 ? Math.floor(diff / 3600) + '{{ __('h ago') }}' :
					Math.floor(diff / 86400) + '{{ __('d ago') }}'
				"
                ></span>
                <span
                    class="inline-flex size-1.5 rounded-full bg-red-500"
                    x-cloak
                    x-show="conversation.unread_count > 0"
                ></span>
            </div>
            <a
                class="absolute -inset-x-2 -bottom-px top-0 z-1 scale-95 rounded-xl transition-all hover:bg-black/5 group-hover:scale-100"
                href="#"
                @click.prevent="openConversation(conversation.id)"
            ></a>
        </div>
    </template>

    @if (isset($recents))
        <a
            class="flex items-center justify-center gap-3 px-4 pb-0.5 pt-4 text-center text-xs text-[--lqd-ext-chat-primary] underline underline-offset-4"
            href="#"
            @click.prevent="startNewConversation"
        >
            {{-- blade-formatter-disable --}}
		<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg" > <path d="M12.75 5C12.125 5 11.5938 4.78125 11.1562 4.34375C10.7188 3.90625 10.5 3.375 10.5 2.75C10.5 2.125 10.7188 1.59375 11.1562 1.15625C11.5938 0.71875 12.125 0.5 12.75 0.5C13.375 0.5 13.9062 0.71875 14.3438 1.15625C14.7812 1.59375 15 2.125 15 2.75C15 3.375 14.7812 3.90625 14.3438 4.34375C13.9062 4.78125 13.375 5 12.75 5ZM0 17V3.5C0 3.0875 0.146875 2.73438 0.440625 2.44063C0.734375 2.14688 1.0875 2 1.5 2H9.075C9.025 2.25 9 2.5 9 2.75C9 3 9.025 3.25 9.075 3.5C9.25 4.375 9.68125 5.09375 10.3687 5.65625C11.0562 6.21875 11.85 6.5 12.75 6.5C13.15 6.5 13.5438 6.4375 13.9313 6.3125C14.3188 6.1875 14.675 6 15 5.75V12.5C15 12.9125 14.8531 13.2656 14.5594 13.5594C14.2656 13.8531 13.9125 14 13.5 14H3L0 17Z" /> </svg>
		{{-- blade-formatter-enable --}}
            {{ __('Start a new chat') }}
        </a>
    @endif
</div>
