<template x-for="(message, index) in activeChat?.histories">
    <div>
        @includeIf('chatbot-voice-call::particles.voice-call-info-box')

        <template x-if="message.role !== 'voice-call-started' && message.role !== 'voice-call-ended'">
            <div
                class="lqd-ext-chatbot-history-message flex max-w-[430px] gap-2"
                data-sender="message.role"
                :class="{ 'flex-row-reverse ms-auto': message.role === 'assistant' || message.role === 'voice-transcript-assistant' }"
            >
                <template x-if="message.role === 'user' || message.role === 'voice-transcript-user'">
                    <figure
                        class="inline-grid size-6 shrink-0 place-items-center rounded-full bg-foreground/20 font-heading text-[12px] font-semibold uppercase text-white"
                        :style="{ 'backgroundColor': activeChat.color ?? '#e633ec' }"
                    >
                        <img
                            class="col-start-1 col-end-1 row-start-1 row-end-1 size-full object-cover object-center"
                            :src="activeChat.avatar"
                            x-show="activeChat.avatar"
                        >

                        <span
                            class="col-start-1 col-end-1 row-start-1 row-end-1"
                            x-show="!activeChat.avatar"
                            x-text="(activeChat.conversation_name ?? '{{ __('Anonymous User') }}').split('')?.at(0)"
                        ></span>
                    </figure>
                </template>

                <div class="lqd-ext-chatbot-history-message-content-wrap max-w-full space-y-1 lg:max-w-[420px]">
                    <div
                        class="lqd-ext-chatbot-history-message-content w-full rounded-xl p-3 [&_.lqd-ext-chatbot-product-carousel-btn-prev]:start-3 [&_.lqd-ext-chatbot-product-carousel]:-mx-3"
                        :class="{
                            'bg-heading-foreground/5 text-heading-foreground': message.role === 'user' || message.role === 'voice-transcript-user',
                            'bg-secondary text-secondary-foreground ms-auto dark:bg-zinc-700 dark:text-primary-foreground': message.role === 'assistant' || message
                                .role === 'voice-transcript-assistant'
                        }"
                    >
                        <pre
                            class="peer prose m-0 w-full whitespace-normal font-body text-sm text-current dark:prose-invert empty:hidden"
                            :class="{
                                'text-heading-foreground': message.role === 'user' || message.role === 'voice-transcript-user',
                                'text-black': message.role === 'assistant' ||
                                    message.role === 'voice-transcript-assistant'
                            }"
                            x-html="getFormattedString(message.message, { isHTML: message.isHTML })"
                        ></pre>

                        <template x-if="message.media_url && message.media_name">
                            <a
                                class="mt-2 block font-medium text-current underline underline-offset-2 peer-empty:mt-0"
                                :data-fslightbox="isImage ? 'gallery' : null"
                                :href="message.media_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                x-data="{ isImage(string) { return /\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)$/i.test(string) } }"
                            >
                                <template x-if="message.media_url && isImage(message.media_url)">
                                    <img
                                        class="mt-2 max-w-full rounded-lg peer-empty:mt-0"
                                        :src="message.media_url"
                                        :alt="message.media_name || 'Uploaded image'"
                                        x-init="if ('refreshFsLightbox' in window) { refreshFsLightbox() }"
                                    />
                                </template>
                                <template x-if="!message.media_url || !isImage(message.media_url)">
                                    <span x-text="message.media_name ?? message.media_url"></span>
                                </template>
                            </a>
                        </template>

                        <p
                            class="mb-0 mt-1 flex items-center gap-1.5 text-3xs opacity-40"
                            :class="{ 'justify-end text-end': message.role === 'assistant' || message.role === 'voice-transcript-assistant' }"
                            x-show="(message.media_url && message.media_name) || message.message?.trim()"
                        >
                            <span
                                x-text="(message.role === 'user' || message.role === 'voice-transcript-user') ? (activeChat.conversation_name ?? '{{ __('Anonymous') }}') : '{{ __('Agent') }}'"
                            ></span>
                            <span class="inline-block size-0.5 rounded-full bg-current"></span>
                            <span x-text="getDiffHumanTime(message.created_at)"></span>
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<template x-if="!fetching && !activeChat?.histories?.length">
    <h4>
        {{ __('No messages found.') }}
    </h4>
</template>
