@php
    $predefined_colors = ['272733', '67D97C', 'E7AC47', '9D74C9', '017BE5'];
@endphp

{{-- Editing Step 2 - Customize --}}
<div
    class="col-start-1 col-end-1 row-start-1 row-end-1 transition-all"
    data-step="2"
    x-show="editingStep === 2"
    x-transition:enter-start="opacity-0 -translate-x-3"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-3"
>
    <h2 class="mb-3.5">
        @lang('Customize')
    </h2>
    <p class="text-xs/5 opacity-60 lg:max-w-[360px]">
        @lang('Create and configure a chatbot that interacts with your users, ensuring it delivers accurate information.')
    </p>

    <div class="flex flex-col gap-7 pt-9">
        <div x-data="logoPreviewHandler">
            <x-forms.input
                class:label="text-heading-foreground"
                label="{{ __('Upload Logo') }}"
                name="logo"
                type="file"
                size="lg"
                x-bind="logoPicker"
                @change="externalChatbot && externalChatbot.toggleWindowState('open');"
            />
            <input
                type="hidden"
                x-model="uploadedLogo"
                x-modelable="activeChatbot.logo"
            >

            <template
                x-for="(error, index) in formErrors.logo"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="text-heading-foreground"
                label="{{ __('Upload Welcome Background') }}"
                name="welcome_bg_image"
                type="file"
                size="lg"
                accept="image/*"
                @change="
                    const file = $event.target.files[0];
                    if (!file) return;
                    activeChatbot.welcome_bg_image_blob = file;
                    activeChatbot.welcome_bg_image = URL.createObjectURL(file);
                    if (externalChatbot) {
                        externalChatbot.toggleWindowState('open');
                        externalChatbot.toggleView('welcome');
                    }
                "
            />
            <template
                x-for="(error, index) in formErrors.welcome_bg_image_blob"
                :key="'welcome-bg-error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <label
                class="mb-5 block w-full cursor-pointer text-xs font-medium text-heading-foreground"
                for="{{ $avatars->isEmpty() ? '' : 'avatar-' . $avatars[0]['id'] }}"
            >
                @lang('Avatar')
            </label>
            <p class="mb-5 w-full text-sm">
                @lang('Select an avatar for your chatbot.')
            </p>
            <div class="grid grid-cols-3 gap-x-2 gap-y-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                <div class="lqd-chatbot-avatar-list contents">
                    @forelse ($avatars as $avatar)
                        <div class="lqd-chatbot-avatar-list-item relative flex justify-center">
                            <input
                                class="peer invisible size-0"
                                id="avatar-{{ $avatar['id'] }}"
                                type="radio"
                                name="avatar"
                                value="{{ $avatar['avatar'] }}"
                                x-model="activeChatbot.avatar"
                            />
                            <label
                                class="relative inline-grid size-10 cursor-pointer place-items-center transition-all hover:scale-110 peer-checked:drop-shadow-xl"
                                for="avatar-{{ $avatar['id'] }}"
                                tabindex="0"
                            >
                                <img
                                    width="40"
                                    height="40"
                                    src="{{ $avatar['avatar_url'] }}"
                                >
                            </label>
                            <span
                                class="pointer-events-none invisible absolute start-1/2 top-1/2 inline-grid size-6 -translate-x-1/2 -translate-y-1/2 scale-75 place-items-center rounded-full bg-white/15 text-heading-foreground opacity-0 backdrop-blur-md backdrop-saturate-150 transition-all peer-checked:visible peer-checked:scale-100 peer-checked:opacity-100"
                            >
                                <x-tabler-check class="size-[18px] text-white" />
                            </span>
                        </div>
                    @empty
                        <div class="lqd-chatbot-avatar-list-item relative flex hidden justify-center">
                            <input
                                class="peer invisible size-0"
                                type="radio"
                                name="avatar"
                                x-model="activeChatbot.avatar"
                            />
                            <label
                                class="relative inline-grid size-10 cursor-pointer place-items-center transition-all hover:scale-110 peer-checked:drop-shadow-xl"
                                tabindex="0"
                            >
                                <img
                                    width="40"
                                    height="40"
                                >
                            </label>
                            <span
                                class="pointer-events-none invisible absolute start-1/2 top-1/2 inline-grid size-6 -translate-x-1/2 -translate-y-1/2 scale-75 place-items-center rounded-full bg-white/15 text-heading-foreground opacity-0 backdrop-blur-md backdrop-saturate-150 transition-all peer-checked:visible peer-checked:scale-100 peer-checked:opacity-100"
                            >
                                <x-tabler-check class="size-[18px] text-white" />
                            </span>
                        </div>
                    @endforelse
                </div>
                <div
                    class="relative flex justify-center"
                    x-data="customAvatar"
                >
                    <input
                        class="peer invisible size-0"
                        id="avatar-custom"
                        type="file"
                        x-bind="customAvatarPicker"
                    >
                    <label
                        class="inline-grid size-10 cursor-pointer place-items-center rounded-full bg-heading-foreground/5 text-heading-foreground transition-all hover:scale-110 hover:bg-heading-foreground hover:text-heading-background peer-checked:drop-shadow-xl"
                        for="avatar-custom"
                        tabindex="0"
                    >
                        <x-tabler-plus class="size-4" />
                    </label>
                </div>
            </div>

            <template
                x-for="(error, index) in formErrors.avatar"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <label
                class="mb-5 block w-full cursor-pointer text-xs font-medium text-heading-foreground"
                for="color-{{ $predefined_colors[0] }}"
            >
                @lang('Color')
            </label>
            <p class="mb-5 w-full text-sm">
                @lang('Choose an accent color that represents your brand.')
            </p>
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                @foreach ($predefined_colors as $color)
                    <div class="relative flex justify-center">
                        <input
                            class="peer invisible size-0"
                            id="color-{{ $color }}"
                            type="radio"
                            name="color"
                            value="#{{ $color }}"
                            x-model="activeChatbot.color"
                        />
                        <label
                            class="relative inline-grid size-10 cursor-pointer place-items-center rounded-full border-[3px] border-background shadow-[0_4px_12px_rgba(0,0,0,0.11)] transition-all hover:scale-110 peer-checked:shadow-xl"
                            style="background-color: #{{ $color }}"
                            for="color-{{ $color }}"
                            tabindex="0"
                        >
                        </label>
                        <x-tabler-check
                            class="pointer-events-none absolute start-1/2 top-1/2 size-[18px] -translate-x-1/2 -translate-y-1/2 scale-50 text-white opacity-0 transition-all peer-checked:scale-100 peer-checked:opacity-100"
                        />
                    </div>
                @endforeach
                <div
                    class="relative flex justify-center"
                    x-data="customColorPicker"
                >
                    <input
                        class="peer invisible size-0"
                        id="color-custom"
                        name="color"
                        type="radio"
                        value=""
                        x-bind="customColorRadioInput"
                    >
                    <input
                        class="invisible absolute size-0"
                        id="custom-color-input"
                        type="color"
                        x-bind="customColorColorInput"
                        x-model="activeChatbot.color"
                    />
                    <label
                        class="relative inline-grid size-10 cursor-pointer place-items-center rounded-full border-[3px] border-background text-heading-foreground shadow-[0_4px_12px_rgba(0,0,0,0.11)] transition-all after:absolute after:inset-0 after:rounded-full after:bg-black/10 hover:scale-110 hover:bg-heading-foreground hover:text-heading-background peer-checked:drop-shadow-xl"
                        for="color-custom"
                        tabindex="0"
                        style="background: conic-gradient(from 90deg, violet, indigo, blue, green, yellow, orange, red, violet);"
                        x-bind="customColorTrigger"
                    >
                    </label>
                    <x-tabler-check
                        class="pointer-events-none absolute start-1/2 top-1/2 size-[18px] -translate-x-1/2 -translate-y-1/2 scale-50 text-white opacity-0 transition-all peer-checked:scale-100 peer-checked:opacity-100"
                    />
                    <span
                        class="absolute end-0 top-0 inline-block size-6 -translate-y-1/3 translate-x-1/3 scale-50 rounded-full border border-background opacity-0 shadow-lg transition-all peer-checked:scale-100 peer-checked:opacity-100"
                        x-bind="customColorOutput"
                    ></span>
                </div>
            </div>

            <template
                x-for="(error, index) in formErrors.color"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <hr>

        <div>
            <label class="mb-5 block w-full text-xs font-medium text-heading-foreground">
                @lang('Header Background')
            </label>

            <div class="lqd-tabs">
                <div class="lqd-tabs-triggers mb-3.5 flex gap-2 rounded-lg bg-heading-foreground/5 p-1">
                    <button
                        class="active grow justify-center rounded p-1 text-center text-2xs font-medium [&.active]:bg-background [&.active]:text-heading-foreground [&.active]:shadow-lg [&.active]:shadow-black/5"
                        type="button"
                        :class="{ 'active': activeChatbot.header_bg_type === 'color' }"
                        @click.prevent="activeChatbot.header_bg_type = 'color'"
                    >
                        @lang('Solid Color')
                    </button>

                    <button
                        class="grow justify-center rounded p-1 text-center text-2xs font-medium [&.active]:bg-background [&.active]:text-heading-foreground [&.active]:shadow-lg [&.active]:shadow-black/5"
                        type="button"
                        :class="{ 'active': activeChatbot.header_bg_type === 'gradient' }"
                        @click.prevent="activeChatbot.header_bg_type = 'gradient'"
                    >
                        @lang('Gradient')
                    </button>

                    <button
                        class="grow justify-center rounded p-1 text-center text-2xs font-medium [&.active]:bg-background [&.active]:text-heading-foreground [&.active]:shadow-lg [&.active]:shadow-black/5"
                        type="button"
                        :class="{ 'active': activeChatbot.header_bg_type === 'image' }"
                        @click.prevent="activeChatbot.header_bg_type = 'image'"
                    >
                        @lang('Image')
                    </button>
                </div>

                <div class="lqd-tabs-contents">
                    <div
                        class="lqd-tab-content active hidden [&.active]:block"
                        :class="{ 'active': activeChatbot.header_bg_type === 'color' }"
                    >
                        <div
                            class="flex grow items-center gap-2 rounded-input border border-input-border px-2"
                            x-data="liquidColorPicker({ colorVal: activeChatbot.header_bg_color })"
                            x-effect="picker && picker.setColor(activeChatbot.header_bg_color)"
                        >
                            <span
                                class="lqd-input-color-wrap !size-[26px] shrink-0 grow-0 rounded-full border-[3px] border-background shadow-md shadow-black/10"
                                x-ref="colorInputWrap"
                                :style="{ backgroundColor: colorVal }"
                            ></span>
                            <input
                                class="h-10 grow border-none bg-transparent p-0 text-2xs font-medium focus:outline-none"
                                type="text"
                                x-model="activeChatbot.header_bg_color"
                                x-ref="colorInput"
                                @change="picker.setColor($event.target.value)"
                                @keydown.enter.prevent="picker?.setColor($event.target.value);"
                                @focus="picker.open(); $el.select();"
                            />
                        </div>
                    </div>
                    <div
                        class="lqd-tab-content hidden [&.active]:block"
                        :class="{ 'active': activeChatbot.header_bg_type === 'gradient' }"
                    >
                        <div
                            class="space-y-2"
                            x-data="gradientPicker({ gradient: activeChatbot.header_bg_gradient })"
                            x-init="$watch('activeChatbot.id', (value) => {
                                gradient = activeChatbot.header_bg_gradient ?? '';
                                parseExistingGradient();
                                updateGradient();
                            });"
                        >
                            <div class="flex gap-2">
                                <x-forms.input
                                    class:container="grow"
                                    type="select"
                                    x-model="gradientType"
                                >
                                    <option value="linear-gradient">
                                        {{ __('Linear Gradient') }}
                                    </option>
                                    <option value="radial-gradient">
                                        {{ __('Radial Gradient') }}
                                    </option>
                                </x-forms.input>

                                <span class="mx-1 inline-block h-6 w-px shrink-0 self-center bg-foreground/10"></span>

                                <div
                                    class="relative size-10 shrink-0 select-none"
                                    :class="{ 'disabled pointer-events-none opacity-50': !gradientType.includes('linear-gradient') }"
                                >
                                    <div
                                        class="absolute inset-0 cursor-grab rounded-full border"
                                        x-ref="gradientAngleHandler"
                                        :style="{ transform: `rotate(${gradientAngle}deg)` }"
                                    >
                                        <div
                                            class="absolute left-1/2 top-0 h-full w-2 -translate-x-1/2 before:absolute before:start-0 before:top-1 before:size-2 before:rounded-full before:bg-foreground">
                                        </div>
                                    </div>
                                </div>

                                <x-forms.input
                                    class:container="shrink-0 w-16"
                                    class="lqd-input-stepper px-2 text-center"
                                    type="number"
                                    min="0"
                                    max="360"
                                    x-ref="gradientAngleInput"
                                    x-model="gradientAngle"
                                    ::disabled="!gradientType.includes('linear-gradient')"
                                />
                            </div>

                            <div
                                class="relative h-10 w-full rounded-xl shadow-sm"
                                x-ref="gradientPreviewWrap"
                            >
                                <div
                                    class="absolute inset-0 rounded-xl opacity-5"
                                    style="background-image: linear-gradient(45deg, hsl(var(--foreground)) 25%, transparent 25%, transparent 75%, hsl(var(--foreground)) 75%, hsl(var(--foreground))), linear-gradient(45deg, hsl(var(--foreground)) 25%, transparent 25%, transparent 75%, hsl(var(--foreground)) 75%, hsl(var(--foreground))); background-size: 14px 14px;background-position: 0 0, 21px 21px;"
                                ></div>
                                <div
                                    class="absolute inset-0 cursor-copy rounded-xl border"
                                    x-ref="gradientPreview"
                                    :style="{ background: gradient }"
                                    @click.prevent="handlePreviewClick"
                                >
                                    <template
                                        x-for="(stop, index) in gradientStops"
                                        :key="index"
                                    >
                                        <div
                                            class="group absolute top-1/2 flex -translate-y-1/2 flex-col items-center"
                                            :style="{ left: stop.position + '%', transform: 'translateX(-50%) translateY(-50%)' }"
                                        >
                                            <x-button
                                                class="pointer-events-none absolute -top-7 left-1/2 size-5 shrink-0 -translate-x-1/2 opacity-0 transition focus-visible:pointer-events-auto focus-visible:opacity-100 group-hover:pointer-events-auto group-hover:opacity-100"
                                                size="none"
                                                variant="danger"
                                                type="button"
                                                title="{{ __('Remove Color Stop') }}"
                                                @click.stop="removeGradientStop(index)"
                                                x-show="gradientStops.length > 2"
                                            >
                                                <x-tabler-x class="size-4" />
                                            </x-button>
                                            <div
                                                class="h-11 w-4 cursor-move rounded-full border-2 border-white shadow-lg transition-all before:absolute before:bottom-full before:left-1/2 before:h-3 before:w-10 before:-translate-x-1/2 after:absolute after:left-1/2 after:top-full after:h-4 after:w-10 after:-translate-x-1/2 hover:ring-4 hover:ring-heading-foreground/25"
                                                :style="{ backgroundColor: stop.color }"
                                                @mousedown="startDragStop(index, $event)"
                                                @click.stop
                                            ></div>
                                            <input
                                                class="lqd-input-stepper pointer-events-none absolute -bottom-10 h-8 w-11 rounded-input border border-input-border bg-background px-2 text-center text-2xs font-medium opacity-0 shadow-lg shadow-black/5 outline-none transition focus-visible:pointer-events-auto focus-visible:border-heading-foreground focus-visible:opacity-100 focus-visible:ring focus-visible:ring-heading-foreground group-hover:pointer-events-auto group-hover:opacity-100"
                                                type="number"
                                                min="0"
                                                max="100"
                                                x-model="gradientStops[index].position"
                                                @click.stop
                                            >
                                        </div>
                                    </template>
                                    <template x-if="!gradientStops.length">
                                        <p class="absolute left-1/2 top-1/2 z-2 m-0 -translate-x-1/2 -translate-y-1/2 text-3xs font-medium">
                                            {{ __('Click to add color stops') }}
                                        </p>
                                    </template>
                                </div>
                            </div>

                            <div class="space-y-1 rounded-lg border p-5">
                                <p class="text-2xs font-medium">
                                    {{ __('Color Stops') }}
                                </p>

                                <template
                                    x-for="(stop, index) in gradientStops"
                                    :key="index"
                                >
                                    <div class="flex gap-1">
                                        <div
                                            class="flex grow items-center gap-2 rounded-input border border-input-border px-2"
                                            x-data="liquidColorPicker({ colorVal: stop.color })"
                                            x-effect="picker && picker.setColor(stop.color)"
                                        >
                                            <span
                                                class="lqd-input-color-wrap !size-[26px] shrink-0 grow-0 rounded-full border-[3px] border-background shadow-md shadow-black/10"
                                                x-ref="colorInputWrap"
                                                :style="{ backgroundColor: colorVal }"
                                            ></span>
                                            <input
                                                class="h-10 grow border-none bg-transparent p-0 text-2xs font-medium focus:outline-none"
                                                type="text"
                                                x-model="stop.color"
                                                x-ref="colorInput"
                                                @change="picker.setColor($event.target.value)"
                                                @keydown.enter.prevent="picker?.setColor($event.target.value);"
                                                @focus="picker.open(); $el.select();"
                                            />
                                        </div>
                                        <x-forms.input
                                            class:container="shrink-0 w-16"
                                            class="lqd-input-stepper px-2 text-center"
                                            type="number"
                                            min="0"
                                            max="100"
                                            x-model="stop.position"
                                        />
                                        <x-button
                                            class="ms-auto size-8 shrink-0 self-center"
                                            size="none"
                                            variant="danger"
                                            type="button"
                                            title="{{ __('Remove Color Stop') }}"
                                            @click="removeGradientStop(index)"
                                            x-show="gradientStops.length > 2"
                                        >
                                            <x-tabler-x class="size-4" />
                                        </x-button>
                                    </div>
                                </template>

                                <!-- Add color stop button -->
                                <x-button
                                    class="w-full text-2xs"
                                    type="button"
                                    variant="success"
                                    @click="addGradientStopAtEnd()"
                                >
                                    <x-tabler-plus class="size-4" />
                                    {{ __('Add a Color Stop') }}
                                </x-button>
                            </div>

                            <input
                                type="hidden"
                                name="header_bg_gradient"
                                x-model="gradient"
                                x-modelable="activeChatbot.header_bg_gradient"
                            >
                        </div>
                    </div>
                    <div
                        class="lqd-tab-content hidden [&.active]:block"
                        :class="{ 'active': activeChatbot.header_bg_type === 'image' }"
                    >
                        <x-forms.input
                            class:label="text-heading-foreground"
                            type="file"
                            name="header_bg_image"
                            size="lg"
                            @change="$event.target.files[0] && (activeChatbot.header_bg_image_blob = $event.target.files[0]) && (activeChatbot.header_bg_image = URL.createObjectURL($event.target.files[0]))"
                        />
                    </div>
                </div>
            </div>

            <input
                type="hidden"
                name="header_bg_type"
                x-model="activeChatbot.header_bg_type"
            >
        </div>

        <hr>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Email Collect') }}"
                type="checkbox"
                switcher
                name="is_email_collect"
                ::checked="activeChatbot.is_email_collect === 1"
                x-model.boolean="activeChatbot.is_email_collect"
                tooltip="{{ __('Request the user email address after they send a message') }}"
                @change="externalChatbot.toggleWindowState('open')"
            />
            <template
                x-for="(error, index) in formErrors.is_email_collect"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Contact Us') }}"
                type="checkbox"
                switcher
                name="is_contact"
                ::checked="activeChatbot.is_contact === 1"
                x-model.boolean="activeChatbot.is_contact"
                tooltip="{{ __('Show the contact link in welcome screen.') }}"
                @change="externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
            />
            <template
                x-for="(error, index) in formErrors.is_contact"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Enable Emoji') }}"
                type="checkbox"
                switcher
                name="is_emoji"
                ::checked="activeChatbot.is_emoji === 1"
                x-model.boolean="activeChatbot.is_emoji"
                @change="externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('conversation-messages')"
            />
            <template
                x-for="(error, index) in formErrors.is_emoji"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Links In Welcome Screen') }}"
                type="checkbox"
                switcher
                name="is_links"
                ::checked="activeChatbot.is_links === 1"
                x-model.boolean="activeChatbot.is_links"
                tooltip="{{ __('Show links in the welcome screen. Configure the links in the Links section.') }}"
                @change="externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
            />
            <template
                x-for="(error, index) in formErrors.is_links"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Links in First Assistant Message') }}"
                type="checkbox"
                switcher
                name="show_social_links_in_first_message"
                ::checked="activeChatbot.show_social_links_in_first_message === 1"
                x-model.boolean="activeChatbot.show_social_links_in_first_message"
                tooltip="{{ __('Show links in first assistant message. Configure the links in the Links section.') }}"
                @change="externalChatbot.toggleWindowState('open');  externalChatbot.toggleView('conversation-messages')"
            />
            <template
                x-for="(error, index) in formErrors.show_social_links_in_first_message"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Show GDPR') }}"
                type="checkbox"
                switcher
                name="is_gdpr"
                ::checked="activeChatbot.is_gdpr === 1"
                x-model.boolean="activeChatbot.is_gdpr"
                tooltip="{{ __('Show GDPR notification. Configure Privacy Policy and Terms of Service links in the Links section.') }}"
                @change="externalChatbot.toggleWindowState('open');  externalChatbot.toggleView('conversation-messages')"
            />
            <template
                x-for="(error, index) in formErrors.is_gdpr"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Help Center') }}"
                type="checkbox"
                switcher
                name="is_contact"
                ::checked="activeChatbot.is_articles === 1"
                x-model.boolean="activeChatbot.is_articles"
                @change="externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
            />
            <template
                x-for="(error, index) in formErrors.is_articles"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Show Logo') }}"
                type="checkbox"
                switcher
                name="show_logo"
                ::checked="activeChatbot.show_logo === 1"
                x-model.boolean="activeChatbot.show_logo"
                tooltip="{{ __('Show the logo in the welcome screen header.') }}"
                @change="externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
            />
            <template
                x-for="(error, index) in formErrors.show_logo"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Show Date and Time') }}"
                type="checkbox"
                switcher
                name="show_date_and_time"
                ::checked="activeChatbot.show_date_and_time === 1"
                x-model.boolean="activeChatbot.show_date_and_time"
                tooltip="{{ __('Show the date and time in the conversation messages.') }}"
                @change="if ( externalChatbot ) { externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('conversation-messages') }"
            />

            <template
                x-for="(error, index) in formErrors.show_date_and_time"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="flex-row-reverse justify-between text-xs font-medium text-heading-foreground"
                size="sm"
                containerClass="[&_.lqd-input-label-txt]:order-1 [&_.lqd-tooltip-container]:me-auto"
                label="{{ __('Transparent Trigger') }}"
                type="checkbox"
                switcher
                name="trigger_background"
                tooltip="{{ __('If activated, the trigger will be transparent in idle state. But we add a background to the trigger in active mode to make it more visible.') }}"
                ::checked="activeChatbot.trigger_background === 'transparent'"
                @change="
                    $event.target.checked ? activeChatbot.trigger_background = 'transparent' : activeChatbot.trigger_background = '';
                    externalChatbot && externalChatbot.toggleWindowState('close');
                "
                x-model="activeChatbot.trigger_background"
            />

            <template
                x-for="(error, index) in formErrors.trigger_background"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div
            class="flex w-full flex-col gap-5 text-heading-foreground"
            x-data="{ currentVal: `${parseInt(activeChatbot.trigger_avatar_size || 60, 10)}px` }"
        >
            <label
                class="block w-full text-xs font-medium text-heading-foreground"
                for="trigger_avatar_size"
            >
                @lang('Trigger Avatar Size')
            </label>
            <div class="flex items-center gap-3">
                <input
                    class="h-2 w-full cursor-ew-resize appearance-none rounded-full bg-heading-foreground/5 focus:outline-secondary [&::-moz-range-thumb]:size-4 [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:bg-heading-foreground active:[&::-moz-range-thumb]:scale-110 [&::-webkit-slider-thumb]:size-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:border-none [&::-webkit-slider-thumb]:bg-heading-foreground active:[&::-webkit-slider-thumb]:scale-110"
                    type="range"
                    min="20"
                    max="100"
                    step="1"
                    name="trigger_avatar_size"
                    @input="
                        currentVal = `${$event.target.value}px`;
                        activeChatbot.trigger_avatar_size = currentVal;
                        externalChatbot && externalChatbot.toggleWindowState('close');
                    "
                    x-modelable="currentVal"
                />
                <span
                    class="ms-2 min-w-10 shrink-0 text-2xs font-medium"
                    x-text="parseInt(currentVal, 10) + 'px'"
                ></span>
            </div>

            <template
                x-for="(error, index) in formErrors.trigger_avatar_size"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <label
                class="mb-5 block w-full cursor-pointer text-xs font-medium text-heading-foreground"
                for="position-left"
            >
                @lang('Position')
            </label>
            <div class="flex flex-wrap justify-between gap-2">
                @foreach (\App\Extensions\Chatbot\System\Enums\PositionEnum::toArray() as $position)
                    <div class="relative text-center">
                        <input
                            class="peer invisible absolute size-0"
                            id="position-{{ $position }}"
                            type="radio"
                            name="position"
                            value="{{ $position }}"
                            x-model="activeChatbot.position"
                        />
                        <label
                            @class([
                                'h-[105px] w-[150px] rounded-lg bg-heading-foreground/5 p-3 text-heading-foreground/20 flex items-end cursor-pointer transition-all hover:scale-105',
                                'justify-end' => $position === 'right',
                            ])
                            for="position-{{ $position }}"
                        >
                            <svg
                                @class([
                                    '-scale-x-100' => $position === 'right',
                                ])
                                width="37"
                                height="50"
                                viewBox="0 0 37 50"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M0 45.0721C0 42.863 1.79086 41.0721 4 41.0721C6.20914 41.0721 8 42.863 8 45.0721C8 47.2813 6.20914 49.0721 4 49.0721C1.79086 49.0721 0 47.2813 0 45.0721Z"
                                />
                                <path
                                    d="M0 4.07214C0 1.863 1.79086 0.0721436 4 0.0721436H33C35.2091 0.0721436 37 1.863 37 4.07214V33.0721C37 35.2813 35.2091 37.0721 33 37.0721H4C1.79086 37.0721 0 35.2813 0 33.0721V4.07214Z"
                                />
                            </svg>
                        </label>
                        <span
                            class="pointer-events-none absolute end-1.5 top-1.5 inline-grid size-7 scale-110 place-items-center rounded-full bg-primary/10 text-primary opacity-0 transition-all peer-checked:scale-100 peer-checked:opacity-100"
                        >
                            <x-tabler-check class="size-5" />
                        </span>
                        <span class="mt-5 block text-xs font-semibold capitalize text-heading-foreground">
                            {{ __($position) }}
                        </span>
                    </div>
                    @if (!$loop->last)
                        <span class="w-px bg-heading-foreground/10"></span>
                    @endif
                @endforeach
            </div>

            <template
                x-for="(error, index) in formErrors.position"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div class="">
            <label class="mb-5 block w-full cursor-pointer text-xs font-medium text-heading-foreground">
                @lang('Links')
            </label>
            <div>
                <x-forms.input
                    class:label="text-heading-foreground"
                    label="{{ __('Footer Link') }}"
                    placeholder="{{ request()->getSchemeAndHttpHost() }}"
                    name="footer_link"
                    size="lg"
                    x-model="activeChatbot.footer_link"
                    @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
                />

                <template
                    x-for="(error, index) in formErrors.footer_link"
                    :key="'error-' + index"
                >
                    <div class="mt-2 text-2xs/5 font-medium text-red-500">
                        <p x-text="error"></p>
                    </div>
                </template>
            </div>

            <div class="mt-3">
                <x-forms.input
                    class:label="text-heading-foreground"
                    label="{{ __('Whatsapp Link') }}"
                    placeholder="{{ __('https://wa.me/your-number') }}"
                    name="whatsapp_link"
                    size="lg"
                    x-model="activeChatbot.whatsapp_link"
                    @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
                />

                <template
                    x-for="(error, index) in formErrors.whatsapp_link"
                    :key="'error-' + index"
                >
                    <div class="mt-2 text-2xs/5 font-medium text-red-500">
                        <p x-text="error"></p>
                    </div>
                </template>
            </div>

            <div class="mt-3">
                <x-forms.input
                    class:label="text-heading-foreground"
                    label="{{ __('Telegram Link') }}"
                    placeholder="{{ __('https://t.me/your-profile') }}"
                    name="telegram_link"
                    size="lg"
                    x-model="activeChatbot.telegram_link"
                    @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
                />

                <template
                    x-for="(error, index) in formErrors.telegram_link"
                    :key="'error-' + index"
                >
                    <div class="mt-2 text-2xs/5 font-medium text-red-500">
                        <p x-text="error"></p>
                    </div>
                </template>
            </div>
            <div class="mt-3">
                <x-forms.input
                    class:label="text-heading-foreground"
                    label="{{ __('Facebook Link') }}"
                    placeholder="{{ __('https://facebook.com/your-profile') }}"
                    name="facebook_link"
                    size="lg"
                    x-model="activeChatbot.facebook_link"
                    @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
                />

                <template
                    x-for="(error, index) in formErrors.facebook_link"
                    :key="'error-' + index"
                >
                    <div class="mt-2 text-2xs/5 font-medium text-red-500">
                        <p x-text="error"></p>
                    </div>
                </template>
            </div>

            <div class="mt-3">
                <x-forms.input
                    class:label="text-heading-foreground"
                    label="{{ __('Instagram Link') }}"
                    placeholder="{{ __('https://instagram.com/your-profile') }}"
                    name="instagram_link"
                    size="lg"
                    x-model="activeChatbot.instagram_link"
                    @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
                />

                <template
                    x-for="(error, index) in formErrors.instagram_link"
                    :key="'error-' + index"
                >
                    <div class="mt-2 text-2xs/5 font-medium text-red-500">
                        <p x-text="error"></p>
                    </div>
                </template>
            </div>

            <div class="mt-3">
                <x-forms.input
                    class:label="text-heading-foreground"
                    label="{{ __('Watch Product Tour') }}"
                    placeholder="{{ __('https://youtube.com/watch?v=galNNnEcK7I') }}"
                    name="watch_product_tour_link"
                    size="lg"
                    x-model="activeChatbot.watch_product_tour_link"
                    @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
                />

                <template
                    x-for="(error, index) in formErrors.watch_product_tour_link"
                    :key="'error-' + index"
                >
                    <div class="mt-2 text-2xs/5 font-medium text-red-500">
                        <p x-text="error"></p>
                    </div>
                </template>
            </div>

            <hr
                class="my-5"
                x-show="activeChatbot.is_gdpr"
            >

            <div
                class="mt-3"
                x-show="activeChatbot.is_gdpr"
            >
                <x-forms.input
                    class:label="text-heading-foreground"
                    label="{{ __('Privacy Policy Link') }}"
                    placeholder="{{ __('https://example.com/privacy-policy') }}"
                    name="privacy_policy_link"
                    size="lg"
                    x-model="activeChatbot.privacy_policy_link"
                    @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
                />

                <template
                    x-for="(error, index) in formErrors.privacy_policy_link"
                    :key="'error-' + index"
                >
                    <div class="mt-2 text-2xs/5 font-medium text-red-500">
                        <p x-text="error"></p>
                    </div>
                </template>
            </div>

            <div
                class="mt-3"
                x-show="activeChatbot.is_gdpr"
            >
                <x-forms.input
                    class:label="text-heading-foreground"
                    label="{{ __('Terms of Service Link') }}"
                    placeholder="{{ __('https://example.com/terms-of-service') }}"
                    name="terms_of_service_link"
                    size="lg"
                    x-model="activeChatbot.terms_of_service_link"
                    @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('welcome')"
                />

                <template
                    x-for="(error, index) in formErrors.terms_of_service_link"
                    :key="'error-' + index"
                >
                    <div class="mt-2 text-2xs/5 font-medium text-red-500">
                        <p x-text="error"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
