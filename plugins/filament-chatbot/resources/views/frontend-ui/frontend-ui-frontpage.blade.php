@vite('app/Extensions/Chatbot/resources/assets/scss/external-chatbot.scss')
@vite('app/Extensions/Chatbot/resources/assets/scss/external-chatbot-tw.scss')

@php
    $style = '';

    if (!empty($chatbot['color'])) {
        $style .= '--lqd-ext-chat-primary: ' . $chatbot['color'] . ';';
    }
@endphp

<div
    class="lqd-ext-chatbot"
    data-pos-x="{{ $chatbot['position'] ?? 'right' }}"
    data-pos-y="{{ $chatbot['position_y'] ?? 'bottom' }}"
    data-window-state="{{ $is_iframe ? 'open' : 'close' }}"
    data-embedded="true"
    data-fetching="true"
    x-data="externalChatbot"
    :data-fetching="fetching ? 'true' : 'false'"
    @if ($style) style="{{ $style }}" @endif
>
    <div
        class="lqd-ext-chatbot-window before:pointer-events-none before:absolute before:bottom-0 before:z-3 before:h-40 before:w-full before:bg-gradient-to-t before:from-[--lqd-ext-chat-window-bg] before:from-40% before:to-transparent before:to-85%">
        <div class="lqd-ext-chatbot-window-contents-wrap grid grow place-items-start overflow-hidden">
            @include('chatbot::frontend-ui.views.welcome')
            @include('chatbot::frontend-ui.views.routes')
            @include('chatbot::frontend-ui.components.loader')
        </div>

        @include('chatbot::frontend-ui.components.floating-bar')

        @include('chatbot::frontend-ui.components.footer')
    </div>

    @include('chatbot::frontend-ui.components.trigger-button')
</div>

<link
    rel="stylesheet"
    href="{{ custom_theme_url('/assets/libs/prism/prism.css') }}"
/>
<link
    rel="stylesheet"
    href="{{ custom_theme_url('/assets/libs/picmo/picmo.min.css') }}"
/>
<script src="{{ custom_theme_url('/assets/libs/prism/prism.js') }}"></script>
<script src="{{ custom_theme_url('/assets/libs/beautify-html.min.js') }}"></script>
<script src="{{ custom_theme_url('/assets/libs/markdownit/markdown-it.min.js') }}"></script>
<script src="{{ custom_theme_url('/assets/libs/turndown.js') }}"></script>
<script src="{{ custom_theme_url('/assets/libs/picmo/picmo.min.js') }}"></script>
<script
    defer
    src="{{ asset('vendor/chatbot/js/alpine.min.js') }}"
></script>

@if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-agent'))
    <script
        src="https://cdn.ably.com/lib/ably.min-1.js"
        type="text/javascript"
    ></script>
@endif

@include('chatbot::frontend-ui.frontend-ui-scripts', ['is_editor' => false])
