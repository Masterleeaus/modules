<div
    class="lqd-ext-chatbot-window-routes-view col-start-1 col-end-1 row-start-1 row-end-1 h-full w-full overflow-y-auto pb-24"
    x-ref="routesView"
    x-cloak
    x-show="currentView !== 'welcome'"
    x-transition:enter="transition"
    x-transition:enter-start="opacity-0 translate-x-1"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-0"
>
    @include('chatbot::frontend-ui.components.header')

    <div class="grid grid-cols-1 place-items-start">
        @include('chatbot::frontend-ui.views.routes.messages')
        @include('chatbot::frontend-ui.views.routes.conversations')
        @include('chatbot::frontend-ui.views.routes.articles')
        @include('chatbot::frontend-ui.views.routes.article-show')
        @include('chatbot::frontend-ui.views.routes.contact-form')
        @include('chatbot::frontend-ui.views.routes.thanks')
		@includeIf('chatbot-booking::particles.iframe')
    </div>
</div>
