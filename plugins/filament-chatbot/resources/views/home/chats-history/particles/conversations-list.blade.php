<div class="h-full overflow-y-auto">
    <ul class="lqd-ext-chatbot-history-list">
        <template
            x-for="(chatItem, index) in chatsList"
            :key="chatItem.id"
        >
            <li
                class="lqd-ext-chatbot-history-list-item group/chat-item relative border-b px-6 py-4 before:absolute before:inset-x-2.5 before:inset-y-1.5 before:z-0 before:scale-95 before:rounded-xl before:bg-accent/10 before:opacity-0 before:transition [&.active]:before:scale-100 [&.active]:before:opacity-100"
                :class="{ 'active': activeChat ? activeChat.id == chatItem.id : index === 0 }"
            >
                <div class="relative z-1 flex gap-2.5">
                    <figure class="inline-grid size-8 shrink-0 place-items-center rounded-full font-heading text-xs font-semibold uppercase text-white">
                        <template x-if="chatItem?.chatbot_channel === 'frame'">
                            <svg
                                class="col-start-1 col-end-1 row-start-1 row-end-1"
                                width="32"
                                height="31"
                                viewBox="0 0 32 31"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M16 0C7.44048 0 0.5 6.93943 0.5 15.5C0.5 24.0606 7.4398 31 16 31C24.5609 31 31.5 24.0606 31.5 15.5C31.5 6.93943 24.5609 0 16 0ZM16 4.63468C18.8323 4.63468 21.1274 6.93057 21.1274 9.76163C21.1274 12.5934 18.8323 14.8886 16 14.8886C13.1691 14.8886 10.874 12.5934 10.874 9.76163C10.874 6.93057 13.1691 4.63468 16 4.63468ZM15.9966 26.9475C13.1718 26.9475 10.5846 25.9187 8.58906 24.2158C8.10294 23.8012 7.82243 23.1931 7.82243 22.5552C7.82243 19.6839 10.1461 17.386 13.0179 17.386H18.9834C21.8559 17.386 24.1708 19.6839 24.1708 22.5552C24.1708 23.1938 23.8916 23.8005 23.4048 24.2151C21.41 25.9187 18.8221 26.9475 15.9966 26.9475Z"
                                    :fill="chatItem.color ?? '#e633ec'"
                                />
                            </svg>
                        </template>

                        <template x-if="chatItem?.chatbot_channel === 'whatsapp'">
                            <svg
                                width="31"
                                height="31"
                                viewBox="0 0 31 31"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M31 15.5C31 6.93959 24.0604 0 15.5 0C6.93959 0 0 6.93959 0 15.5C0 24.0604 6.93959 31 15.5 31C24.0604 31 31 24.0604 31 15.5Z"
                                    fill="#25D366"
                                />
                                <path
                                    d="M21.2891 9.63672C22.8477 11.1953 23.8125 13.2363 23.8125 15.4629C23.8125 19.9902 20.0273 23.7012 15.4629 23.7012C14.0898 23.7012 12.7539 23.3301 11.5293 22.6992L7.1875 23.8125L8.33789 19.5449C7.63281 18.3203 7.22461 16.9102 7.22461 15.4258C7.22461 10.8984 10.9355 7.1875 15.4629 7.1875C17.6895 7.1875 19.7676 8.07812 21.2891 9.63672ZM15.4629 22.291C19.248 22.291 22.4023 19.2109 22.4023 15.4629C22.4023 13.6074 21.623 11.9004 20.3242 10.6016C19.0254 9.30273 17.3184 8.59766 15.5 8.59766C11.7148 8.59766 8.63477 11.6777 8.63477 15.4258C8.63477 16.7246 9.00586 17.9863 9.6738 19.0996L9.8594 19.3594L9.1543 21.8828L11.752 21.1777L11.9746 21.3262C13.0508 21.957 14.2383 22.291 15.4629 22.291ZM19.248 17.1699C19.4336 17.2812 19.582 17.3184 19.6191 17.4297C19.6934 17.5039 19.6934 17.9121 19.5078 18.3945C19.3223 18.877 18.5059 19.3223 18.1348 19.3594C17.4668 19.4707 16.9473 19.4336 15.6484 18.8398C13.5703 17.9492 12.2344 15.8711 12.123 15.7598C12.0117 15.6113 11.3066 14.6465 11.3066 13.6074C11.3066 12.6055 11.8262 12.123 12.0117 11.9004C12.1973 11.6777 12.4199 11.6406 12.5684 11.6406C12.6797 11.6406 12.8281 11.6406 12.9395 11.6406C13.0879 11.6406 13.2363 11.6035 13.4219 12.0117C13.5703 12.4199 14.0156 13.4219 14.0527 13.5332C14.0898 13.6445 14.127 13.7559 14.0527 13.9043C13.6816 14.6836 13.2363 14.6465 13.459 15.0176C14.2754 16.3906 15.0547 16.873 16.2793 17.4668C16.4648 17.5781 16.5762 17.541 16.7246 17.4297C16.8359 17.2812 17.2441 16.7988 17.3555 16.6133C17.5039 16.3906 17.6523 16.4277 17.8379 16.502C18.0234 16.5762 19.0254 17.0586 19.248 17.1699Z"
                                    fill="white"
                                />
                            </svg>
                        </template>

                        <template x-if="chatItem?.chatbot_channel === 'telegram'">
                            <svg
                                width="32"
                                height="31"
                                viewBox="0 0 32 31"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M16 31C24.5604 31 31.5 24.0604 31.5 15.5C31.5 6.93959 24.5604 0 16 0C7.43959 0 0.5 6.93959 0.5 15.5C0.5 24.0604 7.43959 31 16 31Z"
                                    fill="#1D93D2"
                                />
                                <path
                                    d="M10.9921 16.6331L12.831 21.723C12.831 21.723 13.0609 22.1992 13.3071 22.1992C13.5533 22.1992 17.2151 18.3898 17.2151 18.3898L21.2871 10.5249L11.0577 15.3192L10.9921 16.6331Z"
                                    fill="#C8DAEA"
                                />
                                <path
                                    d="M13.4304 17.9384L13.0773 21.6902C13.0773 21.6902 12.9296 22.8398 14.0789 21.6902C15.2282 20.5406 16.3283 19.6541 16.3283 19.6541"
                                    fill="#A9C6D8"
                                />
                                <path
                                    d="M11.0253 16.8147L7.2425 15.5821C7.2425 15.5821 6.79042 15.3987 6.93599 14.9828C6.96595 14.8971 7.0264 14.8241 7.20724 14.6986C8.0454 14.1144 22.7209 8.83965 22.7209 8.83965C22.7209 8.83965 23.1353 8.70002 23.3797 8.79289C23.4401 8.81161 23.4945 8.84604 23.5373 8.89268C23.5801 8.93932 23.6097 8.99648 23.6232 9.05833C23.6496 9.16756 23.6606 9.27994 23.656 9.39222C23.6548 9.48936 23.643 9.57939 23.6341 9.72057C23.5448 11.1627 20.87 21.9259 20.87 21.9259C20.87 21.9259 20.7099 22.5557 20.1366 22.5773C19.9957 22.5819 19.8553 22.558 19.7238 22.5071C19.5923 22.4563 19.4724 22.3794 19.3713 22.2812C18.2461 21.3134 14.3571 18.6998 13.4978 18.1251C13.4784 18.1118 13.4621 18.0946 13.4499 18.0746C13.4377 18.0545 13.43 18.0321 13.4273 18.0088C13.4152 17.9482 13.4811 17.8732 13.4811 17.8732C13.4811 17.8732 20.2528 11.854 20.433 11.2221C20.447 11.1732 20.3942 11.149 20.3235 11.1705C19.8737 11.3359 12.077 16.2596 11.2164 16.803C11.1545 16.8218 11.089 16.8258 11.0253 16.8147Z"
                                    fill="white"
                                />
                            </svg>
                        </template>
                    </figure>

                    <div class="flex w-10/12 grow gap-1">
                        <div class="max-w-full grow overflow-hidden text-start">
                            <h4 class="mb-0 flex max-w-full items-center gap-2 text-xs font-medium">
                                <span
                                    class="inline-block grow truncate"
                                    x-text="chatItem.conversation_name"
                                ></span>
                                <x-tabler-pinned
                                    class="size-4 shrink-0 fill-current"
                                    x-cloak
                                    x-show="chatItem.pinned != null && chatItem.pinned > 0"
                                />
                            </h4>
                            <p
                                class="mb-0 text-xs opacity-50"
                                x-text="`@${chatItem.chatbot_channel === 'frame' ? '{{ __('livechat') }}' : chatItem.chatbot_channel}`"
                            ></p>
                            <p
                                class="mb-0 truncate text-xs"
                                x-text="chatItem.lastMessage?.message ? chatItem.lastMessage?.message :  '{{ __('Chat history item') }}'"
                                :class="{ 'font-bold': chatItem.role === 'user' && chatItem.histories?.find(h => !h.read_at) }"
                            ></p>
                        </div>

                        <div class="shrink-0">
                            <p class="mb-0.5 text-[12px] opacity-40">
                                <span x-text="getShortDiffHumanTime(chatItem.updated_at || chatItem?.lastMessage?.created_at)"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <a
                    class="lqd-ext-chatbot-history-list-item-trigger absolute start-0 top-0 z-2 inline-block h-full w-full"
                    href="#"
                    title="{{ __('Open Chat History') }}"
                    @click.prevent="setActiveChat(chatItem.id)"
                ></a>
            </li>
        </template>

        <template x-if="!fetching && !chatsList.length">
            <p class="mb-0 px-4 py-5 font-medium">
                {{ __('No chat history found.') }}
            </p>
        </template>
    </ul>

    <div
        class="lqd-ext-chatbot-history-load-wrap grid place-items-center p-6 font-medium text-heading-foreground"
        x-ref="loadMoreWrap"
    >
        <x-button
            class="lqd-ext-chatbot-history-load-more col-start-1 col-end-1 row-start-1 row-end-1 w-full"
            variant="link"
            href="{{ route('dashboard.chatbot.conversations.with.paginate', ['page' => 1]) }}"
            x-ref="loadMore"
            x-show="!allLoaded && !fetching"
            x-cloak
            @click.prevent="loadMore"
        >
            {{ __('Load More') }}
        </x-button>
        <span
            class="lqd-ext-chatbot-history-loading col-start-1 col-end-1 row-start-1 row-end-1 inline-flex gap-2"
            x-show="!allLoaded && fetching"
            x-ref="loading"
        >
            {{ __('Loading') }}
            <x-tabler-refresh class="size-4 animate-spin" />
        </span>
        <span
            class="lqd-ext-chatbot-history-all-loaded col-start-1 col-end-1 row-start-1 row-end-1 inline-flex gap-2"
            x-ref="allLoaded"
            x-show="!fetching && chatsList.length && allLoaded"
            x-cloak
        >
            {{ __('All Items Loaded') }}
            <x-tabler-check class="size-4" />
        </span>
    </div>
</div>
