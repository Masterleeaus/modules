@if (!isset($recents))
    <a
        class="flex items-center justify-center gap-3 px-4 pb-6 text-center text-xs text-[--lqd-ext-chat-primary] underline underline-offset-4"
        href="#"
        @click.prevent="toggleView('contact-form')"
    >
        {{-- blade-formatter-disable --}}
		<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg" > <path d="M12.75 5C12.125 5 11.5938 4.78125 11.1562 4.34375C10.7188 3.90625 10.5 3.375 10.5 2.75C10.5 2.125 10.7188 1.59375 11.1562 1.15625C11.5938 0.71875 12.125 0.5 12.75 0.5C13.375 0.5 13.9062 0.71875 14.3438 1.15625C14.7812 1.59375 15 2.125 15 2.75C15 3.375 14.7812 3.90625 14.3438 4.34375C13.9062 4.78125 13.375 5 12.75 5ZM0 17V3.5C0 3.0875 0.146875 2.73438 0.440625 2.44063C0.734375 2.14688 1.0875 2 1.5 2H9.075C9.025 2.25 9 2.5 9 2.75C9 3 9.025 3.25 9.075 3.5C9.25 4.375 9.68125 5.09375 10.3687 5.65625C11.0562 6.21875 11.85 6.5 12.75 6.5C13.15 6.5 13.5438 6.4375 13.9313 6.3125C14.3188 6.1875 14.675 6 15 5.75V12.5C15 12.9125 14.8531 13.2656 14.5594 13.5594C14.2656 13.8531 13.9125 14 13.5 14H3L0 17Z" /> </svg>
		{{-- blade-formatter-enable --}}
        {{ __('Need help? Leave a message') }}
    </a>
@endif

<form
    class="relative mb-1 w-full"
    action="#"
    x-show="articles?.length"
    @submit.prevent="onArticlesSearch($event.target.elements.s.value)"
>
    <input
        class="h-10 w-full rounded-lg bg-black/5 px-4 text-base"
        type="text"
        name="s"
        placeholder="{{ __('Search for articles') }}"
        @input.throttle.100ms="onArticlesSearch($event.target.value)"
    >
    <div class="absolute end-2 top-1/2 inline-grid size-8 -translate-y-1/2 place-items-center">
        {{-- blade-formatter-disable --}}
		<svg class="col-start-1 col-end-1 row-start-1 row-end-1" x-show="!searchingArticles" width="14" height="13" viewBox="0 0 14 13" fill="currentColor" xmlns="http://www.w3.org/2000/svg" > <path fill-rule="evenodd" clip-rule="evenodd" d="M6.14547 1.34164C4.04008 1.34164 2.33331 3.0484 2.33331 5.1538C2.33331 7.25922 4.04008 8.96597 6.14547 8.96597C8.25089 8.96597 9.95764 7.25922 9.95764 5.1538C9.95764 3.0484 8.25089 1.34164 6.14547 1.34164ZM0.983307 5.1538C0.983307 2.30282 3.2945 -0.00836182 6.14547 -0.00836182C8.99647 -0.00836182 11.3076 2.30282 11.3076 5.1538C11.3076 8.0048 8.99647 10.316 6.14547 10.316C3.2945 10.316 0.983307 8.0048 0.983307 5.1538ZM10.5143 9.52264C10.778 9.25906 11.2053 9.25906 11.469 9.52264L13.8023 11.856C14.0659 12.1196 14.0659 12.547 13.8023 12.8106C13.5386 13.0742 13.1113 13.0742 12.8476 12.8106L10.5143 10.4773C10.2507 10.2136 10.2507 9.78631 10.5143 9.52264Z" /> </svg>
		{{-- blade-formatter-enable --}}
        <x-tabler-loader-2
            class="col-start-1 col-end-1 row-start-1 row-end-1 animate-spin"
            x-cloak
            x-show="searchingArticles"
        />
    </div>
</form>

<template @if (!isset($take)) x-for="article in articles"@else x-for="article in articles.slice(0, {{ $take }})" @endif>
    <div class="lqd-ext-chatbot-window-articles-list-item group relative py-4">
        <h4
            class="lqd-ext-chatbot-window-articles-list-item-info-name m-0 mb-2 text-2xs font-semibold text-black"
            x-text="article.title"
        ></h4>
        <p
            class="lqd-ext-chatbot-window-articles-list-item-info-last-message m-0 line-clamp-2 w-full overflow-hidden text-ellipsis text-2xs opacity-70"
            x-text="article.excerpt"
        ></p>

        <a
            class="absolute -inset-x-2 -bottom-px top-0 z-1 scale-95 rounded-xl transition-all hover:bg-black/5 group-hover:scale-100"
            href="#"
            @click.prevent="showArticle(article.id)"
        ></a>
    </div>
</template>

<h4
    class="lqd-ext-chatbot-window-articles-list-no-articles mb-0 py-4 text-sm font-semibold"
    x-show="!articles.length"
    x-cloak
>
    {{ __('No articles yet.') }}
</h4>
