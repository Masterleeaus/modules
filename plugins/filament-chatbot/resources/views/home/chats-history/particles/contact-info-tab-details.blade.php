<div
    class="col-start-1 col-end-1 row-start-1 row-end-1 w-full px-4 py-6 text-center"
    x-show="contactInfo.activeTab === 'details'"
    x-transition.opacity
>
    <div class="mx-auto mb-3 size-[90px]">
        <figure
            class="relative grid size-full place-items-center rounded-full text-3xl/none font-semibold text-white"
            :style="{ 'backgroundColor': activeChat?.color ?? '#e633ec' }"
        >
            <img
                class="col-start-1 col-end-1 row-start-1 row-end-1 size-full object-cover object-center"
                :src="activeChat?.avatar"
                x-cloak
                x-show="activeChat?.avatar"
            >

            <span
                class="col-start-1 col-end-1 row-start-1 row-end-1"
                x-show="!activeChat?.avatar"
                x-text="(activeChat?.conversation_name ?? '{{ __('Anonymous User') }}').split('')?.at(0)"
            >-</span>

            {{-- <x-button
                class="absolute -end-1 bottom-0 inline-grid size-7 place-items-center bg-background p-0 text-foreground"
                size="none"
                hover-variant="primary"
                @click.prevent="contactInfo.editMode = !contactInfo.editMode;
					$nextTick( () => {
						if ( contactInfo.editMode ) {
							$refs.contactInfoName.focus()
						} else {
							updateConversationDetails({name: $refs.contactInfoName.textContent})
						}
					})
				"
            >
                <span class="sr-only">
                    {{ __('Edit User Details') }}
                </span>
                <x-tabler-pencil
                    class="col-start-1 col-end-1 row-start-1 row-end-1 size-4"
                    x-show="!contactInfo.editMode"
                />
                <x-tabler-check
                    class="col-start-1 col-end-1 row-start-1 row-end-1 size-4"
                    x-cloak
                    x-show="contactInfo.editMode"
                />
            </x-button> --}}
        </figure>
    </div>

    <h3
        class="mb-2.5"
        x-text="activeChat?.conversation_name ?? '{{ __('Anonymous User') }}'"
        {{-- :contenteditable="contactInfo.editMode"
        x-ref="contactInfoName"
        @keydown.enter="contactInfo.editMode = false; $el.blur();"
        @keydown.esc="contactInfo.editMode = false; $el.blur()"
        @dblclick.prevent="contactInfo.editMode = true; $nextTick( () => $refs.contactInfoName.focus())"
        @blur="contactInfo.editMode = false; $refs.contactInfoName.textContent.trim() !== activeChat.conversation_name && updateConversationDetails({name: $refs.contactInfoName.textContent})" --}}
    >
        ---
    </h3>

    <div class="mb-2.5 flex items-center justify-center gap-3 font-medium opacity-70">
        {{ __('Channel') }}
        <span class="inline-block size-0.5 rounded-full bg-current"></span>
        <span
            x-text="activeChat?.chatbot_channel && activeChat.chatbot_channel === 'frame' ? '{{ __('Live Chat') }}' : activeChat?.chatbot_channel ? activeChat.chatbot_channel : '---'"
        ></span>
    </div>

    <p class="mb-7 flex items-center justify-center gap-1 font-medium text-blue-500">
        <x-tabler-at class="size-4" />
        <span x-text="activeChat?.ip_address">
            ---
        </span>
    </p>

    @php
        $hasCustomerTagExtension = $hasCustomerTagExtension ?? \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-customer-tag');
    @endphp

    @if ($hasCustomerTagExtension)
        <div class="relative mb-5 flex justify-center">
            <div
                class="flex max-w-full items-center justify-center gap-2"
                @click.outside="if (!$event.target.closest('.cp_dialog')) customerTagModal.show = false"
            >
                <template x-if="activeChat?.customer_tags?.length">
                    <div class="no-scrollbar flex gap-2 overflow-x-auto whitespace-nowrap">
                        <template
                            x-for="tag in activeChat?.customer_tags ?? []"
                            :key="tag.id"
                        >
                            <div
                                class="relative z-1 inline-flex shrink-0 cursor-default items-center gap-1 overflow-hidden rounded-md px-2.5 py-1 text-[12px] font-medium text-[--color] before:absolute before:inset-0 before:-z-1 before:bg-[--color] before:opacity-15"
                                :style="{ '--color': tag.tag_color ?? '#eef2ff' }"
                                @click.prevent="toggleCustomTagModal"
                                x-text="tag.tag"
                            ></div>
                        </template>
                    </div>
                </template>

                <span
                    class="inline-flex rounded-md bg-foreground/5 px-2.5 py-1 text-[12px] font-medium"
                    x-show="!activeChat?.customer_tags?.length ?? false"
                    @click.prevent="toggleCustomTagModal"
                >
                    {{ __('No tags assigned yet') }}
                </span>

                <x-button
                    class="size-[26px] shrink-0 rounded-md bg-foreground/5 p-0"
                    size="none"
                    variant="ghost-shadow"
                    title="{{ __('Add or edit tags') }}"
                    @click.prevent="toggleCustomTagModal"
                >
                    <x-tabler-plus class="size-4" />
                </x-button>

                <div
                    class="absolute inset-x-0 top-full z-10 mt-2.5 max-h-72 overflow-y-auto overscroll-contain rounded-dropdown border border-dropdown-border bg-dropdown-background px-4 py-6 shadow-xl shadow-black/5"
                    x-cloak
                    x-show="customerTagModal.show"
                    x-transition.origin.top
                    x-trap="customerTagModal.show"
                >

                    <form
                        class="relative mb-1"
                        @submit.prevent="createCustomerTag"
                    >
                        <div
                            class="absolute start-4 top-1/2 z-2 flex -translate-y-1/2 items-center before:absolute before:-inset-2.5 before:left-1/2 before:top-1/2 before:-translate-x-1/2 before:-translate-y-1/2"
                            x-data="liquidColorPicker({ colorVal: customerTagModal.form.tag_color })"
                        >
                            <span
                                class="lqd-input-color-wrap !size-3.5 shrink-0 rounded-full !border-[3px] !border-background shadow-md shadow-black/10"
                                x-ref="colorInputWrap"
                                :style="{ backgroundColor: colorVal }"
                            ></span>
                            <input
                                class="invisible w-0 border-none bg-transparent p-0 text-3xs font-medium focus:outline-none"
                                type="text"
                                size="sm"
                                value="#ffffff"
                                x-ref="colorInput"
                                :value="customerTagModal.form.tag_color"
                                @input="customerTagModal.form.tag_color = $event.target.value"
                                @change="picker.setColor($event.target.value)"
                                @keydown.enter.prevent="picker?.setColor($event.target.value);"
                                @focus="picker.open(); $el.select();"
                            />
                        </div>
                        <x-forms.input
                            class="min-h-11 w-full rounded-lg bg-foreground/5 px-10 py-3.5"
                            type="text"
                            placeholder="{{ __('Add New Segment') }}"
                            x-model="customerTagModal.form.tag"
                        />

                        <x-button
                            class="absolute end-4 top-1/2 size-5 -translate-y-1/2 p-0 hover:-translate-y-1/2 hover:scale-105"
                            size="lg"
                            variant="none"
                            size="none"
                            type="submit"
                            ::disabled="customerTagModal.creating"
                            @click.prevent="createCustomerTag"
                            ::title="customerTagModal.creating ? '{{ __('Creating...') }}' : '{{ __('Create Tag') }}'"
                        >
                            <x-tabler-plus
                                class="size-4"
                                x-show="!customerTagModal.creating && !customerTagModal.form.tag.trim()"
                            />
                            <x-tabler-check
                                class="size-4"
                                x-show="!customerTagModal.creating && customerTagModal.form.tag.trim()"
                            />
                            <x-tabler-loader-2
                                class="size-4 animate-spin"
                                x-show="customerTagModal.creating"
                            />
                        </x-button>
                    </form>

                    <p
                        class="mb-0 mt-5 text-2xs font-medium"
                        x-show="customerTagModal.loading"
                    >
                        {{ __('Loading tags...') }}
                    </p>

                    <div
                        x-show="!customerTagModal.loading"
                        x-cloak
                    >
                        <template
                            x-for="tag in customerTagModal.items"
                            :key="tag.id"
                        >
                            <button
                                class="mb-0.5 flex w-full items-center justify-between gap-1 rounded-lg p-3 transition last:mb-0 hover:bg-foreground/[3%] [&.active]:bg-foreground/10"
                                type="button"
                                :class="{ 'active': customerTagModal.selected.includes(tag.id) }"
                                @click.prevent="toggleCustomerTagSelection(tag.id)"
                                :title="tag.tag"
                            >
                                <span class="inline-flex items-center gap-2">
                                    <span
                                        class="size-2 shrink-0 rounded-sm"
                                        :style="{ backgroundColor: tag.tag_color }"
                                    ></span>
                                    <span
                                        class="w-full grow truncate"
                                        x-text="tag.tag"
                                    ></span>
                                </span>

                                <x-tabler-check
                                    class="size-4"
                                    x-show="customerTagModal.selected.includes(tag.id)"
                                />
                            </button>
                        </template>

                        <p
                            class="mb-0 mt-5 text-2xs font-medium"
                            x-show="!customerTagModal.items.length"
                            x-cloak
                        >
                            {{ __('No tags yet. Use the form below to create one.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <p class="-mx-4 mb-5 flex items-center justify-center gap-10">
        <span class="inline-block h-px grow bg-current opacity-5"></span>
        {{ __('Details') }}
        <span class="inline-block h-px grow bg-current opacity-5"></span>
    </p>

    <div class="rounded-lg border px-4 xl:mx-4">
        <p class="mb-0 flex items-center justify-between gap-1 border-b py-4 text-foreground/60">
            {{ __('ID') }}
            <span
                class="max-w-[65%] overflow-hidden text-ellipsis whitespace-nowrap text-end capitalize text-foreground"
                x-text="activeChat?.id ?? '---'"
            ></span>
        </p>
        <p class="mb-0 flex items-center justify-between gap-1 border-b py-4 text-foreground/60">
            {{ __('Status') }}
            <span
                class="max-w-[65%] overflow-hidden text-ellipsis whitespace-nowrap text-end capitalize text-foreground"
                x-text="activeChat?.ticket_status ?? '---'"
            ></span>
        </p>
        <p class="mb-0 flex items-center justify-between gap-1 border-b py-4 text-foreground/60">
            {{ __('Created') }}
            <span
                class="max-w-[65%] overflow-hidden text-ellipsis whitespace-nowrap text-end capitalize text-foreground"
                x-text="activeChat?.created_at ? getDiffHumanTime(activeChat.created_at) : '---'"
            ></span>
        </p>
        <p class="mb-0 flex items-center justify-between gap-1 border-b py-4 text-foreground/60">
            {{ __('Updated') }}
            <span
                class="max-w-[65%] overflow-hidden text-ellipsis whitespace-nowrap text-end capitalize text-foreground"
                x-text="activeChat?.updated_at ? getDiffHumanTime(activeChat.updated_at) : '---'"
            ></span>
        </p>
        <p class="mb-0 flex items-center justify-between gap-1 border-b py-4 text-foreground/60">
            {{ __('Country') }}
            <span
                class="max-w-[65%] overflow-hidden text-ellipsis whitespace-nowrap text-end capitalize text-foreground"
                x-text="activeChat?.country_code ?? '---'"
            ></span>
        </p>
        <p class="mb-0 flex items-center justify-between gap-1 py-4 text-foreground/60">
            {{ __('IP Address') }}
            <span
                class="max-w-[65%] overflow-hidden text-ellipsis whitespace-nowrap text-end capitalize text-foreground"
                x-text="activeChat?.ip_address ?? '---'"
            ></span>
        </p>
    </div>
</div>
