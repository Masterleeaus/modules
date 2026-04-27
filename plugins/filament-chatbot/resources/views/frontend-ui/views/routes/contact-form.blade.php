<template x-if="currentView === 'contact-form'">
    <div
        class="col-start-1 col-end-1 row-start-1 row-end-1 w-full p-5 [&.active]:motion-translate-x-in-[5px] [&.active]:motion-opacity-in-[0%] [&.active]:motion-duration-200"
        :class="{ 'active': currentView === 'contact-form' }"
    >
        @include('chatbot::frontend-ui.components.contact-form')
    </div>
</template>
