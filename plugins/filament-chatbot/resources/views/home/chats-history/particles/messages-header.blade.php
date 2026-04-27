<div class="lqd-ext-chatbot-history-head sticky top-0 z-1 h-14 w-full shrink-0 border-b bg-background px-4 lg:h-[--header-height] lg:border-b-0 lg:bg-transparent">
    <x-progressive-blur
        class="-bottom-12 hidden h-auto lg:block"
        dir="reverse"
    />

    <div
        class="relative z-1 flex h-full items-center justify-between gap-4 transition"
        :class="{ 'opacity-0': messagesSearchFormVisible }"
    >

        <div class="ms-auto hidden grow items-center justify-end gap-2 lg:flex">
            <x-button
                class="size-7 shrink-0 bg-foreground/5 shadow-[inset_1px_1px_1px_-0.5px_hsl(0_0%_100%/60%),inset_-1px_-1px_1px_-0.5px_hsl(0_0%_100%/40%),0_1px_2px_-1px_hsl(0_0%_0%/30%)] backdrop-blur backdrop-contrast-125"
                size="none"
                variant="none"
                title="{{ __('Search In Messages') }}"
                @click.prevent="messagesSearchFormVisible = !messagesSearchFormVisible"
            >
                <x-tabler-search class="size-4" />
            </x-button>

            <x-button
                class="size-7 shrink-0 bg-foreground/5 shadow-[inset_1px_1px_1px_-0.5px_hsl(0_0%_100%/60%),inset_-1px_-1px_1px_-0.5px_hsl(0_0%_100%/40%),0_1px_2px_-1px_hsl(0_0%_0%/30%)] backdrop-blur backdrop-contrast-125"
                size="none"
                variant="none"
                title="{{ __('Pin The Conversation') }}"
                @click.prevent="pinConversation(activeChat.id)"
            >
                <x-tabler-pin
                    class="size-4"
                    x-show="!activeChat?.pinned"
                />
                <x-tabler-pinned
                    class="size-4 fill-current"
                    x-cloak
                    x-show="activeChat?.pinned"
                />
            </x-button>

            <x-dropdown.dropdown
                anchor="end"
                triggerType="click"
                offsetY="10px"
            >
                <x-slot:trigger
                    class="size-7 shrink-0 bg-foreground/5 p-0 shadow-[inset_1px_1px_1px_-0.5px_hsl(0_0%_100%/60%),inset_-1px_-1px_1px_-0.5px_hsl(0_0%_100%/40%),0_1px_2px_-1px_hsl(0_0%_0%/30%)] backdrop-blur backdrop-contrast-125"
                    variant="none"
                    size="none"
                    title="{{ __('More Options') }}"
                >
                    <x-tabler-dots-vertical class="size-4" />
                </x-slot:trigger>

                <x-slot:dropdown
                    class="min-w-56 !rounded-2xl px-4 pb-1 pt-4 text-xs font-medium"
                >
                    <p class="mb-0 border-b pb-3 text-3xs/none font-semibold uppercase tracking-wider text-foreground/60">
                        {{ __('Actions') }}
                    </p>
                    <ul>
                        <li class="border-b last:border-b-0">
                            <x-button
                                class="w-full justify-start py-3 text-start"
                                href="#"
                                variant="link"
                                @click.prevent="deleteConversation(activeChat.id)"
                            >
                                {{ __('Delete') }}
                            </x-button>
                        </li>
                        <li class="border-b last:border-b-0">
                            <button
                                class="group m-0 flex w-full items-center justify-between gap-2 py-3 text-start text-xs font-medium"
                                type="button"
                                @click.prevent="showExportOptions = !showExportOptions"
                                :class="{ 'active': showExportOptions }"
                            >
                                {{ __('Export Conversation') }}

                                <x-tabler-chevron-down class="size-4 transition group-[&.active]:rotate-180" />
                            </button>
                            <div
                                x-cloak
                                x-show="showExportOptions"
                                x-collapse
                            >
                                @foreach ([['format' => 'txt', 'label' => __('Download as Text'), 'icon' => 'tabler-file-type-txt'], ['format' => 'csv', 'label' => __('Download as CSV'), 'icon' => 'tabler-file-type-csv'], ['format' => 'pdf', 'label' => __('Download as PDF'), 'icon' => 'tabler-file-type-pdf'], ['format' => 'json', 'label' => __('Download as JSON'), 'icon' => 'tabler-json']] as $exportFormat)
                                    <a
                                        class="flex items-center gap-2 py-2 text-xs font-medium hover:underline"
                                        :href="`/api/v2/chatbot/${activeChat?.chatbot?.uuid}/session/${activeChat?.session_id}/conversation/${activeChat?.id}/export?format={{ $exportFormat['format'] }}`"
                                    >
                                        <x-dynamic-component
                                            class="size-4"
                                            :component="$exportFormat['icon']"
                                        />

                                        {{ $exportFormat['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </x-slot:dropdown>
            </x-dropdown.dropdown>
        </div>

        <div class="ms-auto flex items-center gap-0.5 lg:hidden">
            <x-button
                class="size-8 [&.active]:bg-primary [&.active]:text-primary-foreground"
                variant="none"
                size="none"
                ::class="{ 'active': mobile.contactInfoVisible && contactInfo.activeTab === 'details' }"
                @click.prevent="mobile.filtersVisible = false; mobile.contactInfoVisible = mobile.contactInfoVisible && contactInfo.activeTab === 'details' ? false : true; contactInfo.activeTab = 'details';"
                aria-label="{{ __('Details') }}"
            >
                <svg
                    width="20"
                    height="21"
                    viewBox="0 0 20 21"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M4 18.55V18.5C4 17.4391 4.42143 16.4217 5.17157 15.6716C5.92172 14.9214 6.93913 14.5 8 14.5H12C13.0609 14.5 14.0783 14.9214 14.8284 15.6716C15.5786 16.4217 16 17.4391 16 18.5V18.55M10 11.5C10.7956 11.5 11.5587 11.1839 12.1213 10.6213C12.6839 10.0587 13 9.29565 13 8.5C13 7.70435 12.6839 6.94129 12.1213 6.37868C11.5587 5.81607 10.7956 5.5 10 5.5C9.20435 5.5 8.44129 5.81607 7.87868 6.37868C7.31607 6.94129 7 7.70435 7 8.5C7 9.29565 7.31607 10.0587 7.87868 10.6213C8.44129 11.1839 9.20435 11.5 10 11.5ZM10 1.5C17.2 1.5 19 3.3 19 10.5C19 17.7 17.2 19.5 10 19.5C2.8 19.5 1 17.7 1 10.5C1 3.3 2.8 1.5 10 1.5Z"
                    />
                </svg>
            </x-button>

            {{-- <x-button
                class="size-8 [&.active]:bg-primary [&.active]:text-primary-foreground"
                variant="none"
                size="none"
                ::class="{ 'active': mobile.contactInfoVisible && contactInfo.activeTab === 'history' }"
                @click.prevent="mobile.filtersVisible = false; mobile.contactInfoVisible = mobile.contactInfoVisible && contactInfo.activeTab === 'history' ? false : true; contactInfo.activeTab = 'history';"
                aria-label="{{ __('History') }}"
            >
                <svg
                    width="21"
                    height="22"
                    viewBox="0 0 21 22"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M19 10.5C18.9999 8.76145 18.4962 7.06014 17.5499 5.60171C16.6035 4.14329 15.255 2.99017 13.6674 2.28174C12.0797 1.5733 10.3208 1.33988 8.60333 1.60967C6.88584 1.87947 5.28325 2.64094 3.98927 3.80205C2.69529 4.96316 1.7653 6.4742 1.31171 8.15254C0.858119 9.83088 0.900347 11.6047 1.43329 13.2595C1.96623 14.9144 2.96707 16.3795 4.31484 17.4777C5.66261 18.5759 7.29962 19.2602 9.028 19.448C9.348 19.482 9.672 19.5 10 19.5M10 5.5V10.5L12 12.5M16.42 14.11C16.615 13.915 16.8465 13.7603 17.1013 13.6548C17.3561 13.5492 17.6292 13.4949 17.905 13.4949C18.1808 13.4949 18.4539 13.5492 18.7087 13.6548C18.9635 13.7603 19.195 13.915 19.39 14.11C19.585 14.305 19.7397 14.5365 19.8452 14.7913C19.9508 15.0461 20.0051 15.3192 20.0051 15.595C20.0051 15.8708 19.9508 16.1439 19.8452 16.3987C19.7397 16.6535 19.585 16.885 19.39 17.08L16 20.5H13V17.5L16.42 14.11Z"
                    />
                </svg>
            </x-button> --}}

            <x-button
                class="size-8 [&.active]:bg-primary [&.active]:text-primary-foreground"
                variant="none"
                size="none"
                ::class="{ 'active': mobile.filtersVisible }"
                @click.prevent="mobile.contactInfoVisible = false; mobile.filtersVisible = !mobile.filtersVisible"
                aria-label="{{ __('Filters') }}"
            >
                <x-tabler-dots
                    class="col-start-1 col-end-1 row-start-1 row-end-1 size-5"
                    x-show="!mobile.filtersVisible"
                />
                <x-tabler-x
                    class="col-start-1 col-end-1 row-start-1 row-end-1 size-5"
                    x-show="mobile.filtersVisible"
                    x-cloak
                />
            </x-button>
        </div>
    </div>

    <form
        class="absolute start-0 top-0 z-2 h-full w-full"
        action="#"
        @submit.prevent="handleMessagesSearch($event.target.elements.search)"
        x-cloak
        x-show="messagesSearchFormVisible"
        @keyup.escape.window="messagesSearchFormVisible = false"
        x-trap="messagesSearchFormVisible"
        x-transition
    >
        <x-forms.input
            class="h-full w-full rounded-none border-none bg-transparent pe-6 font-medium text-heading-foreground placeholder:text-heading-foreground focus:ring-0 sm:ps-12 lg:pe-12"
            containerClass="h-full"
            @keyup.throttle.100ms="handleMessagesSearch($event.target)"
            type="search"
            name="search"
            placeholder="{{ __('Search in messages...') }}"
            x-ref="historySearchInput"
        />
        <x-tabler-search class="pointer-events-none absolute start-5 top-1/2 size-[18px] -translate-y-1/2" />

        <x-button
            class="absolute end-4 top-1/2 size-6 -translate-y-1/2 bg-foreground/10 text-foreground shadow-[inset_1px_1px_1px_-0.5px_hsl(0_0%_100%/60%),inset_-1px_-1px_1px_-0.5px_hsl(0_0%_100%/40%),0_1px_2px_-1px_hsl(0_0%_0%/30%)] backdrop-blur backdrop-contrast-125 hover:-translate-y-1/2 hover:scale-105"
            hover-variant="danger"
            @click.prevent="messagesSearchFormVisible = false"
            size="none"
        >
            <x-tabler-x class="size-4" />
        </x-button>
    </form>
</div>
