<form
    class="lqd-ext-chatbot-window-form relative m-0 flex min-h-full w-full flex-col"
    @submit.prevent="onSendMessage"
>
    <template x-if="pendingMediaPreview">
        <div class="flex items-center gap-2 px-2 pt-2">
            <div class="relative inline-block">
                <img
                    class="size-16 rounded-lg object-cover"
                    :src="pendingMediaPreview"
                    alt="Preview"
                />
                <button
                    class="absolute -end-1.5 -top-1.5 inline-grid size-5 place-items-center rounded-full bg-red-500 p-0 text-white"
                    type="button"
                    @click.prevent="clearPendingMedia"
                >
                    <x-tabler-x class="size-3" />
                </button>
            </div>
        </div>
    </template>

    <div class="flex min-h-14 w-full items-center">
        <textarea
            id="message"
            @class([
                'min-h-14 w-full resize-none border-none bg-transparent ps-2 pt-3.5 text-base font-normal focus:shadow-none focus:outline-none',
                'pe-14' =>
                    isset($chatbot) &&
                    ($chatbot->is_attachment || $chatbot->is_emoji) &&
                    !(isset($chatbot) && $chatbot->is_attachment && $chatbot->is_emoji),
                'pe-24' => isset($chatbot) && $chatbot->is_attachment && $chatbot->is_emoji,
            ])
            name="message"
            cols="30"
            rows="1"
            placeholder="{{ __('Message...') }}"
            @keydown.enter.prevent="onMessageFieldHitEnter"
            @input.throttle.50ms="$el.scrollTop = $el.scrollHeight; $refs.sendBtn.classList.toggle('active', $el.value.trim() || pendingMedia)"
            x-ref="message"
        ></textarea>

        <div class="pointer-events-none absolute end-2 top-auto flex gap-3">
            @if ($is_editor || (isset($chatbot) && $chatbot->is_attachment))
                <div
                    class="pointer-events-auto relative inline-grid size-[18px] cursor-pointer place-items-center overflow-hidden p-0"
                    @if ($is_editor) x-show="activeChatbot.is_attachment" @endif
                >
                    @if (isset($chatbot))
                        <input
                            class="absolute start-0 top-0 z-10 h-full w-full cursor-pointer appearance-none border-none p-0 opacity-0 file:size-full file:cursor-pointer"
                            type="file"
                            @change.prevent="onFileSelect"
                            x-ref="mediaInput"
                            x-show="!uploading"
                            ::accept="activeConversationData?.connect_agent_at ? '' : 'image/jpeg,image/png,image/webp'"
                        >
                    @endif
                    <x-tabler-paperclip
                        class="size-full"
                        x-show="!uploading"
                    />
                    <x-tabler-loader-2
                        class="size-full animate-spin"
                        x-show="uploading"
                    />
                </div>
            @endif
            @if ($is_editor || (isset($chatbot) && $chatbot->is_emoji))
                <button
                    class="pointer-events-auto inline-grid size-[18px] place-items-center p-0"
                    @if ($is_editor) x-show="activeChatbot.is_emoji" @endif
                    @click.prevent="showEmojiPicker = !showEmojiPicker"
                    type="button"
                >
                    <x-tabler-mood-smile class="size-full" />
                </button>
            @endif

            <button
                class="pointer-events-auto hidden size-[18px] cursor-pointer place-items-center [&.active]:inline-grid"
                type="submit"
                x-ref="sendBtn"
            >
                <svg
                    width="16"
                    height="13.5"
                    viewBox="0 0 19 16"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path d="M0 16V10L8 8L0 6V0L19 8L0 16Z" />
                </svg>
            </button>
        </div>
    </div>

    @if ($is_editor || (isset($chatbot) && $chatbot->is_emoji))
        <div
            class="lqd-ext-chatbot-emoji-picker pointer-events-auto absolute -inset-x-4 bottom-full"
            x-ref="emojiPicker"
            x-show="showEmojiPicker"
            @click.outside="showEmojiPicker = false"
        ></div>
    @endif
</form>
