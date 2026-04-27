<div class="flex shrink-0 justify-between gap-1 border-b px-4 py-5 lg:py-6 xl:px-6">
    <x-dropdown.dropdown
        class:dropdown-dropdown="max-lg:end-auto max-lg:start-0 max-sm:-left-20"
        offsetY="10px"
        triggerType="click"
    >
        <x-slot:trigger
            class="gap-2 font-heading text-[12px] font-medium capitalize"
            variant="link"
        >
            <span x-text="filters.sort">
                {{ $sort_filters['newest']['label'] }}
            </span>

            <x-tabler-chevron-down class="size-4" />
        </x-slot:trigger>

        <x-slot:dropdown
            class="min-w-44 p-2 text-xs font-medium"
        >
            <ul class="space-y-px">
                @foreach ($sort_filters as $key => $filter)
                    <li>
                        <a
                            @class([
                                'group flex items-center justify-between gap-1 rounded px-2.5 py-1.5 text-2xs font-medium transition hover:bg-foreground/5 [&.active]:bg-primary/5',
                                'active' => $key === 'newest',
                            ])
                            href="#"
                            @click.prevent="filterSort('{{ $key }}')"
                            :class="{ active: filters.sort === '{{ $key }}' }"
                        >
                            {{ $filter['label'] }}
                            <x-tabler-check class="hidden size-4 text-primary group-[&.active]:block" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </x-slot:dropdown>
    </x-dropdown.dropdown>

    <x-forms.input
        class="border-foreground/10"
        class:label="text-[12px] text-foreground font-medium flex-row-reverse"
        type="checkbox"
        switcher
        label="{{ __('Unread') }}"
        size="sm"
        @change.prevent="filterUnread"
        ::checked="filters.unreadsOnly"
    />
</div>
