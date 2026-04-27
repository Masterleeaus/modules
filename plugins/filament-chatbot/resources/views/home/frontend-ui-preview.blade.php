{{-- This is the frontend ui but with some changes for the editor --}}

@push('css')
    <style>
        .lqd-chatbot-preview .lqd-ext-chatbot-window {
            width: var(--lqd-ext-chat-window-w);
            height: min(var(--lqd-ext-chat-window-h), calc(100vh - var(--lqd-ext-chat-offset-y, 30px) - var(--lqd-ext-chat-trigger-h, 60px) - 110px));
        }
    </style>
@endpush

<div class="lqd-chatbot-preview sticky top-24">
    @include('chatbot::frontend-ui.frontend-ui', [
        'is_editor' => true,
        'is_iframe' => false,
    ])
</div>
