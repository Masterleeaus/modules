@push('before-head-close')
    @vite('app/Extensions/Chatbot/resources/assets/scss/external-chatbot.scss')
    <link
        rel="stylesheet"
        href="{{ custom_theme_url('/assets/libs/picmo/picmo.min.css') }}"
    />
@endpush

<div
    class="lqd-ext-chatbot"
    data-pos-x="right"
    data-pos-y="bottom"
    data-window-state="open"
    data-embedded="false"
    x-data="externalChatbot"
    :data-pos-x="activeChatbot.position"
    :style="{
        '--lqd-ext-chat-primary': activeChatbot.color,
        '--lqd-ext-chat-trigger-background': activeChatbot.trigger_background,
        '--lqd-ext-chat-window-w': `${testIframeWidth}px`,
        '--lqd-ext-chat-window-h': `${testIframeHeight}px`,
    }"
>
    <div
        class="lqd-ext-chatbot-window before:pointer-events-none before:absolute before:bottom-0 before:z-3 before:h-40 before:w-full before:bg-gradient-to-t before:from-[--lqd-ext-chat-window-bg] before:from-40% before:to-transparent before:to-85%">
        <div class="lqd-ext-chatbot-window-contents-wrap grid grow place-items-start overflow-hidden">
            @include('chatbot::frontend-ui.views.welcome')
            @include('chatbot::frontend-ui.views.routes')
        </div>

        @include('chatbot::frontend-ui.components.floating-bar')

        @include('chatbot::frontend-ui.components.footer')
    </div>

    @include('chatbot::frontend-ui.components.trigger-bubble')

    @include('chatbot::frontend-ui.components.trigger-button')
</div>

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/picmo/picmo.min.js') }}"></script>

    @include('chatbot::frontend-ui.frontend-ui-scripts', ['is_editor' => true])
@endpush
