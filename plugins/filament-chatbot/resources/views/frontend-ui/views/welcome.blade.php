@php
    $default_bg_image = custom_theme_url('/assets/img/external-chatbot/chat-default-bg.jpg');
    $bg_image = $default_bg_image;

    if (isset($chatbot) && !empty($chatbot['welcome_bg_image'])) {
        $chatbot_bg = $chatbot['welcome_bg_image'];
        if (!\Illuminate\Support\Str::startsWith($chatbot_bg, ['http://', 'https://'])) {
            $chatbot_bg = asset($chatbot_bg);
        }

        $bg_image = $chatbot_bg;
    }
@endphp



<div
    class="lqd-ext-chatbot-window-welcome-screen relative col-start-1 col-end-1 row-start-1 row-end-1 h-full w-full overflow-hidden bg-cover"
    @if ($is_editor)
        x-bind:style="{
            backgroundImage: activeChatbot && activeChatbot.welcome_bg_image ? `url(${activeChatbot.welcome_bg_image})` : 'url({{ $bg_image }})',
            backgroundPosition: 'top center'
        }"
    @else
        style="background-image: url({{ $bg_image }}); background-position: top center;"
    @endif
    x-show="currentView === 'welcome'"
    x-transition:enter="transition"
    x-transition:enter-start="opacity-0 translate-x-1"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-0"
>


    <div class="absolute start-0 top-0 h-full w-full bg-gradient-to-t from-[--lqd-ext-chat-foot-bg] from-25% to-transparent to-80%"></div>

    <div class="relative flex h-full flex-col gap-8 overflow-y-auto px-6 pb-28 pt-6">
        @include('chatbot::frontend-ui.components.welcome-banner')

        @if ($is_editor || (isset($chatbot) && ($chatbot->is_links || $chatbot->is_contact)))
            <div
                class="rounded-3xl border border-black/10 bg-white p-4 shadow-2xl"
                @if ($is_editor) x-show="activeChatbot.is_links || activeChatbot.is_contact" @endif
            >
                @include('chatbot::frontend-ui.components.channels-list')
            </div>
        @endif

        <div class="rounded-3xl border border-black/10 bg-white p-4 shadow-2xl">
            @include('chatbot::frontend-ui.components.conversations-list', ['recents' => true, 'take' => 3])
        </div>

        @if ($is_editor || (isset($chatbot) && $chatbot->is_articles))
            <div
                class="rounded-3xl border border-black/10 bg-white p-4 shadow-2xl"
                @if ($is_editor) x-show="activeChatbot.is_articles" @endif
            >
                @include('chatbot::frontend-ui.components.articles-list', ['recents' => true, 'take' => 3])
            </div>
        @endif
    </div>
</div>
