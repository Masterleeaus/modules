<template x-if="currentView === 'conversation-messages'">
    <div
        class="[_>div:first-of-type]:!mt-0 col-start-1 col-end-1 row-start-1 row-end-1 w-full space-y-7 px-5 [&.active]:motion-translate-x-in-[5px] [&.active]:motion-opacity-in-[0%] [&.active]:motion-duration-200"
        x-ref="conversationMessagesContainer"
        :class="{ 'active': currentView === 'conversation-messages' }"
    >
        @include('chatbot::frontend-ui.components.conversation-messages')
    </div>
</template>
