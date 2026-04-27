{{-- Editing Step 1 - Configure --}}
<div
    class="col-start-1 col-end-1 row-start-1 row-end-1 transition-all"
    data-step="1"
    x-show="editingStep === 1"
    x-transition:enter-start="opacity-0 -translate-x-3"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-3"
>
    <h2 class="mb-3.5">
        @lang('Configure')
    </h2>
    <p class="text-xs/5 opacity-60 lg:max-w-[360px]">
        @lang('Create and configure a chatbot that interacts with your users, ensuring it delivers accurate information.')
    </p>

    <div class="flex flex-col gap-7 pt-9">
        <div>
            <x-forms.input
                class:label="text-heading-foreground"
                label="{{ __('Chatbot Title') }}"
                placeholder="{{ __('MagicBot') }}"
                name="title"
                size="lg"
                x-model="activeChatbot.title"
                @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('open')"
            />

            <template
                x-for="(error, index) in formErrors.title"
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
                label="{{ __('Bubble Message') }}"
                placeholder="{{ __('MagicBot') }}"
                name="bubble_message"
                size="lg"
                x-model="activeChatbot.bubble_message"
                @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('close')"
            />

            <template
                x-for="(error, index) in formErrors.bubble_message"
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
                label="{{ __('Bubble Design') }}"
                name="bubble_design"
                size="lg"
                type="select"
                x-model="activeChatbot.bubble_design"
                @change="externalChatbot && externalChatbot.toggleWindowState('close'); $nextTick(() => externalChatbot && externalChatbot.fillDemoBubbleData && externalChatbot.fillDemoBubbleData())"
            >
                @foreach (\App\Extensions\Chatbot\System\Enums\BubbleDesign::cases() as $design)
                    <option value="{{ $design->value }}">
                        {{ __(ucwords(str_replace('_', ' ', $design->value))) }}
                    </option>
                @endforeach
            </x-forms.input>

            <template
                x-for="(error, index) in formErrors.bubble_design"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div
            class="flex flex-wrap items-center justify-between gap-2"
            x-cloak
            x-show="activeChatbot.bubble_design === 'promo_banner'"
            x-transition
        >
            <p class="m-0 text-2xs font-medium text-heading-foreground">
                {{ __('Promo Banner Settings') }}
            </p>
            <x-modal
                class:modal="z-[100]"
                class:modal-head="border-b-0"
                class:modal-body="pt-0"
                class:modal-content="w-[min(600px,100%)]"
            >
                <x-slot:trigger
                    size="sm"
                    variant="ghost-shadow"
                    type="button"
                >
                    {{ __('Configure') }}
                </x-slot:trigger>

                <x-slot:modal>
                    <h3 class="mb-3.5">
                        {{ __('Promo Banner') }}
                    </h3>
                    <p class="mb-9 text-balance text-base font-medium opacity-50">
                        {{ __('Configure the promotional banner that appears above the trigger button.') }}
                    </p>

                    <div class="space-y-5">
                        <div>
                            <x-forms.input
                                class:label="text-heading-foreground"
                                label="{{ __('Banner Image') }}"
                                name="promo_banner_image"
                                type="file"
                                size="lg"
                                accept="image/*"
                                @change="
                                    const file = $event.target.files[0];
                                    if (!file) return;
                                    activeChatbot.promo_banner_image_blob = file;
                                    activeChatbot.promo_banner_image = URL.createObjectURL(file);
                                    externalChatbot && externalChatbot.toggleWindowState('close');
                                "
                            />
                            <template x-if="activeChatbot.promo_banner_image">
                                <div class="mt-2 overflow-hidden">
                                    <img
                                        class="h-[155px] w-[min(280px,100%)] rounded-lg object-cover"
                                        :src="activeChatbot.promo_banner_image"
                                        alt=""
                                    />
                                </div>
                            </template>
                            <template
                                x-for="(error, index) in formErrors.promo_banner_image_blob"
                                :key="'promo-img-error-' + index"
                            >
                                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                                    <p x-text="error"></p>
                                </div>
                            </template>
                        </div>

                        <div>
                            <x-forms.input
                                class:label="text-heading-foreground"
                                label="{{ __('Title') }}"
                                placeholder="{{ __('Claim your discount') }}"
                                name="promo_banner_title"
                                size="lg"
                                x-model="activeChatbot.promo_banner_title"
                                @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('close')"
                            />
                            <template
                                x-for="(error, index) in formErrors.promo_banner_title"
                                :key="'promo-title-error-' + index"
                            >
                                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                                    <p x-text="error"></p>
                                </div>
                            </template>
                        </div>

                        <div>
                            <x-forms.input
                                class:label="text-heading-foreground"
                                label="{{ __('Description') }}"
                                placeholder="{{ __('For a limited time, enjoy massive discounts across our entire store.') }}"
                                name="promo_banner_description"
                                type="textarea"
                                rows="3"
                                size="lg"
                                x-model="activeChatbot.promo_banner_description"
                                @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('close')"
                            />
                            <template
                                x-for="(error, index) in formErrors.promo_banner_description"
                                :key="'promo-desc-error-' + index"
                            >
                                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                                    <p x-text="error"></p>
                                </div>
                            </template>
                        </div>

                        <div>
                            <x-forms.input
                                class:label="text-heading-foreground"
                                label="{{ __('Button Label') }}"
                                placeholder="{{ __('View Offer') }}"
                                name="promo_banner_btn_label"
                                size="lg"
                                x-model="activeChatbot.promo_banner_btn_label"
                                @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('close')"
                            />
                            <template
                                x-for="(error, index) in formErrors.promo_banner_btn_label"
                                :key="'promo-btn-label-error-' + index"
                            >
                                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                                    <p x-text="error"></p>
                                </div>
                            </template>
                        </div>

                        <div>
                            <x-forms.input
                                class:label="text-heading-foreground"
                                label="{{ __('Button Link') }}"
                                placeholder="{{ __('https://example.com/offer') }}"
                                name="promo_banner_btn_link"
                                size="lg"
                                x-model="activeChatbot.promo_banner_btn_link"
                                @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('close')"
                            />
                            <template
                                x-for="(error, index) in formErrors.promo_banner_btn_link"
                                :key="'promo-btn-link-error-' + index"
                            >
                                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                                    <p x-text="error"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <x-button
                        class="mt-8 w-full"
                        variant="secondary"
                        @click.prevent="modalOpen = false"
                    >
                        {{ __('Save Settings') }}
                    </x-button>
                </x-slot:modal>
            </x-modal>
        </div>

        <div>
            <x-forms.input
                class:label="text-heading-foreground"
                label="{{ __('Welcome Message') }}"
                placeholder="{{ __('Enter welcome message') }}"
                name="welcome_message"
                size="lg"
                x-model="activeChatbot.welcome_message"
                @input.throttle.250ms="if ( externalChatbot ) { externalChatbot.toggleWindowState('open'); externalChatbot.toggleView('conversation-messages') }"
            />

            <template
                x-for="(error, index) in formErrors.welcome_message"
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
                label="{{ __('Chatbot Instructions') }}"
                placeholder="{{ __('Explain chatbot role') }}"
                name="instructions"
                size="lg"
                type="textarea"
                rows="4"
                x-model="activeChatbot.instructions"
            />

            <template
                x-for="(error, index) in formErrors.instructions"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="text-heading-foreground flex-row-reverse justify-between"
                label="{{ __('Do Not Go Beyond Instructions') }}"
                name="do_not_go_beyond_instructions"
                size="sm"
                type="checkbox"
                switcher
                x-model.boolean="activeChatbot.do_not_go_beyond_instructions"
            />

            <template
                x-for="(error, index) in formErrors.do_not_go_beyond_instructions"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        <div>
            <x-forms.input
                class:label="text-heading-foreground flex-row-reverse justify-between"
                label="{{ __('Suggested Prompts/Questions') }}"
                name="suggested_prompts_enabled"
                size="sm"
                type="checkbox"
                switcher
                x-model.boolean="activeChatbot.suggested_prompts_enabled"
                @change="toggleSuggestedPrompts($event.target.checked)"
            />

            <div
                class="mt-6 space-y-3 rounded-lg border border-border/60 bg-background/60 p-5"
                x-cloak
                x-show="activeChatbot.suggested_prompts_enabled"
                x-transition
            >
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-heading-foreground">
                            {{ __('Suggested Prompts/Questions') }}
                        </h3>
                        <p class="text-muted-foreground mt-1 text-2xs/5">
                            {{ __('Provide quick starter prompts your assistant can suggest to users.') }}
                        </p>
                    </div>

                    <x-button
                        class="shrink-0"
                        type="button"
                        size="xs"
                        variant="success"
                        @click="openSuggestedPromptModal('create')"
                    >
                        <x-tabler-plus class="size-4" />
                        {{ __('Add Prompt') }}
                    </x-button>
                </div>

                <template x-if="!Array.isArray(activeChatbot.suggested_prompts) || activeChatbot.suggested_prompts.length === 0">
                    <div class="text-muted-foreground rounded-md border border-dashed border-border/60 bg-background p-4 text-2xs/6">
                        {{ __('No prompts added yet. Add one to get started.') }}
                    </div>
                </template>

                <template
                    x-for="(prompt, index) in activeChatbot.suggested_prompts"
                    :key="'suggested-prompt-' + index"
                >
                    <div class="bg-card space-y-3 rounded-md border border-border/60 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p
                                    class="text-xs font-semibold text-heading-foreground"
                                    x-text="prompt.name?.trim() || '{{ __('Untitled Prompt') }}'"
                                ></p>
                                <p
                                    class="text-muted-foreground mt-1 text-2xs/5"
                                    x-text="prompt.prompt?.trim() || '{{ __('No prompt provided') }}'"
                                ></p>
                            </div>
                            <div class="flex items-center gap-1">
                                <x-button
                                    class="size-8 shrink-0"
                                    type="button"
                                    size="none"
                                    variant="ghost"
                                    title="{{ __('Edit Prompt') }}"
                                    @click="openSuggestedPromptModal('edit', index)"
                                >
                                    <x-tabler-pencil class="size-4" />
                                </x-button>
                                <x-button
                                    class="size-8 shrink-0"
                                    type="button"
                                    size="none"
                                    variant="ghost"
                                    title="{{ __('Remove Prompt') }}"
                                    @click="removeSuggestedPrompt(index)"
                                >
                                    <x-tabler-trash class="size-4" />
                                </x-button>
                            </div>
                        </div>

                        <input
                            type="hidden"
                            x-bind:name="'suggested_prompts[' + index + '][name]'"
                            :value="prompt.name ?? ''"
                        >
                        <input
                            type="hidden"
                            x-bind:name="'suggested_prompts[' + index + '][prompt]'"
                            :value="prompt.prompt ?? ''"
                        >

                        <template x-if="formErrors['suggested_prompts.' + index + '.name']?.length">
                            <div class="text-2xs/5 font-medium text-red-500">
                                <p x-text="formErrors['suggested_prompts.' + index + '.name'][0]"></p>
                            </div>
                        </template>
                        <template x-if="formErrors['suggested_prompts.' + index + '.prompt']?.length">
                            <div class="text-2xs/5 font-medium text-red-500">
                                <p x-text="formErrors['suggested_prompts.' + index + '.prompt'][0]"></p>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <div
            class="fixed inset-0 z-[999] flex items-center justify-center"
            x-cloak
            x-show="suggestedPromptModal.open"
            x-trap.noscroll="suggestedPromptModal.open"
            @keydown.escape.window="closeSuggestedPromptModal()"
        >
            <div
                class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                @click="closeSuggestedPromptModal()"
                x-transition.opacity
            ></div>

            <div
                class="relative z-[1000] w-full max-w-lg rounded-xl bg-background p-6 shadow-2xl shadow-black/20"
                x-transition.scale
            >
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-base font-semibold text-heading-foreground">
                        <template x-if="suggestedPromptModal.mode === 'create'">
                            <span>{{ __('Add Suggested Prompt') }}</span>
                        </template>
                        <template x-if="suggestedPromptModal.mode === 'edit'">
                            <span>{{ __('Edit Suggested Prompt') }}</span>
                        </template>
                    </h3>
                    <button
                        class="text-muted-foreground hover:bg-muted/60 inline-flex size-8 items-center justify-center rounded-lg transition"
                        type="button"
                        @click="closeSuggestedPromptModal()"
                    >
                        <x-tabler-x class="size-4" />
                    </button>
                </div>

                <div class="mt-5 space-y-4">
                    <x-forms.input
                        class:label="text-heading-foreground"
                        label="{{ __('Prompt Name') }}"
                        name="_suggested_prompt_name"
                        placeholder="{{ __('Example: Pricing') }}"
                        x-model="suggestedPromptModal.form.name"
                    />
                    <x-forms.input
                        class:label="text-heading-foreground"
                        label="{{ __('Prompt') }}"
                        name="_suggested_prompt_value"
                        type="textarea"
                        rows="4"
                        placeholder="{{ __('Example: How much does the premium plan cost?') }}"
                        x-model="suggestedPromptModal.form.prompt"
                    />
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <x-button
                        type="button"
                        size="sm"
                        variant="outline"
                        @click="closeSuggestedPromptModal()"
                    >
                        {{ __('Cancel') }}
                    </x-button>
                    <x-button
                        type="button"
                        size="sm"
                        variant="primary"
                        @click="saveSuggestedPrompt()"
                    >
                        <template x-if="suggestedPromptModal.mode === 'create'">
                            <span>{{ __('Add Prompt') }}</span>
                        </template>
                        <template x-if="suggestedPromptModal.mode === 'edit'">
                            <span>{{ __('Save Changes') }}</span>
                        </template>
                    </x-button>
                </div>
            </div>
        </div>

        <div>
            <x-forms.input
                class:label="text-heading-foreground"
                label="{{ __('Language') }}"
                name="language"
                size="lg"
                type="select"
                x-model="activeChatbot.language"
            >
                <option
                    value="auto"
                    selected
                >
                    @lang('Auto')
                </option>
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @if (in_array($localeCode, explode(',', $settings_two->languages), true))
                        <option value="{{ $localeCode }}">
                            {{ $properties['name'] }}
                        </option>
                    @endif
                @endforeach
            </x-forms.input>
            <template
                x-for="(error, index) in formErrors.language"
                :key="'error-' + index"
            >
                <div class="mt-2 text-2xs/5 font-medium text-red-500">
                    <p x-text="error"></p>
                </div>
            </template>
        </div>

        @if (class_exists(\App\Extensions\Chatbot\System\Helpers\ChatbotHelper::class) && \App\Extensions\Chatbot\System\Helpers\ChatbotHelper::planAllowsHumanAgent())
            @includeIf('chatbot-agent::particles.chatbot-config')
        @endif
        @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-review'))
            @includeIf('chatbot-review::partials.review-config')
        @endif

        @includeIf('chatbot-voice-call::particles.chatbot-config')
		@includeIf('chatbot-booking::particles.chatbot-config')
		@includeIf('chatbot-ecommerce::particles.chatbot-config')

        {{--        <div> --}}
        {{--            <x-forms.input --}}
        {{--                class:label="text-heading-foreground" --}}
        {{--                label="{{ __('AI Model') }}" --}}
        {{--                name="ai_model" --}}
        {{--                size="lg" --}}
        {{--                type="select" --}}
        {{--                x-model="activeChatbot.ai_model" --}}
        {{--                x-ref="aiModelSelect" --}}
        {{--            > --}}
        {{--                @foreach (\App\Domains\Entity\Enums\EntityEnum::reWriterModels(\App\Domains\Engine\Enums\EngineEnum::OPEN_AI) as $model) --}}
        {{--                    <option value="{{ $model->value }}"> --}}
        {{--                        {{ $model->label() }} --}}
        {{--                    </option> --}}
        {{--                @endforeach --}}
        {{--            </x-forms.input> --}}

        {{--            <template --}}
        {{--                x-for="(error, index) in formErrors.ai_model" --}}
        {{--                :key="'error-' + index" --}}
        {{--            > --}}
        {{--                <div class="mt-2 text-2xs/5 font-medium text-red-500"> --}}
        {{--                    <p x-text="error"></p> --}}
        {{--                </div> --}}
        {{--            </template> --}}
        {{--        </div> --}}
    </div>
</div>
