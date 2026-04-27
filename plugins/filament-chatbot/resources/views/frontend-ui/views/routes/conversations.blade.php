<template x-if="currentView === 'conversations-list'">
    <div
        class="col-start-1 col-end-1 row-start-1 row-end-1 w-full space-y-7 p-5 [&.active]:motion-translate-x-in-[5px] [&.active]:motion-opacity-in-[0%] [&.active]:motion-duration-200"
        :class="{ 'active': currentView === 'conversations-list' }"
    >
        @include('chatbot::frontend-ui.components.conversations-list')
    </div>
</template>
