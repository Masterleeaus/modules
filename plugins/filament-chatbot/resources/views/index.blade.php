@php
    $user_avatar = Auth::user()->avatar;

    if (!Auth::user()->github_token && !Auth::user()->google_token && !Auth::user()->facebook_token) {
        $user_avatar = '/' . $user_avatar;
    }
    $human_agent_conditions = [
        'When the issue is too complex or ambiguous.',
        'When the customer is frustrated or dissatisfied.',
        'When sensitive topics (legal, financial, medical, etc.) are involved.',
        'When the AI fails to understand after repeated attempts.',
        'When empathy or emotional intelligence is required.',
        'When the request is outside the AI’s scope or permissions.',
        'When the customer explicitly requests a human.',
    ];
    $booking_assistant_conditions = [
        'User explicitly asks to schedule a meeting',
        'User asks for examples, use cases, or real demos',
        'User mentions team size, enterprise, or agency use',
        'User expresses hesitation, doubt, or objections',
        'User explicitly asks to see how it works',
    ];
    $shop_features = ['getPaymentGateway', 'getShippingMethods', 'getCoupons', 'getProductReviews'];
    $shop_features_label = [
        'getPaymentGateway' => 'Payment Gateways',
        'getShippingMethods' => 'Shipping Methods',
        'getCoupons' => 'Coupons',
        'getProductReviews' => 'Product Reviews',
    ];
@endphp

@extends('panel.layout.app', ['disable_tblr' => true, 'disable_mobile_bottom_menu' => true])
@section('title', $setting->site_name . __('Bots'))
@section('titlebar_subtitle')
    {{ __('View and manage external chatbots') }}
@endsection
@section('titlebar_actions')
    <x-button
        href="{{ route('dashboard.chatbot.analytics.index') }}"
        variant="ghost-shadow"
    >
        @lang('Analytics')
    </x-button>

    <x-button
        href="#"
        @click.prevent="$store.externalChatbotEditor.setActiveChatbot('new_chatbot', 1, true);"
        x-data="{}"
    >
        <x-tabler-plus class="size-4" />
        @lang('Add New Chatbot')
    </x-button>
@endsection

@section('content')
    <div class="py-10">
        <div
            class="lqd-external-chatbot-edit"
            x-data="externalChatbotEditor"
            @keydown.escape.window="setActiveChatbot(null)"
        >
            @include('chatbot::home.actions-grid')

            @include('chatbot::home.chatbots-list', ['chatbots' => $chatbots])

            @include('chatbot::home.edit-window.edit-window', ['avatars' => $avatars])
        </div>

        @include('chatbot::home.chats-history.chats-history')
    </div>
@endsection

@push('script')
    <link
        rel="stylesheet"
        href="{{ custom_theme_url('/assets/libs/prism/prism.css') }}"
    />
    <link
        rel="stylesheet"
        href="{{ custom_theme_url('assets/libs/jscolorpicker/dist/colorpicker.css') }}"
    >
    <script src="{{ custom_theme_url('assets/libs/jscolorpicker/dist/colorpicker.iife.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/prism/prism.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/beautify-html.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/markdownit/markdown-it.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/turndown.js') }}"></script>

    <script>
        (() => {
            document.addEventListener('alpine:init', () => {
                Alpine.data('externalChatbotEditor', () => ({
                    chatbots: @json($chatbots),
                    activeChatbot: {},
                    prevActiveChatbotId: null,
                    editingStep: 1,
                    submittingData: false,
                    // used for the chatbot ui
                    externalChatbot: null,
                    // used for the training tab
                    externalChatbotTraining: null,
                    testIframeWidth: 420,
                    testIframeHeight: 745,
                    suggestedPromptModal: {
                        open: false,
                        mode: 'create',
                        index: null,
                        form: {
                            name: '',
                            prompt: '',
                        },
                    },
                    defaultFormInputs: {
                        id: '',
                        interaction_type: 'automatic_response',
                        title: '{{ $setting->site_name . __('Bots') }}',
                        bubble_message: '{{ __('Hey there, How can I help you?') }}',
                        welcome_message: '{{ __('Hi, how can I help you?') }}',
                        connect_message: '{{ __('I’ve forwarded your request to a human agent. An agent will connect with you as soon as possible.') }}',
                        instructions: '',
                        do_not_go_beyond_instructions: 0,
                        suggested_prompts: [],
                        suggested_prompts_enabled: false,
                        language: '',
                        ai_model: 'gpt-3.5-turbo',
                        logo: '',
                        avatar: (@json($avatars?->isEmpty() ? [] : $avatars->first()))?.avatar || '{{ $user_avatar }}',
                        color: '#272733',
                        show_logo: true,
                        show_date_and_time: true,
                        show_average_response_time: true,
                        trigger_background: '',
                        trigger_avatar_size: '60px',
                        position: 'right',
                        active: true,
                        footer_link: '',
                        whatsapp_link: '',
                        telegram_link: '',
                        facebook_link: '',
                        instagram_link: '',
                        watch_product_tour_link: '',
                        privacy_policy_link: '',
                        terms_of_service_link: '',
                        show_social_links_in_first_message: false,
                        is_email_collect: true,
                        is_contact: true,
                        is_attachment: true,
                        is_emoji: true,
                        is_articles: true,
                        is_links: true,
                        is_gdpr: false,
                        header_bg_type: 'color',
                        header_bg_color: '',
                        header_bg_gradient: '',
                        header_bg_image: '',
                        header_bg_image_blob: null,
                        welcome_bg_image: '',
                        welcome_bg_image_blob: null,
                        human_agent_conditions: [],
                        is_booking_assistant: 0,
                        booking_assistant_conditions: [],
                        booking_assistant_iframe: '',
                        voice_call_enabled: false,
                        voice_call_first_message: '',
                        trusted_domains: [],
                        bubble_design: 'blank',
                        promo_banner_image: '',
                        promo_banner_image_blob: null,
                        promo_banner_title: '',
                        promo_banner_description: '',
                        promo_banner_btn_label: '',
                        promo_banner_btn_link: '',
                        is_review_enabled: false,
                        review_prompt: '',
                        review_responses: [],
                        is_shop: false,
                        shop_source: 'shopify',
                        shop_features: [],
                        shopify_domain: '',
                        shopify_access_token: '',
                        woocommerce_domain: '',
                        woocommerce_consumer_key: '',
                        woocommerce_consumer_secret: ''
                    },
                    reviewMaxResponses: 5,
                    reviewResponsesLimitMessage: '{{ __('You can add up to :count review responses.', ['count' => 5]) }}',
                    formErrors: {},
                    contactInfo: {
                        activeTab: 'details',
                        editMode: false,
                    },
                    mobile: {
                        filtersVisible: false,
                        contactInfoVisible: false,
                    },

                    init() {
                        this.ensureChatbotsReviewPayload();
                        this.createNewChatObj();
                        this.initFormErrors();

                        Alpine.store('externalChatbotEditor', this);
                    },
                    createNewChatObj() {
                        this.chatbots.data.unshift({
                            ...this.defaultFormInputs,
                            id: 'new_chatbot',
                            suggested_prompts: [],
                            suggested_prompts_enabled: false,
                            human_agent_conditions: [],
                            trusted_domains: [],
                        });

                        this.ensureSuggestedPromptsState(this.chatbots.data[0]);
                        this.hydrateChatbotReviewPayload(this.chatbots.data[0]);
                    },
                    initFormErrors() {
                        Object.keys(this.defaultFormInputs).forEach(key => {
                            this.formErrors[key] = [];
                        });
                    },
                    setActiveChatbot(chatbotId, step, skipCRUD = false) {
                        const topNoticeBar = document.querySelector('.top-notice-bar');
                        const navbar = document.querySelector('.lqd-navbar');
                        const pageContentWrap = document.querySelector('.lqd-page-content-wrap');
                        const navbarExpander = document.querySelector('.lqd-navbar-expander');

                        const activeChatbotId = this.activeChatbot.id;

                        this.activeChatbot = this.chatbots.data.find(c => c.id === chatbotId) || {
                            id: chatbotId
                        };
                        this.hydrateChatbotReviewPayload(this.activeChatbot);
                        this.closeReviewModal();

                        this.ensureSuggestedPromptsState(this.activeChatbot);
                        this.resetSuggestedPromptModal();

                        if (activeChatbotId) {
                            this.prevActiveChatbotId = activeChatbotId;
                        }

                        if (step) {
                            this.setEditingStep(step, skipCRUD);
                        }

                        this.formErrors = {};

                        document.documentElement.style.overflow = this.activeChatbot.id ? 'hidden' : '';

                        if (window.innerWidth >= 992) {

                            if (navbar) {
                                navbar.style.position = this.activeChatbot.id ? 'fixed' : '';
                            }

                            if (pageContentWrap && navbar?.offsetWidth > 0) {
                                pageContentWrap.style.paddingInlineStart = this.activeChatbot.id ? 'var(--navbar-width)' : '';
                            }

                            if (topNoticeBar) {
                                topNoticeBar.style.visibility = this.activeChatbot.id ? 'hidden' :
                                    '';
                            }

                            if (navbarExpander) {
                                navbarExpander.style.visibility = this.activeChatbot.id ? 'hidden' :
                                    '';
                                navbarExpander.style.opacity = this.activeChatbot.id ? 0 : 1;
                            }
                        }
                    },
                    async setEditingStep(step, skipCRUD = false) {
                        const prevStep = this.editingStep;
                        let editingStep = step;

                        if (step === '>') {
                            editingStep = Math.min(4, this.editingStep + 1);
                        } else if (step === '<') {
                            editingStep = Math.max(1, this.editingStep - 1);
                        }

                        if (
                            !skipCRUD &&
                            prevStep !== editingStep &&
                            prevStep === 1 &&
                            this.activeChatbot.id === 'new_chatbot'
                        ) {
                            await this.createNewChatbot();
                            return;
                        }

                        if (
                            !skipCRUD &&
                            prevStep !== editingStep &&
                            (prevStep === 2 || (prevStep === 1 && editingStep === 2)) &&
                            this.activeChatbot.id !== 'new_chatbot'
                        ) {
                            await this.updateChatbot();
                        }

                        if (
                            !skipCRUD &&
                            this.externalChatbotTraining != null &&
                            editingStep === 3 &&
                            this.activeChatbot.id !== 'new_chatbot'
                        ) {
                            this.externalChatbotTraining.fetchEmbeddings();
                        }

                        this.prevEditingStep = this.editingStep;
                        this.editingStep = editingStep;
                    },
                    async toggleChatbotActivation(chatbotId) {
                        const chatbot = this.chatbots.data.find(c => c.id === chatbotId);

                        if (!chatbot) return;

                        await this.updateChatbot(chatbot);
                    },
                    async deleteChatbot(event) {
                        if (!confirm(
                                '{{ __('Are you sure you want to delete this chatbot?') }}')) {
                            return;
                        }

                        const form = event.target;
                        const id = form.elements['id'].value;
                        const chatbotIndex = this.chatbots.data.findIndex(c => c.id == id);

                        this.submittingData = true;

                        const res = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: this.getFormData(this.chatbots.data.at(chatbotIndex))
                        });

                        if (!res.ok) {

                            const data = await res.json();

                            toastr.error(data.message);

                            return;
                        }

                        const data = await res.json();

                        if (data.type !== 'success') {
                            toastr.error(data.message);
                            return;
                        }

                        if (chatbotIndex !== -1) {
                            this.chatbots.data.splice(chatbotIndex, 1);
                        }

                        this.submittingData = false;

                        toastr.clear();
                        toastr.success(data.message ||
                            '{{ __('Chatbot deleted successfully') }}');
                    },
                    training: {
                        activeTab: 'website',
                        setActiveTab(tab) {
                            if (this.activeTab === tab) return;
                            this.activeTab = tab;
                        }
                    },
                    async createNewChatbot() {
                        this.submittingData = true;
                        this.formErrors = {};

                        const res = await fetch('{{ route('dashboard.chatbot.store') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                // 'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: this.getFormData()
                        });
                        const data = await res.json();
                        const {
                            data: chatbotData
                        } = data;

                        this.submittingData = false;

                        if (!res.ok || !chatbotData) {
                            if (data.errors) {
                                this.formErrors = data.errors;
                            } else if (data.message) {
                                toastr.error(data.message);
                            }

                            this.setEditingStep(1, true);
                            return;
                        }

                        this.chatbots.data.shift();

                        this.chatbots.data.unshift({
                            ...this.defaultFormInputs,
                            ...chatbotData
                        });
                        this.hydrateChatbotReviewPayload(this.chatbots.data[0]);
                        this.ensureSuggestedPromptsState(this.chatbots.data[0]);

                        this.setActiveChatbot(chatbotData.id);
                        this.setEditingStep(2, true);
                        this.createNewChatObj();

                        toastr.clear();
                        toastr.success('{{ __('Chatbot created successfully') }}');
                    },
                    async updateChatbot(chatbot) {
                        this.submittingData = true;
                        this.formErrors = {};

                        const res = await fetch('{{ route('dashboard.chatbot.update') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                // Content-Type header'ını kaldırıyoruz, FormData kendi ayarlayacak
                            },
                            body: this.getFormData(chatbot)
                        });

                        const data = await res.json();
                        const {
                            data: chatbotData
                        } = data;

                        this.submittingData = false;

                        if (!res.ok || !chatbotData) {
                            if (data.errors) {
                                this.formErrors = data.errors;
                            } else if (data.message) {
                                toastr.error(data.message);
                            }
                            return;
                        }

                        const chatbotIndex = this.chatbots.data.findIndex(c => c.id === chatbotData.id);

                        if (chatbotIndex !== -1) {
                            this.chatbots.data[chatbotIndex] = {
                                ...this.chatbots.data[chatbotIndex],
                                ...chatbotData,
                            };

                            this.ensureSuggestedPromptsState(this.chatbots.data[chatbotIndex]);
                            this.hydrateChatbotReviewPayload(this.chatbots.data[chatbotIndex]);
                        }

                        if (this.activeChatbot?.id === chatbotData.id) {
                            this.hydrateChatbotReviewPayload(this.activeChatbot);
                            this.ensureSuggestedPromptsState(this.activeChatbot);
                        }

                        toastr.clear();
                        toastr.success('{{ __('Chatbot updated successfully') }}');
                    },

                    onHumanAgentConditionsChange(event) {
                        const checkboxEl = event.currentTarget;
                        const conditionValue = checkboxEl.getAttribute('data-condition')?.trim();

                        if (!conditionValue) return;

                        if (!this.activeChatbot.human_agent_conditions) {
                            this.activeChatbot.human_agent_conditions = [];
                        }

                        const existingConditionIndex = this.activeChatbot.human_agent_conditions.findIndex(condition => condition === conditionValue);

                        if (checkboxEl.checked && existingConditionIndex === -1) {
                            this.activeChatbot.human_agent_conditions.push(conditionValue);
                        } else if (!checkboxEl.checked) {
                            this.activeChatbot.human_agent_conditions.splice(existingConditionIndex, 1);
                        }
                    },

                    openSuggestedPromptModal(mode = 'create', index = null) {
                        if (!Array.isArray(this.activeChatbot.suggested_prompts)) {
                            this.activeChatbot.suggested_prompts = [];
                        }

                        this.suggestedPromptModal.mode = mode;
                        this.suggestedPromptModal.index = index;

                        if (mode === 'edit' && typeof index === 'number' && this.activeChatbot.suggested_prompts[index]) {
                            const targetPrompt = this.activeChatbot.suggested_prompts[index];

                            this.suggestedPromptModal.form = {
                                name: targetPrompt.name ?? '',
                                prompt: targetPrompt.prompt ?? '',
                            };
                        } else {
                            this.suggestedPromptModal.form = {
                                name: '',
                                prompt: '',
                            };
                        }

                        this.suggestedPromptModal.open = true;
                    },

                    closeSuggestedPromptModal() {
                        this.suggestedPromptModal.open = false;

                        this.$nextTick(() => {
                            this.resetSuggestedPromptModal(false);
                        });
                    },

                    resetSuggestedPromptModal(resetVisibility = true) {
                        if (resetVisibility) {
                            this.suggestedPromptModal.open = false;
                        }

                        this.suggestedPromptModal.mode = 'create';
                        this.suggestedPromptModal.index = null;
                        this.suggestedPromptModal.form = {
                            name: '',
                            prompt: '',
                        };
                    },

                    saveSuggestedPrompt() {
                        const name = (this.suggestedPromptModal.form.name || '').trim();
                        const prompt = (this.suggestedPromptModal.form.prompt || '').trim();

                        if (!name && !prompt) {
                            toastr.error('{{ __('Please provide at least a prompt name or the prompt itself.') }}');
                            return;
                        }

                        const payload = {
                            name,
                            prompt
                        };

                        if (!Array.isArray(this.activeChatbot.suggested_prompts)) {
                            this.activeChatbot.suggested_prompts = [];
                        }

                        if (this.suggestedPromptModal.mode === 'edit' && typeof this.suggestedPromptModal.index === 'number') {
                            this.activeChatbot.suggested_prompts.splice(this.suggestedPromptModal.index, 1, payload);
                        } else {
                            this.activeChatbot.suggested_prompts.push(payload);
                        }

                        this.activeChatbot.suggested_prompts_enabled = this.activeChatbot.suggested_prompts.length > 0;

                        this.closeSuggestedPromptModal();
                    },

                    removeSuggestedPrompt(index) {
                        if (!Array.isArray(this.activeChatbot.suggested_prompts)) {
                            this.activeChatbot.suggested_prompts = [];
                            return;
                        }

                        if (index < 0 || index >= this.activeChatbot.suggested_prompts.length) {
                            return;
                        }

                        this.activeChatbot.suggested_prompts.splice(index, 1);

                        if (this.activeChatbot.suggested_prompts.length === 0) {
                            this.activeChatbot.suggested_prompts_enabled = false;
                        }
                    },

                    toggleSuggestedPrompts(enabled) {
                        if (enabled) {
                            if (!Array.isArray(this.activeChatbot.suggested_prompts)) {
                                this.activeChatbot.suggested_prompts = [];
                            }

                            this.activeChatbot.suggested_prompts_enabled = true;

                            if (this.activeChatbot.suggested_prompts.length === 0) {
                                this.openSuggestedPromptModal('create');
                            }
                        } else {
                            this.activeChatbot.suggested_prompts_enabled = false;
                            this.activeChatbot.suggested_prompts = [];
                        }
                    },

                    ensureSuggestedPromptsState(chatbot) {
                        if (!chatbot) return;

                        if (!Array.isArray(chatbot.suggested_prompts)) {
                            chatbot.suggested_prompts = [];
                        }

                        if (typeof chatbot.suggested_prompts_enabled !== 'boolean') {
                            chatbot.suggested_prompts_enabled = chatbot.suggested_prompts.length > 0;
                        }
                    },

                    onBookingAssistantConditionsChange(event) {
                        const checkboxEl = event.currentTarget;
                        const conditionValue = checkboxEl.getAttribute('data-condition')?.trim();

                        if (!conditionValue) return;

                        if (!this.activeChatbot.booking_assistant_conditions) {
                            this.activeChatbot.booking_assistant_conditions = [];
                        }

                        const existingConditionIndex = this.activeChatbot.booking_assistant_conditions.findIndex(condition => condition === conditionValue);

                        if (checkboxEl.checked && existingConditionIndex === -1) {
                            this.activeChatbot.booking_assistant_conditions.push(conditionValue);
                        } else if (!checkboxEl.checked) {
                            this.activeChatbot.booking_assistant_conditions.splice(existingConditionIndex, 1);
                        }
                    },

                    getFormData(chatbot) {
                        const chatbotData = chatbot || this.activeChatbot;
                        const formData = new FormData();

                        Object.keys(chatbotData).forEach(key => {
                            const value = chatbotData[key];

                            if (value instanceof File) {
                                formData.append(key, value);
                            } else if (Array.isArray(value)) {
                                value.forEach((item, index) => {
                                    if (item instanceof File) {
                                        formData.append(`${key}[${index}]`, item);
                                    } else if (typeof item === 'object' && item !== null) {
                                        Object.keys(item).forEach(nestedKey => {
                                            const nestedValue = item[nestedKey];

                                            if (nestedValue instanceof File) {
                                                formData.append(`${key}[${index}][${nestedKey}]`, nestedValue);
                                            } else if (typeof nestedValue === 'boolean') {
                                                formData.append(`${key}[${index}][${nestedKey}]`, nestedValue ? 1 : 0);
                                            } else if (nestedValue !== null && nestedValue !== undefined) {
                                                formData.append(`${key}[${index}][${nestedKey}]`, nestedValue);
                                            } else {
                                                formData.append(`${key}[${index}][${nestedKey}]`, '');
                                            }
                                        });
                                    } else if (typeof item === 'boolean') {
                                        formData.append(`${key}[${index}]`, item ? 1 : 0);
                                    } else if (item !== null && item !== undefined) {
                                        formData.append(`${key}[${index}]`, item);
                                    }
                                });
                            } else if (typeof value === 'boolean') {
                                formData.append(key, value ? 1 : 0);
                            } else if (value !== null && value !== undefined) {
                                formData.append(key, value);
                            }
                        });

                        return formData;
                    },
                    ensureChatbotsReviewPayload() {
                        if (!this.chatbots?.data) {
                            return;
                        }

                        this.chatbots.data.forEach(chatbot => this.hydrateChatbotReviewPayload(chatbot));
                    },
                    hydrateChatbotReviewPayload(chatbot) {
                        if (!chatbot || typeof chatbot !== 'object') {
                            return;
                        }

                        chatbot.is_review_enabled = Boolean(chatbot.is_review_enabled);

                        const responses = Array.isArray(chatbot.review_responses) ?
                            chatbot.review_responses :
                            (chatbot.review_responses ? Object.values(chatbot.review_responses) : []);

                        chatbot.review_responses = responses
                            .filter(response => typeof response === 'string')
                            .slice(0, this.reviewMaxResponses);

                        if (chatbot.review_prompt == null) {
                            chatbot.review_prompt = '';
                        }
                    },
                    onReviewToggleChange(checked) {
                        if (checked) {
                            this.hydrateChatbotReviewPayload(this.activeChatbot);
                            this.openReviewModal();
                        } else {
                            this.closeReviewModal();
                        }
                    },
                    openReviewModal() {
                        if (!this.activeChatbot?.id) {
                            return;
                        }

                        const reviewModal = document.getElementById('chatbot-review-modal');

                        if (!reviewModal) return;

                        const modalData = Alpine.$data(reviewModal);

                        if (!modalData) return;

                        this.hydrateChatbotReviewPayload(this.activeChatbot);
                        modalData.modalOpen = true;
                    },
                    closeReviewModal() {
                        const reviewModal = document.getElementById('chatbot-review-modal');

                        if (!reviewModal) return;

                        const modalData = Alpine.$data(reviewModal);

                        if (!modalData) return;

                        modalData.modalOpen = false;
                    },
                    addReviewResponse() {
                        if (!this.activeChatbot) {
                            return;
                        }

                        this.hydrateChatbotReviewPayload(this.activeChatbot);

                        if (this.activeChatbot.review_responses.length >= this.reviewMaxResponses) {
                            toastr.info(this.reviewResponsesLimitMessage);
                            return;
                        }

                        this.activeChatbot.review_responses.push('');
                    },
                    removeReviewResponse(index) {
                        if (!this.activeChatbot?.review_responses) {
                            return;
                        }

                        this.activeChatbot.review_responses.splice(index, 1);
                    },
                    onShopFeaturesChange(event) {
                        const checkboxEl = event.currentTarget;
                        const conditionValue = checkboxEl.getAttribute('data-condition')?.trim();

                        if (!conditionValue) return;

                        if (!this.activeChatbot.shop_features) {
                            this.activeChatbot.shop_features = [];
                        }

                        const existingConditionIndex = this.activeChatbot.shop_features.findIndex(condition => condition === conditionValue);

                        if (checkboxEl.checked && existingConditionIndex === -1) {
                            this.activeChatbot.shop_features.push(conditionValue);
                        } else if (!checkboxEl.checked) {
                            this.activeChatbot.shop_features.splice(existingConditionIndex, 1);
                        }
                    }
                }));
            });
        })();
    </script>
@endpush
