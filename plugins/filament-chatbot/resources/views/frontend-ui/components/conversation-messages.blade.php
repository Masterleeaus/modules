@php
    $connect_agent_enabled =
        isset($chatbot) &&
        $chatbot->getAttribute('interaction_type') === \App\Extensions\Chatbot\System\Enums\InteractionType::SMART_SWITCH &&
        \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-agent');
    $booking_assistant_enabled = isset($chatbot) && $chatbot->getAttribute('is_booking_assistant') && \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-booking');
@endphp

@if ($is_editor)
    <template x-if="activeChatbot.is_gdpr">
        <div class="my-7 flex items-center gap-3 text-center text-3xs">
            <span class="h-px grow bg-current opacity-5"></span>
            <span
                class="max-w-[265px] text-pretty"
                x-html="`{{ __('By continuing, you agree to our <a href=\':pp\' target=\'_blank\' class=\'underline underline-offset-2\'>Privacy Policy</a> and <a href=\':tos\' target=\'_blank\' class=\'underline underline-offset-2\'>Terms of Use</a> to help us enhance our services.') }}`
                    .replace(':pp', activeChatbot.privacy_policy_link || '#')
                    .replace(':tos', activeChatbot.terms_of_service_link || '#')"
            ></span>
            <span class="h-px grow bg-current opacity-5"></span>
        </div>
    </template>
@elseif ($chatbot['is_gdpr'] ?? false)
    <div class="my-7 flex items-center gap-3 text-center text-3xs">
        <span class="h-px grow bg-current opacity-5"></span>
        <span class="max-w-[265px] text-pretty">
            {!! __(
                'By continuing, you agree to our <a href=":privacy_policy_link" target="_blank" class="underline underline-offset-2">Privacy Policy</a> and <a href=":terms_of_service_link" target="_blank" class="underline underline-offset-2">Terms of Use</a> to help us enhance our services.',
                [
                    'privacy_policy_link' => $chatbot['privacy_policy_link'] ?? '#',
                    'terms_of_service_link' => $chatbot['terms_of_service_link'] ?? '#',
                ],
            ) !!}
        </span>
        <span class="h-px grow bg-current opacity-5"></span>
    </div>
@endif

<template x-for="(message, index) in messages">
    <div>
        @includeIf('chatbot-voice-call::particles.voice-call-info-box')

        <template x-if="message.role === 'collect-email'">
            <div class="mb-7 flex items-center gap-3 text-3xs">
                <span class="h-px grow bg-current opacity-5"></span>
                {{ __('Let’s keep you notified.') }}
                <span class="h-px grow bg-current opacity-5"></span>
            </div>
        </template>

        <template x-if="message.role !== 'connecting-to-agent' && message.role !== 'voice-call-started' && message.role !== 'voice-call-ended'">
            <div
                class="lqd-ext-chatbot-window-conversation-message group/message"
                :class="{ 'is-html': message.isHTML }"
                :data-type="message.role === 'voice-transcript-user' ? 'user' : (message.role === 'voice-transcript-assistant' ? 'assistant' : message.role)"
                :data-id="message.id"
            >
                <template x-if="message.role !== 'user' && message.role !== 'voice-transcript-user' && !message.isHTML">
                    <figure class="lqd-ext-chatbot-window-conversation-message-avatar">
                        <img
                            {{-- blade-formatter-disable --}}
						@if ($is_editor)
							:src="() => activeChatbot.avatar ? `${window.location.origin}/${activeChatbot.avatar}` : ''"
						@else
							src="/{{ $chatbot['avatar'] }}"
							alt="{{ $chatbot['title'] }}"
							@if (!empty($chatbot['trigger_avatar_size']))
								style="width: {{ (int) $chatbot['trigger_avatar_size'] }}px"
							@endif
						@endif
						{{-- blade-formatter-enable --}}
                            width="27"
                            height="27"
                        />
                    </figure>
                </template>

                <div class="lqd-ext-chatbot-window-conversation-message-content-wrap">
                    <div
                        class="lqd-ext-chatbot-window-conversation-message-content group-[&.is-html]/message:rounded-none-0 text-xs/5 group-[&.is-html]/message:bg-transparent group-[&.is-html]/message:p-0">
                        <pre
                            class="peer"
                            :class="{ 'prose prose-neutral prose-sm': message.role === 'assistant' }"
                            x-ref="conversationMessage"
                            :data-index="index"
                            x-html="addMessage(message.message, $el, { isHTML: message.isHTML })"
                        ></pre>

                        <template x-if="message.media_url">
                            <div class="mt-2 peer-empty:mt-0">
                                <template x-if="message.media_url.startsWith('blob:') || /\.(jpe?g|png|webp|gif)$/i.test(message.media_url)">
                                    <img
                                        class="max-h-[200px] rounded-lg object-contain"
                                        :src="message.media_url"
                                        :alt="message.media_name || 'Image'"
                                    />
                                </template>
                                <template x-if="!message.media_url.startsWith('blob:') && !/\.(jpe?g|png|webp|gif)$/i.test(message.media_url)">
                                    <a
                                        class="block font-medium text-current underline underline-offset-2"
                                        :href="message.media_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        x-text="message.media_name"
                                    ></a>
                                </template>
                            </div>
                        </template>

                        {{-- Social links in first assistant message --}}
                        @if ($is_editor)
                            <template
                                x-if="index === 0 && message.role === 'assistant' && activeChatbot.show_social_links_in_first_message && (activeChatbot.whatsapp_link?.trim() || activeChatbot.telegram_link?.trim() || activeChatbot.facebook_link?.trim() || activeChatbot.instagram_link?.trim())"
                            >
                                <div class="mb-1 mt-3.5 flex w-[min(185px,85%)] items-center justify-between gap-4">
                                    <a
                                        class="opacity-60 transition-opacity hover:opacity-100"
                                        x-show="activeChatbot.whatsapp_link?.trim()"
                                        :href="activeChatbot.whatsapp_link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <svg
                                            width="17"
                                            height="17"
                                            viewBox="0 0 17 17"
                                            fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M8.4375 0C3.78506 0 0 3.78479 0 8.4375C0 10.0745 0.469391 11.6526 1.36038 13.0232L0.0455933 16.0911C-0.0450439 16.3021 0.00219727 16.5476 0.164795 16.7102C0.272461 16.8179 0.416382 16.875 0.5625 16.875C0.637207 16.875 0.712738 16.8602 0.784149 16.8294L3.85208 15.5143C5.22235 16.4059 6.80054 16.875 8.4375 16.875C13.0902 16.875 16.875 13.0902 16.875 8.4375C16.875 3.78479 13.0902 0 8.4375 0ZM12.7683 11.4576C12.7683 11.4576 12.0668 12.3574 11.5598 12.5678C10.2711 13.1012 8.45178 12.5678 6.37921 10.4958C4.30719 8.42322 3.77353 6.60388 4.30719 5.31519C4.51758 4.80762 5.41736 4.10669 5.41736 4.10669C5.66125 3.91663 6.04028 3.94025 6.25891 4.15887L7.27679 5.17676C7.49542 5.39539 7.49542 5.75354 7.27679 5.97217L6.63794 6.61047C6.63794 6.61047 6.37921 7.38721 7.93323 8.94177C9.48724 10.4958 10.2645 10.2371 10.2645 10.2371L10.9028 9.59821C11.1215 9.37958 11.4796 9.37958 11.6982 9.59821L12.7161 10.6161C12.9348 10.8347 12.9584 11.2132 12.7683 11.4576Z"
                                            />
                                        </svg>
                                    </a>
                                    <a
                                        class="opacity-60 transition-opacity hover:opacity-100"
                                        x-show="activeChatbot.telegram_link?.trim()"
                                        :href="activeChatbot.telegram_link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <svg
                                            width="18"
                                            height="15"
                                            viewBox="0 0 18 15"
                                            fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M15.6496 0.124822C13.3937 1.05902 3.72105 5.06564 1.04841 6.1577C-0.744034 6.85718 0.305186 7.51292 0.305186 7.51292C0.305186 7.51292 1.83519 8.03744 3.14667 8.43092C4.45815 8.8244 5.15763 8.38718 5.15763 8.38718L11.3215 4.23404C13.5073 2.74778 12.9828 3.97178 12.4582 4.4963C11.3215 5.633 9.44181 7.42526 7.86807 8.86796C7.16859 9.47996 7.51833 10.0047 7.82433 10.2669C8.96103 11.2287 12.0648 13.1959 12.2397 13.3271C13.1633 13.9809 14.9799 14.9221 15.2562 12.9336L16.3491 6.07022C16.6989 3.75326 17.0486 1.61108 17.0923 0.999082C17.2234 -0.487178 15.6496 0.124822 15.6496 0.124822Z"
                                            />
                                        </svg>
                                    </a>
                                    <a
                                        class="opacity-60 transition-opacity hover:opacity-100"
                                        x-show="activeChatbot.facebook_link?.trim()"
                                        :href="activeChatbot.facebook_link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <svg
                                            width="17"
                                            height="17"
                                            viewBox="0 0 17 17"
                                            fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M16.2821 8.14104C16.2821 12.2116 13.2948 15.5927 9.38845 16.1836V10.5046H11.2924L11.6535 8.14104H9.38845V6.63101C9.38845 5.97447 9.71672 5.35076 10.7344 5.35076H11.752V3.34833C11.752 3.34833 10.8328 3.1842 9.91368 3.1842C8.07538 3.1842 6.86079 4.33313 6.86079 6.36839V8.14104H4.79271V10.5046H6.86079V16.1836C2.95441 15.5927 0 12.2116 0 8.14104C0 3.64377 3.64377 0 8.14104 0C12.6383 0 16.2821 3.64377 16.2821 8.14104Z"
                                            />
                                        </svg>
                                    </a>
                                    <a
                                        class="opacity-60 transition-opacity hover:opacity-100"
                                        x-show="activeChatbot.instagram_link?.trim()"
                                        :href="activeChatbot.instagram_link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <svg
                                            width="17"
                                            height="17"
                                            viewBox="0 0 17 17"
                                            fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M12.75 0H4.25C1.904 0 0 1.904 0 4.25V12.75C0 15.096 1.904 17 4.25 17H12.75C15.096 17 17 15.096 17 12.75V4.25C17 1.904 15.096 0 12.75 0ZM8.5 12.75C6.154 12.75 4.25 10.846 4.25 8.5C4.25 6.154 6.154 4.25 8.5 4.25C10.846 4.25 12.75 6.154 12.75 8.5C12.75 10.846 10.846 12.75 8.5 12.75ZM13.0475 4.777C12.58 4.777 12.1975 4.3945 12.1975 3.927C12.1975 3.4595 12.58 3.077 13.0475 3.077C13.515 3.077 13.8975 3.4595 13.8975 3.927C13.8975 4.3945 13.515 4.777 13.0475 4.777Z"
                                            />
                                            <path
                                                d="M8.50443 11.0499C9.91275 11.0499 11.0544 9.90826 11.0544 8.49993C11.0544 7.0916 9.91275 5.94993 8.50443 5.94993C7.0961 5.94993 5.95443 7.0916 5.95443 8.49993C5.95443 9.90826 7.0961 11.0499 8.50443 11.0499Z"
                                            />
                                        </svg>
                                    </a>
                                </div>
                            </template>
                        @elseif (isset($chatbot) && $chatbot['show_social_links_in_first_message'])
                            <template x-if="index === 0 && message.role === 'assistant'">
                                <div class="mb-1 mt-3.5 flex w-[min(185px,85%)] items-center justify-between gap-4">
                                    @if (!empty($chatbot['whatsapp_link']))
                                        <a
                                            class="opacity-60 transition-opacity hover:opacity-100"
                                            href="{{ $chatbot['whatsapp_link'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <svg
                                                width="17"
                                                height="17"
                                                viewBox="0 0 17 17"
                                                fill="currentColor"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M8.4375 0C3.78506 0 0 3.78479 0 8.4375C0 10.0745 0.469391 11.6526 1.36038 13.0232L0.0455933 16.0911C-0.0450439 16.3021 0.00219727 16.5476 0.164795 16.7102C0.272461 16.8179 0.416382 16.875 0.5625 16.875C0.637207 16.875 0.712738 16.8602 0.784149 16.8294L3.85208 15.5143C5.22235 16.4059 6.80054 16.875 8.4375 16.875C13.0902 16.875 16.875 13.0902 16.875 8.4375C16.875 3.78479 13.0902 0 8.4375 0ZM12.7683 11.4576C12.7683 11.4576 12.0668 12.3574 11.5598 12.5678C10.2711 13.1012 8.45178 12.5678 6.37921 10.4958C4.30719 8.42322 3.77353 6.60388 4.30719 5.31519C4.51758 4.80762 5.41736 4.10669 5.41736 4.10669C5.66125 3.91663 6.04028 3.94025 6.25891 4.15887L7.27679 5.17676C7.49542 5.39539 7.49542 5.75354 7.27679 5.97217L6.63794 6.61047C6.63794 6.61047 6.37921 7.38721 7.93323 8.94177C9.48724 10.4958 10.2645 10.2371 10.2645 10.2371L10.9028 9.59821C11.1215 9.37958 11.4796 9.37958 11.6982 9.59821L12.7161 10.6161C12.9348 10.8347 12.9584 11.2132 12.7683 11.4576Z"
                                                />
                                            </svg>
                                        </a>
                                    @endif
                                    @if (!empty($chatbot['telegram_link']))
                                        <a
                                            class="opacity-60 transition-opacity hover:opacity-100"
                                            href="{{ $chatbot['telegram_link'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <svg
                                                width="18"
                                                height="15"
                                                viewBox="0 0 18 15"
                                                fill="currentColor"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M15.6496 0.124822C13.3937 1.05902 3.72105 5.06564 1.04841 6.1577C-0.744034 6.85718 0.305186 7.51292 0.305186 7.51292C0.305186 7.51292 1.83519 8.03744 3.14667 8.43092C4.45815 8.8244 5.15763 8.38718 5.15763 8.38718L11.3215 4.23404C13.5073 2.74778 12.9828 3.97178 12.4582 4.4963C11.3215 5.633 9.44181 7.42526 7.86807 8.86796C7.16859 9.47996 7.51833 10.0047 7.82433 10.2669C8.96103 11.2287 12.0648 13.1959 12.2397 13.3271C13.1633 13.9809 14.9799 14.9221 15.2562 12.9336L16.3491 6.07022C16.6989 3.75326 17.0486 1.61108 17.0923 0.999082C17.2234 -0.487178 15.6496 0.124822 15.6496 0.124822Z"
                                                />
                                            </svg>
                                        </a>
                                    @endif
                                    @if (!empty($chatbot['facebook_link']))
                                        <a
                                            class="opacity-60 transition-opacity hover:opacity-100"
                                            href="{{ $chatbot['facebook_link'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <svg
                                                width="17"
                                                height="17"
                                                viewBox="0 0 17 17"
                                                fill="currentColor"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M16.2821 8.14104C16.2821 12.2116 13.2948 15.5927 9.38845 16.1836V10.5046H11.2924L11.6535 8.14104H9.38845V6.63101C9.38845 5.97447 9.71672 5.35076 10.7344 5.35076H11.752V3.34833C11.752 3.34833 10.8328 3.1842 9.91368 3.1842C8.07538 3.1842 6.86079 4.33313 6.86079 6.36839V8.14104H4.79271V10.5046H6.86079V16.1836C2.95441 15.5927 0 12.2116 0 8.14104C0 3.64377 3.64377 0 8.14104 0C12.6383 0 16.2821 3.64377 16.2821 8.14104Z"
                                                />
                                            </svg>
                                        </a>
                                    @endif
                                    @if (!empty($chatbot['instagram_link']))
                                        <a
                                            class="opacity-60 transition-opacity hover:opacity-100"
                                            href="{{ $chatbot['instagram_link'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <svg
                                                width="17"
                                                height="17"
                                                viewBox="0 0 17 17"
                                                fill="currentColor"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M12.75 0H4.25C1.904 0 0 1.904 0 4.25V12.75C0 15.096 1.904 17 4.25 17H12.75C15.096 17 17 15.096 17 12.75V4.25C17 1.904 15.096 0 12.75 0ZM8.5 12.75C6.154 12.75 4.25 10.846 4.25 8.5C4.25 6.154 6.154 4.25 8.5 4.25C10.846 4.25 12.75 6.154 12.75 8.5C12.75 10.846 10.846 12.75 8.5 12.75ZM13.0475 4.777C12.58 4.777 12.1975 4.3945 12.1975 3.927C12.1975 3.4595 12.58 3.077 13.0475 3.077C13.515 3.077 13.8975 3.4595 13.8975 3.927C13.8975 4.3945 13.515 4.777 13.0475 4.777Z"
                                                />
                                                <path
                                                    d="M8.50443 11.0499C9.91275 11.0499 11.0544 9.90826 11.0544 8.49993C11.0544 7.0916 9.91275 5.94993 8.50443 5.94993C7.0961 5.94993 5.95443 7.0916 5.95443 8.49993C5.95443 9.90826 7.0961 11.0499 8.50443 11.0499Z"
                                                />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </template>
                        @endif

                        <template x-if="message.role === 'loader'">
                            <span class="lqd-ext-chatbot-window-conversation-message-loader inline-flex items-center gap-1">
                                <span class="inline-block size-1 rounded-full bg-current"></span>
                                <span class="inline-block size-1 rounded-full bg-current"></span>
                                <span class="inline-block size-1 rounded-full bg-current"></span>
                            </span>
                        </template>

                        <template x-if="message.role === 'collect-email'">
                            <div>
                                <p class="mb-3.5 text-balance">
                                    {{ __('In case we lose contact, may I have your email address so we can follow up?') }}
                                </p>
                                <form
                                    class="relative mb-0 w-full"
                                    @submit.prevent="collectEmail"
                                >
                                    <input
                                        class="h-[50px] w-full rounded-lg bg-white px-4 shadow-[0_4px_44px_hsl(0_0%_0%/5%)]"
                                        placeholder="{{ __('Email address') }}"
                                        type="email"
                                        name="email"
                                        :disabled="collectingEmail"
                                    >
                                    <button
                                        class="absolute end-2 top-1/2 inline-grid size-8 -translate-y-1/2 place-items-center text-black"
                                        type="submit"
                                        :class="{ 'pointer-events-none': collectingEmail }"
                                    >
                                        <svg
                                            class="col-start-1 col-end-1 row-start-1 row-end-1"
                                            width="14"
                                            height="12"
                                            viewBox="0 0 14 12"
                                            fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg"
                                            x-show="!collectingEmail"
                                        >
                                            <path
                                                d="M13.0318 5.20841L13.0274 5.20649L1.34941 0.36282C1.25119 0.321708 1.14431 0.305585 1.03833 0.315892C0.932345 0.326199 0.830572 0.362615 0.74211 0.421882C0.648647 0.483124 0.571876 0.56664 0.518704 0.664918C0.465532 0.763196 0.437627 0.87315 0.4375 0.98489V4.08266C0.437552 4.23542 0.490891 4.38337 0.588324 4.50102C0.685757 4.61867 0.821179 4.69864 0.97125 4.72716L7.34043 5.90485C7.36546 5.9096 7.38804 5.92293 7.40429 5.94255C7.42054 5.96216 7.42943 5.98684 7.42943 6.01231C7.42943 6.03779 7.42054 6.06246 7.40429 6.08208C7.38804 6.1017 7.36546 6.11503 7.34043 6.11977L0.971524 7.29747C0.821495 7.32591 0.68608 7.40578 0.588604 7.52332C0.491127 7.64086 0.437691 7.78871 0.4375 7.94141V11.0397C0.437428 11.1464 0.463847 11.2515 0.514387 11.3455C0.564928 11.4394 0.638008 11.5194 0.72707 11.5781C0.834203 11.6493 0.959931 11.6874 1.08855 11.6875C1.17797 11.6874 1.26648 11.6695 1.34887 11.6347L13.0266 6.81868L13.0318 6.81622C13.1889 6.74866 13.3229 6.63651 13.417 6.49363C13.5111 6.35074 13.5613 6.1834 13.5613 6.01231C13.5613 5.84122 13.5111 5.67388 13.417 5.531C13.3229 5.38812 13.1889 5.27596 13.0318 5.20841Z"
                                            />
                                        </svg>
                                        <x-tabler-loader-2
                                            class="col-start-1 col-end-1 row-start-1 row-end-1 size-4 animate-spin"
                                            x-cloak
                                            x-show="collectingEmail"
                                        />
                                    </button>
                                </form>
                            </div>
                        </template>

                        @if ($connect_agent_enabled)
                            <template x-if="message.showConnectButtons">
                                <div class="mt-3.5 flex w-full flex-wrap gap-2">
                                    <button
                                        class="rounded-xl bg-[#3882C20D] px-5 py-3 text-start text-xs font-normal text-[#3882C2] transition-all hover:-translate-y-0.5 hover:bg-[--lqd-ext-chat-primary] hover:text-[--lqd-ext-chat-primary-foreground]"
                                        @click.prevent="doNotConnectToAgent"
                                    >
                                        {{ __('No, Thanks! 👍') }}
                                    </button>

                                    <button
                                        class="rounded-xl bg-[#3882C20D] px-5 py-3 text-start text-xs font-normal text-[#3882C2] transition-all hover:-translate-y-0.5 hover:bg-[--lqd-ext-chat-primary] hover:text-[--lqd-ext-chat-primary-foreground]"
                                        @click.prevent="connectToAgent"
                                    >
                                        {{ __('Connect to human agent') }}
                                    </button>
                                </div>
                            </template>
                        @endif

                        @if ($booking_assistant_enabled)
                            <template x-if="message.showBookingButtons">
                                <div class="mt-3.5 flex w-full flex-wrap gap-2">
                                    <button
                                        class="rounded-xl bg-[#3882C20D] px-5 py-3 text-start text-xs font-normal text-[#3882C2] transition-all hover:-translate-y-0.5 hover:bg-[--lqd-ext-chat-primary] hover:text-[--lqd-ext-chat-primary-foreground]"
                                        @click.prevent="doNotShowBookingIframe"
                                    >
                                        {{ __('No, Thanks! 👍') }}
                                    </button>

                                    <button
                                        class="rounded-xl bg-[#3882C20D] px-5 py-3 text-start text-xs font-normal text-[#3882C2] transition-all hover:-translate-y-0.5 hover:bg-[--lqd-ext-chat-primary] hover:text-[--lqd-ext-chat-primary-foreground]"
                                        @click.prevent="setBookingIframe(true)"
                                    >
                                        {{ __('Schedule 📅') }}
                                    </button>
                                </div>
                            </template>
                        @endif
                    </div>
                    @if (isset($chatbot) && $chatbot['show_date_and_time'])
                        <template x-if="message.role !== 'collect-email' && !message.isHTML">
                            <div
                                class="lqd-ext-chatbot-window-conversation-message-time"
                                x-text="getTimeLabel(message.created_at ?? new Date())"
                            ></div>
                        </template>
                    @endif
                    @if (!isset($chatbot))
                        <template x-if="activeChatbot?.show_date_and_time && message.role !== 'collect-email' && !message.isHTML">
                            <div
                                class="lqd-ext-chatbot-window-conversation-message-time"
                                x-text="getTimeLabel(message.created_at ?? new Date())"
                            ></div>
                        </template>
                    @endif
                </div>
            </div>
        </template>

        @if ($connect_agent_enabled)
            <template x-if="message.role === 'connecting-to-agent'">
                <div
                    class="mt-7 flex items-center gap-3 text-3xs"
                    :class="{ 'hidden': !connectingToAgent && connect_agent_at == null, 'flex': connectingToAgent || connect_agent_at != null }"
                >
                    <span class="h-px grow bg-current opacity-5"></span>
                    <span class="flex items-center gap-1">
                        <span x-text="connect_agent_at == null && connectingToAgent ? '{{ __('Connecting to Human Agent') }}' : '{{ __('Human Agent Connected') }}'"></span>
                        <template x-if="message.role === 'connect-agent' && connectingToAgent">
                            <span class="lqd-ext-chatbot-window-conversation-message-loader inline-flex items-center gap-0.5">
                                <span class="inline-block size-0.5 rounded-full bg-current"></span>
                                <span class="inline-block size-0.5 rounded-full bg-current"></span>
                                <span class="inline-block size-0.5 rounded-full bg-current"></span>
                            </span>
                        </template>
                    </span>
                    <span class="h-px grow bg-current opacity-5"></span>
                </div>
            </template>
        @endif
    </div>
</template>

@if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-review'))
    @includeIf('chatbot-review::partials.review-widget')
@endif
