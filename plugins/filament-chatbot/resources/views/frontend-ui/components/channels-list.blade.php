@php
    $channels = [
        [
            'id' => 'whatsapp',
            'title' => 'WhatsApp',
            'logo' =>
                '<svg width="18" height="18" viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M8.95056 0.760199C4.29813 0.760199 0.513062 4.54499 0.513062 9.1977C0.513062 10.8347 0.982452 12.4128 1.87344 13.7834L0.558655 16.8513C0.468018 17.0623 0.515259 17.3078 0.677856 17.4704C0.785522 17.5781 0.929443 17.6352 1.07556 17.6352C1.15027 17.6352 1.2258 17.6204 1.29721 17.5896L4.36514 16.2745C5.73541 17.1661 7.3136 17.6352 8.95056 17.6352C13.6033 17.6352 17.3881 13.8504 17.3881 9.1977C17.3881 4.54499 13.6033 0.760199 8.95056 0.760199ZM13.2814 12.2178C13.2814 12.2178 12.5799 13.1176 12.0729 13.328C10.7842 13.8614 8.96484 13.328 6.89227 11.256C4.82025 9.18342 4.28659 7.36408 4.82025 6.07538C5.03064 5.56782 5.93042 4.86689 5.93042 4.86689C6.17432 4.67682 6.55334 4.70045 6.77197 4.91907L7.78986 5.93696C8.00848 6.15558 8.00848 6.51374 7.78986 6.73237L7.151 7.37067C7.151 7.37067 6.89227 8.14741 8.44629 9.70197C10.0003 11.256 10.7776 10.9973 10.7776 10.9973L11.4159 10.3584C11.6345 10.1398 11.9927 10.1398 12.2113 10.3584L13.2292 11.3763C13.4478 11.5949 13.4714 11.9734 13.2814 12.2178Z"/></svg>',
            'href' => $chatbot->whatsapp_link ?? '#',
        ],
        [
            'id' => 'telegram',
            'title' => 'Telegram',
            'logo' =>
                '<svg width="18" height="16" viewBox="0 0 18 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M16.55 1.05915C14.2941 1.99335 4.62144 5.99997 1.9488 7.09203C0.156356 7.79151 1.20558 8.44725 1.20558 8.44725C1.20558 8.44725 2.73558 8.97177 4.04706 9.36525C5.35854 9.75873 6.05802 9.32151 6.05802 9.32151L12.2219 5.16837C14.4077 3.68211 13.8832 4.90611 13.3586 5.43063C12.2219 6.56733 10.3422 8.35959 8.76846 9.80229C8.06898 10.4143 8.41872 10.939 8.72472 11.2012C9.86142 12.163 12.9652 14.1302 13.1401 14.2614C14.0637 14.9152 15.8803 15.8564 16.1566 13.8679L17.2495 7.00455C17.5993 4.68759 17.949 2.54541 17.9927 1.93341C18.1238 0.447148 16.55 1.05915 16.55 1.05915Z"/></svg>',
            'href' => $chatbot->telegram_link ?? '#',
        ],
        [
            'id' => 'facebook',
            'title' => 'Facebook',
            'logo' =>
                '<svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M16.2821 8.14104C16.2821 12.2116 13.2948 15.5927 9.38845 16.1836V10.5046H11.2924L11.6535 8.14104H9.38845V6.63101C9.38845 5.97447 9.71672 5.35076 10.7344 5.35076H11.752V3.34833C11.752 3.34833 10.8328 3.1842 9.91368 3.1842C8.07538 3.1842 6.86079 4.33313 6.86079 6.36839V8.14104H4.79271V10.5046H6.86079V16.1836C2.95441 15.5927 0 12.2116 0 8.14104C0 3.64377 3.64377 0 8.14104 0C12.6383 0 16.2821 3.64377 16.2821 8.14104Z"/></svg>',
            'href' => $chatbot->facebook_link ?? '#',
        ],
        [
            'id' => 'instagram',
            'title' => 'Instagram',
            'logo' =>
                '<svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12.75 0H4.25C1.904 0 0 1.904 0 4.25V12.75C0 15.096 1.904 17 4.25 17H12.75C15.096 17 17 15.096 17 12.75V4.25C17 1.904 15.096 0 12.75 0ZM8.5 12.75C6.154 12.75 4.25 10.846 4.25 8.5C4.25 6.154 6.154 4.25 8.5 4.25C10.846 4.25 12.75 6.154 12.75 8.5C12.75 10.846 10.846 12.75 8.5 12.75ZM13.0475 4.777C12.58 4.777 12.1975 4.3945 12.1975 3.927C12.1975 3.4595 12.58 3.077 13.0475 3.077C13.515 3.077 13.8975 3.4595 13.8975 3.927C13.8975 4.3945 13.515 4.777 13.0475 4.777Z" /><path d="M8.50443 11.0499C9.91275 11.0499 11.0544 9.90826 11.0544 8.49993C11.0544 7.0916 9.91275 5.94993 8.50443 5.94993C7.0961 5.94993 5.95443 7.0916 5.95443 8.49993C5.95443 9.90826 7.0961 11.0499 8.50443 11.0499Z"/></svg>',
            'href' => $chatbot->instagram_link ?? '#',
        ],
        [
            'id' => 'watch_product_tour',
            'title' => 'Watch Product Tour',
            'logo' =>
                '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M10.25 0.25C4.87391 0.25 0.5 4.62391 0.5 10C0.5 15.3761 4.87391 19.75 10.25 19.75C15.6261 19.75 20 15.3761 20 10C20 4.62391 15.6261 0.25 10.25 0.25ZM13.7548 10.4359L8.39 13.6769C8.3131 13.7229 8.22533 13.7477 8.1357 13.7487C8.04606 13.7496 7.95778 13.7267 7.87992 13.6823C7.80205 13.6379 7.73741 13.5736 7.69261 13.4959C7.64781 13.4183 7.62448 13.3301 7.625 13.2405V6.75953C7.62448 6.66989 7.64781 6.58173 7.69261 6.50408C7.73741 6.42643 7.80205 6.3621 7.87992 6.31768C7.95778 6.27326 8.04606 6.25036 8.1357 6.25131C8.22533 6.25227 8.3131 6.27705 8.39 6.32312L13.7548 9.56406C13.8296 9.60962 13.8914 9.67365 13.9343 9.75C13.9772 9.82634 13.9997 9.91243 13.9997 10C13.9997 10.0876 13.9772 10.1737 13.9343 10.25C13.8914 10.3264 13.8296 10.3904 13.7548 10.4359Z"/></svg>',
            'href' => $chatbot->watch_product_tour_link ?? '#',
        ],
    ];

    $channels_has_actual_links = isset($chatbot) && collect($channels)->some(fn($channel) => !empty($channel['href']) && $channel['href'] !== '#');
@endphp

@if ($is_editor || (isset($chatbot) && ($chatbot->is_links || $chatbot->is_contact)))
    <div
        class="flex flex-col gap-6"
        @if ($is_editor) x-show="activeChatbot.is_links || activeChatbot.is_contact" @endif
    >
        @if ($is_editor || $channels_has_actual_links)
            <div
                class="space-y-2"
                @if ($is_editor) x-show="activeChatbot.is_links && [activeChatbot.whatsapp_link, activeChatbot.telegram_link, activeChatbot.facebook_link, activeChatbot.instagram_link, activeChatbot.watch_product_tour_link].some(link => link?.trim() && link !== '#')" @endif
            >
                @foreach ($channels as $channel)
                    @continue(isset($chatbot) && (empty($channel['href']) || $channel['href'] === '#'))
                    <a
                        class="flex items-center justify-between gap-1 rounded-xl bg-black/5 px-6 py-4 text-[12px] font-medium transition-all hover:scale-[1.03] hover:bg-black hover:text-white"
                        {{-- blade-formatter-disable --}}
						@if ($is_editor)
							:href="activeChatbot.{{ $channel['id'] . '_link' }}"
							x-show="activeChatbot.{{ $channel['id'] . '_link' }}?.trim()"
						@else
							href="{{ $channel['href'] }}"
						@endif
						target="_blank"
						{{-- blade-formatter-enable --}}
                    >
                        {{ $channel['title'] }}

                        <span class="opacity-90">
                            {!! $channel['logo'] !!}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($is_editor || (isset($chatbot) && $chatbot->is_contact))
            <a
                class="flex items-center justify-center gap-3 px-4 pb-0.5 text-center text-xs text-[--lqd-ext-chat-primary] underline underline-offset-4"
                @if ($is_editor) x-show="activeChatbot.is_contact" @endif
                href="#"
                @click.prevent="toggleView('contact-form')"
            >
                {{-- blade-formatter-disable --}}
				<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg" > <path d="M12.75 5C12.125 5 11.5938 4.78125 11.1562 4.34375C10.7188 3.90625 10.5 3.375 10.5 2.75C10.5 2.125 10.7188 1.59375 11.1562 1.15625C11.5938 0.71875 12.125 0.5 12.75 0.5C13.375 0.5 13.9062 0.71875 14.3438 1.15625C14.7812 1.59375 15 2.125 15 2.75C15 3.375 14.7812 3.90625 14.3438 4.34375C13.9062 4.78125 13.375 5 12.75 5ZM0 17V3.5C0 3.0875 0.146875 2.73438 0.440625 2.44063C0.734375 2.14688 1.0875 2 1.5 2H9.075C9.025 2.25 9 2.5 9 2.75C9 3 9.025 3.25 9.075 3.5C9.25 4.375 9.68125 5.09375 10.3687 5.65625C11.0562 6.21875 11.85 6.5 12.75 6.5C13.15 6.5 13.5438 6.4375 13.9313 6.3125C14.3188 6.1875 14.675 6 15 5.75V12.5C15 12.9125 14.8531 13.2656 14.5594 13.5594C14.2656 13.8531 13.9125 14 13.5 14H3L0 17Z" /> </svg>
				{{-- blade-formatter-enable --}}
                {{ __('Leave a message') }}
            </a>
        @endif
    </div>
@endif
