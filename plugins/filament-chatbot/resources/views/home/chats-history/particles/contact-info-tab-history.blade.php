<div
    class="col-start-1 col-end-1 row-start-1 row-end-1 w-full px-4 py-6 text-center"
    x-show="contactInfo.activeTab === 'history'"
    x-transition.opacity
>
    <div class="h-full overflow-y-auto">
        <ul class="lqd-ext-chatbot-history-list">
            <template x-for="(chatItem, index) in userConversationHistory">
                <li
                    class="lqd-ext-chatbot-history-list-item group/chat-item relative border-b px-6 py-4 before:absolute before:inset-x-2.5 before:inset-y-1.5 before:z-0 before:scale-95 before:rounded-xl before:bg-primary/10 before:opacity-0 before:transition [&.active]:before:scale-100 [&.active]:before:opacity-100"
                    :class="{ 'active': activeChat.id === chatItem.id }"
                >
                    <div class="relative z-1 flex gap-2.5">
                        <figure
                            class="inline-grid size-8 shrink-0 place-items-center rounded-full bg-foreground/20 font-heading text-xs font-semibold uppercase text-white"
                            :style="{ 'backgroundColor': chatItem.color ?? '#e633ec' }"
                        >
                            <img
                                class="col-start-1 col-end-1 row-start-1 row-end-1 size-full object-cover object-center"
                                :src="chatItem.avatar"
                                x-show="chatItem.avatar"
                            >

                            <span
                                class="col-start-1 col-end-1 row-start-1 row-end-1"
                                x-show="!chatItem.avatar"
                                x-text="(chatItem.conversation_name ?? '{{ __('Anonymous User') }}').split('')?.at(0)"
                            ></span>
                        </figure>

                        <div class="flex w-10/12 grow gap-1">
                            <div class="max-w-full grow overflow-hidden text-start">
                                <h4
                                    class="mb-0 truncate text-xs font-medium"
                                    x-text="chatItem.conversation_name"
                                ></h4>
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
                                <span
                                    class="ms-auto flex size-4 items-center justify-center rounded-full bg-primary/10 text-4xs font-medium text-primary"
                                    x-text="getUnreadMessages(chatItem.id)"
                                    x-show="getUnreadMessages(chatItem.id)"
                                ></span>
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

            <template x-if="!userConversationHistory.length">
                <p class="mb-0 px-4 py-5 font-medium">
                    {{ __('No chat history found.') }}
                </p>
            </template>
        </ul>
    </div>
</div>
