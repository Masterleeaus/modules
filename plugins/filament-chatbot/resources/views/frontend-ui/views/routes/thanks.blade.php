<template x-if="currentView === 'thanks'">
    <div
        class="col-start-1 col-end-1 row-start-1 row-end-1 h-full w-full p-5 [&.active]:motion-translate-x-in-[5px] [&.active]:motion-opacity-in-[0%] [&.active]:motion-duration-200"
        :class="{ 'active': currentView === 'thanks' }"
    >
        @include('chatbot::frontend-ui.components.thanks')
    </div>
</template>
