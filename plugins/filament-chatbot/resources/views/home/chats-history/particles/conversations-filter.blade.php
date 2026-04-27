<div class="relative flex h-[--header-height] shrink-0 items-center gap-2.5 border-b px-4">
    <x-button
        class="flex aspect-square size-8 bg-foreground/10 text-foreground"
        hover-variant="primary"
        size="none"
        aria-label="{{ __('Back to Dashboard') }}"
        href="#"
        @click.prevent="setOpen(false)"
    >
        <x-tabler-chevron-left class="size-5" />
    </x-button>

    <p class="m-0 flex gap-1 font-heading text-base font-bold text-heading-foreground sm:gap-2">
        <svg
            width="18"
            height="19"
            viewBox="0 0 18 19"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M2 18.5C1.45 18.5 0.979167 18.3042 0.5875 17.9125C0.195833 17.5208 0 17.05 0 16.5V2.5C0 1.95 0.195833 1.47917 0.5875 1.0875C0.979167 0.695833 1.45 0.5 2 0.5H16C16.55 0.5 17.0208 0.695833 17.4125 1.0875C17.8042 1.47917 18 1.95 18 2.5V16.5C18 17.05 17.8042 17.5208 17.4125 17.9125C17.0208 18.3042 16.55 18.5 16 18.5H2ZM9 13.5C9.63333 13.5 10.2083 13.3167 10.725 12.95C11.2417 12.5833 11.6 12.1 11.8 11.5H16V2.5H2V11.5H6.2C6.4 12.1 6.75833 12.5833 7.275 12.95C7.79167 13.3167 8.36667 13.5 9 13.5Z"
            />
        </svg>
        {{ __('Messages') }}
    </p>

    <div class="ms-auto flex gap-2">
        <x-dropdown.dropdown
            anchor="end"
            triggerType="click"
            offsetY="10px"
        >
            <x-slot:trigger
                class="size-7 shrink-0 bg-foreground/5 p-0 hover:-translate-y-0.5"
                variant="none"
                title="{{ __('Filter Agents') }}"
            >
                <svg
                    width="14"
                    height="9"
                    viewBox="0 0 14 9"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path d="M5.58333 8.75V7.33333H8.41667V8.75H5.58333ZM2.75 5.20833V3.79167H11.25V5.20833H2.75ZM0.625 1.66667V0.25H13.375V1.66667H0.625Z" />
                </svg>
            </x-slot:trigger>

            <x-slot:dropdown
                class="min-w-44 p-2 text-xs font-medium"
            >
                <ul>
                    @foreach ($agent_filters as $key => $filter)
                        <li>
                            <a
                                class='group flex items-center justify-between gap-1 rounded px-2.5 py-1.5 text-2xs font-medium transition hover:bg-foreground/5 [&.active]:bg-primary/5'
                                href="#"
                                @click.prevent="filterAgent('{{ $key }}')"
                                :class="{ active: filters.agent === '{{ $key }}' }"
                            >
                                {{ $filter['label'] }}
                                <x-tabler-check class="hidden size-4 text-primary group-[&.active]:block" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-slot:dropdown>
        </x-dropdown.dropdown>

        <x-dropdown.dropdown
            anchor="end"
            triggerType="click"
            offsetY="10px"
        >
            <x-slot:trigger
                class="size-7 shrink-0 bg-foreground/5 p-0 hover:-translate-y-0.5"
                variant="none"
                title="{{ __('Export Conversations') }}"
            >
                <x-tabler-download class="size-4" />
            </x-slot:trigger>

            <x-slot:dropdown
                class="min-w-44 p-2 text-xs font-medium"
            >
                <ul>
                    <li>
                        <a
                            class='group flex cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-2xs font-medium transition hover:bg-foreground/5'
                            href="#"
                            @click.prevent="exportConversations('csv')"
                        >
                            <x-tabler-file-spreadsheet class="size-4" />
                            {{ __('Export as CSV') }}
                        </a>
                    </li>
                    <li>
                        <a
                            class='group flex cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-2xs font-medium transition hover:bg-foreground/5'
                            href="#"
                            @click.prevent="exportConversations('json')"
                        >
                            <x-tabler-file-code class="size-4" />
                            {{ __('Export as JSON') }}
                        </a>
                    </li>
                    <li>
                        <a
                            class='group flex cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-2xs font-medium transition hover:bg-foreground/5'
                            href="#"
                            @click.prevent="exportConversations('pdf')"
                        >
                            <x-tabler-file-text class="size-4" />
                            {{ __('Export as PDF') }}
                        </a>
                    </li>
                </ul>
            </x-slot:dropdown>
        </x-dropdown.dropdown>

        <x-button
            class="size-7 shrink-0 bg-foreground/5"
            size="none"
            variant="none"
            title="{{ __('Filter by Date') }}"
            @click.prevent="Alpine.$data(document.querySelector('#date-range-modal')).modalOpen = true;"
            ::class="{ 'bg-primary text-primary-foreground': filters.dateRange.start || filters.dateRange.end }"
        >
            <x-tabler-calendar class="size-4" />
        </x-button>

        <x-button
            class="size-7 shrink-0 bg-foreground/5"
            size="none"
            variant="none"
            title="{{ __('Search') }}"
            @click.prevent="conversationsSearchFormVisible = !conversationsSearchFormVisible"
        >
            <x-tabler-search class="size-4" />
        </x-button>
    </div>

    <form
        class="absolute start-0 top-0 h-full w-full bg-background"
        action="#"
        x-ref="convSearchForm"
        @submit.prevent="handleConversationsSearch()"
        x-cloak
        x-show="conversationsSearchFormVisible"
        @keyup.escape.window="conversationsSearchFormVisible = false"
        x-trap="conversationsSearchFormVisible"
        x-transition
    >
        <input
            class="lqd-input h-full w-full rounded-none border-none bg-transparent pe-6 ps-10 font-medium text-input-foreground outline-none focus:ring-0 sm:ps-12 lg:pe-12"
            type="search"
            name="conv_search"
            placeholder="{{ __('Search for chats...') }}"
        />
        <x-tabler-search class="pointer-events-none absolute start-4 top-1/2 size-[18px] -translate-y-1/2 sm:start-5" />

        <x-button
            class="absolute end-4 top-1/2 size-6 -translate-y-1/2 bg-foreground/15 text-foreground hover:-translate-y-1/2 hover:scale-105"
            hover-variant="danger"
            @click.prevent="conversationsSearchFormVisible = false"
            size="none"
        >
            <x-tabler-x class="size-4" />
        </x-button>
    </form>
</div>

{{-- Date Range Filter Modal --}}
<x-modal
    class:modal-body="p-5"
    class:modal-content="w-[min(100%,400px)]"
    id="date-range-modal"
>
    <x-slot:modal>
        <div class="mb-4 [&_.air-datepicker]:w-full">
            <input
                class="hidden"
                id="conversationsDatepicker"
                type="text"
                x-ref="datepicker"
            />
        </div>

        <div class="flex items-center justify-between gap-3">
            <x-button
                class="flex-1"
                variant="outline"
                @click.prevent="clearDateRange(); modalOpen = false;"
            >
                {{ __('Clear') }}
            </x-button>
            <x-button
                class="flex-1"
                variant="primary"
                @click.prevent="applyDateRange(); modalOpen = false;"
            >
                {{ __('Apply') }}
            </x-button>
        </div>
    </x-slot:modal>
</x-modal>
