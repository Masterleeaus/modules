@includeIf('chatbot-voice-call::particles.voice-call-scripts')

<script>
    (() => {
        document.addEventListener('alpine:init', () => {
            Alpine.data('externalChatbot', (isEditor = false) => ({
                isEditorMode: isEditor,
                conversations: [],
                activeConversationData: null,
                activeChatbot: @json(isset($chatbot) ? (is_array($chatbot) ? $chatbot : $chatbot->toArray()) : []),
                trustedDomains: @json(isset($chatbot) ? (is_array($chatbot) ? $chatbot['trusted_domains'] ?? [] : $chatbot->trusted_domains ?? []) : []),
                parentOrigin: null,
                showConnectButtons: true,
                showConnectButtonsStepTwo: false,
                showConnectButtonsStepOne: true,
                connectingToAgent: false,
                connect_agent_at: null,
                showBookingButtons: true,
                showBookingIframe: false,
                messages: [],
                activeConversation: null,
                fetching: false,
                windowState: 'close',
                assistantMessageBubbles: {},
                prevViews: [],
                currentView: '{{ $is_editor ? 'conversation-messages' : 'welcome' }}',
                ablyInstance: null,
                ablyChannel: null,
                articles: {!! $is_editor ? '[]' : $chatbot->articles()->toJson() !!},
                showEmojiPicker: false,
                uploading: false,
                pendingMedia: null,
                pendingMediaPreview: null,
                updatingCart: false,
                widgetStatus: {
                    type: null,
                    message: null
                },
                suggestedPrompts: @json(isset($chatbot) ? (is_array($chatbot) ? $chatbot['suggested_prompts'] ?? [] : $chatbot->suggested_prompts ?? []) : []),
                suggestedPromptFallbackLabel: '{{ __('Prompt') }}',
                collectingEmail: false,
                searchingArticles: false,
                showingArticle: null,
                showOptionsDropdown: false,
                soundEnabled: {{ isset($chatbot) ? $chatbot->getAttribute('enable_sound') ?? 'true' : 'true' }},
                cart: {},
                reviewRouteTemplate: '{{ $is_editor ? '' : $routes['review'] ?? '' }}',
                conversationRouteTemplate: '{{ $is_editor ? '' : $routes['conversation'] ?? '' }}',
                reviewInactivityDelay: 600000,
                reviewDismissed: {},
                reviewRequesting: false,
                reviewWidget: {
                    visible: false,
                    reason: null,
                    submitting: false,
                    error: null,
                },
                showExportOptions: false,

                init() {
                    this.windowState = this.$el.getAttribute('data-window-state');

                    this.handleWindowMessages = this.handleWindowMessages.bind(this);
                    this.toggleWindowState = this.toggleWindowState.bind(this);
                    this.onSendMessage = this.onSendMessage.bind(this);
                    this.scrollMessagesToBottom = this.scrollMessagesToBottom.bind(this);
                    this.doNotConnectToAgent = this.doNotConnectToAgent.bind(this);
                    this.onTypingDone = this.onTypingDone.bind(this);

                    @if ($is_editor)
                        this.$data.externalChatbot = this;

                        this.fillDemoConversations();
                        this.fillDemoMessages();
                        this.fillDemoArticles();

                        this.syncActiveChatbotFromEditorStore();

                        this.$watch('activeChatbot', () => {
                            this.$nextTick(() => this.fillDemoBubbleData());
                        });

                        this.$watch(
                            () => {
                                if (typeof Alpine.store !== 'function') return null;

                                return Alpine.store('externalChatbotEditor')?.activeChatbot ?? null;
                            },
                            (value) => {
                                if (!value) return;

                                this.activeChatbot = value;
                                this.updateSuggestedPrompts(value?.suggested_prompts);
                                this.$nextTick(() => this.fillDemoBubbleData());
                            }
                        );

                        @if ($is_editor)
                            this.$watch('activeChatbot.voice_call_enabled', (val) => {
                                Alpine.store('voiceCall').enabled = !!val;
                            });
                        @endif
                    @else
                        this.getSession();
                        if (window.self !== window.top) {
                            window.addEventListener("message", this.handleWindowMessages);
                            document.documentElement.classList.add('lqd-ext-chatbot-embedded');

                            if (document.referrer) {
                                try {
                                    const referrerOrigin = new URL(document.referrer).origin;
                                    if (this.isOriginTrusted(referrerOrigin)) {
                                        this.parentOrigin = referrerOrigin;
                                    }
                                } catch {}
                            }

                            if (this.parentOrigin) {
                                window.parent.postMessage({
                                    type: 'lqd-ext-chatbot-session-id',
                                    sessionId: '{{ $session }}'
                                }, this.parentOrigin);
                            }
                        }
                    @endif

                    this.initEmojiPicker();
                    this.initAudioContext();

                    this.updateSuggestedPrompts(this.suggestedPrompts.length ? this.suggestedPrompts : this.activeChatbot?.suggested_prompts);

                    this.$watch('activeChatbot', (value) => {
                        this.updateSuggestedPrompts(value?.suggested_prompts);
                    });

                    this.$watch('activeChatbot.suggested_prompts', (value) => {
                        this.updateSuggestedPrompts(value);
                    });

                    this.$watch('activeChatbot.suggested_prompts_enabled', (value) => {
                        this.updateSuggestedPrompts(value ? this.activeChatbot?.suggested_prompts : []);
                    });

                    this.$watch('currentView', view => {
                        if (view === 'conversation-messages' && this.suggestedPrompts.length && !this.messages.find(m => m.role === 'user')) {
                            this.$nextTick(() => {
                                this.$refs.conversationMessagesContainer.style.paddingBottom =
                                    `${this.$refs.suggestedPromptsContainer.offsetHeight + 20}px`;
                                this.scrollMessagesToBottom();
                            })
                        }
                    })
                },
                @if ($is_editor)
                    fillDemoConversations() {
                            const demoConversations = [{
                                    id: 0,
                                    last_message: '{{ __('I noted some aspects of the platform that need...') }}',
                                    created_at: new Date(Date.now() - 3600000).toISOString(),
                                },
                                {
                                    id: 1,
                                    last_message: '{{ __('I noted some aspects of the platform that need...') }}',
                                    created_at: new Date(Date.now() - 3600000).toISOString(),
                                },
                                {
                                    id: 2,
                                    last_message: '{{ __('I noted some aspects of the platform that need...') }}',
                                    created_at: new Date(Date.now() - 3600000).toISOString(),
                                },
                                {
                                    id: 3,
                                    last_message: '{{ __('I noted some aspects of the platform that need...') }}',
                                    created_at: new Date(Date.now() - 3600000).toISOString(),
                                },
                                {
                                    id: 4,
                                    last_message: '{{ __('I noted some aspects of the platform that need...') }}',
                                    created_at: new Date(Date.now() - 3600000).toISOString(),
                                },
                                {
                                    id: 5,
                                    last_message: '{{ __('I noted some aspects of the platform that need...') }}',
                                    created_at: new Date(Date.now() - 3600000).toISOString(),
                                },
                            ];

                            this.conversations = demoConversations;
                        },
                        fillDemoMessages() {
                            const demoMessages = [{
                                    id: 0,
                                    message: '{{ __('Hi, how can I help you?') }}',
                                    role: 'assistant',
                                    created_at: new Date(Date.now() - 3600000).toISOString(),
                                },
                                {
                                    id: 1,
                                    message: '{{ __('I need to make a refund.') }}',
                                    role: 'user',
                                    created_at: new Date(Date.now() - 3500000).toISOString(),
                                },
                                {
                                    id: 2,
                                    message: '{{ __('A refund will be provided after we process your return item at our facilities. It may take additional time for your financial institution to  process the refund.') }}',
                                    role: 'assistant',
                                    created_at: new Date(Date.now() - 3400000).toISOString(),
                                },
                                {
                                    id: 3,
                                    message: '{{ __('🤔') }}',
                                    role: 'user',
                                    created_at: new Date(Date.now()).toISOString(),
                                },
                            ];

                            this.messages = demoMessages;
                        },
                        fillDemoArticles() {
                            const demoArticles = [{
                                    title: '{{ __('Social Media Suite') }}',
                                    excerpt: '{{ __('Hey there, Welcome to our support center. I’ll be happy to assist you...') }}',
                                    link: '#',
                                },
                                {
                                    title: '{{ __('Marketing Bot') }}',
                                    excerpt: '{{ __('If you need further assistance, feel free to reach out via our contact form.') }}',
                                    link: '#',
                                },
                                {
                                    title: '{{ __('LMS') }}',
                                    excerpt: '{{ __('We have a collection of guides and tutorials to help you navigate our services.') }}',
                                    link: '#',
                                },
                            ];

                            this.articles = demoArticles;
                        },
                        fillDemoBubbleData() {
                            const design = this.activeChatbot?.bubble_design;

                            if (!design || !this.isEditorMode) return;

                            if (design === 'promo_banner') {
                                if (!this.activeChatbot.promo_banner_title) {
                                    this.activeChatbot.promo_banner_title = '{{ __('Special Offer!') }}';
                                }
                                if (!this.activeChatbot.promo_banner_description) {
                                    this.activeChatbot.promo_banner_description = '{{ __('Get 20% off on all plans this week. Don\'t miss out on this limited-time deal.') }}';
                                }
                                if (!this.activeChatbot.promo_banner_btn_label) {
                                    this.activeChatbot.promo_banner_btn_label = '{{ __('Learn More') }}';
                                }
                                if (!this.activeChatbot.promo_banner_btn_link) {
                                    this.activeChatbot.promo_banner_btn_link = '#';
                                }
                                if (!this.activeChatbot.promo_banner_image) {
                                    this.activeChatbot.promo_banner_image = 'https://placehold.co/280x155/e2e8f0/64748b?text=Promo+Banner';
                                }
                            }

                            if (design === 'suggestions') {
                                const prompts = this.activeChatbot.suggested_prompts;
                                const hasPrompts = Array.isArray(prompts) && prompts.some(p => (p?.name || p?.prompt || '').trim());

                                if (!hasPrompts) {
                                    this.activeChatbot.suggested_prompts = this.getDemoSuggestedPrompts();
                                    this.updateSuggestedPrompts(this.activeChatbot.suggested_prompts);
                                }
                            }

                            if (design === 'links') {
                                if (!this.activeChatbot.whatsapp_link) {
                                    this.activeChatbot.whatsapp_link = 'https://wa.me/1234567890';
                                }
                                if (!this.activeChatbot.telegram_link) {
                                    this.activeChatbot.telegram_link = 'https://t.me/example';
                                }
                            }
                        },
                        syncActiveChatbotFromEditorStore() {
                            if (typeof Alpine.store !== 'function') {
                                requestAnimationFrame(() => this.syncActiveChatbotFromEditorStore());
                                return;
                            }

                            const store = Alpine.store('externalChatbotEditor');

                            if (!store || !store.activeChatbot) {
                                requestAnimationFrame(() => this.syncActiveChatbotFromEditorStore());
                                return;
                            }

                            this.activeChatbot = store.activeChatbot;
                            this.updateSuggestedPrompts(store.activeChatbot?.suggested_prompts);
                            this.$nextTick(() => this.fillDemoBubbleData());
                        },
                @endif
                initEmojiPicker() {
                    const picker = picmo.createPicker({
                        rootElement: this.$refs.emojiPicker
                    });

                    picker.addEventListener('emoji:select', event => {
                        this.$refs.message.value += event.emoji;
                        this.showEmojiPicker = false;

                        this.$refs.message.focus();
                        this.$refs.message.dispatchEvent(new Event('input'));
                    });
                },

                initAudioContext() {
                    try {
                        this.audioContext = new(window.AudioContext || window.webkitAudioContext)();
                    } catch (e) {
                        console.warn('Web Audio API not supported');
                    }
                },

                playBubbleSound() {
                    if (!this.audioContext || !this.soundEnabled) return;

                    try {
                        // Resume context if suspended (required for user interaction)
                        if (this.audioContext.state === 'suspended') {
                            this.audioContext.resume();
                        }

                        const now = this.audioContext.currentTime;

                        // Create oscillators for iMessage-style sound
                        const osc1 = this.audioContext.createOscillator();
                        const osc2 = this.audioContext.createOscillator();

                        // Create gain nodes
                        const gain1 = this.audioContext.createGain();
                        const gain2 = this.audioContext.createGain();
                        const masterGain = this.audioContext.createGain();

                        // Create a subtle reverb effect using delay
                        const delay = this.audioContext.createDelay();
                        const delayGain = this.audioContext.createGain();

                        // High-pass filter for crisp sound
                        const filter = this.audioContext.createBiquadFilter();
                        filter.type = 'highpass';
                        filter.frequency.setValueAtTime(200, now);
                        filter.Q.setValueAtTime(0.7, now);

                        // Connect the audio graph
                        osc1.connect(gain1);
                        osc2.connect(gain2);

                        gain1.connect(filter);
                        gain2.connect(filter);

                        filter.connect(masterGain);
                        filter.connect(delay);

                        delay.connect(delayGain);
                        delayGain.connect(masterGain);

                        masterGain.connect(this.audioContext.destination);

                        // Configure oscillators for iMessage-like sound
                        osc1.type = 'sine';
                        osc2.type = 'sine';

                        // First tone - bright and clear
                        osc1.frequency.setValueAtTime(1046.5, now); // C6
                        osc1.frequency.exponentialRampToValueAtTime(1318.5, now + 0.05); // E6

                        // Second tone - harmonic
                        osc2.frequency.setValueAtTime(1318.5, now); // E6
                        osc2.frequency.exponentialRampToValueAtTime(1568, now + 0.05); // G6

                        // Configure delay for subtle reverb
                        delay.delayTime.setValueAtTime(0.02, now);
                        delayGain.gain.setValueAtTime(0.1, now);

                        // Create sharp attack and quick decay (iMessage characteristic)
                        // Main oscillator envelope
                        gain1.gain.setValueAtTime(0, now);
                        gain1.gain.linearRampToValueAtTime(0.3, now + 0.005);
                        gain1.gain.exponentialRampToValueAtTime(0.15, now + 0.05);
                        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.15);

                        // Harmonic oscillator envelope
                        gain2.gain.setValueAtTime(0, now);
                        gain2.gain.linearRampToValueAtTime(0.2, now + 0.01);
                        gain2.gain.exponentialRampToValueAtTime(0.1, now + 0.06);
                        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.18);

                        // Master gain envelope
                        masterGain.gain.setValueAtTime(0.8, now);
                        masterGain.gain.exponentialRampToValueAtTime(0.001, now + 0.2);

                        // Start oscillators
                        osc1.start(now);
                        osc2.start(now + 0.002);

                        // Stop oscillators
                        osc1.stop(now + 0.2);
                        osc2.stop(now + 0.22);

                    } catch (e) {
                        console.warn('Could not play bubble sound:', e);
                    }
                },

                async initAbly() {
                    @if (isset($session) && setting('ably_public_key') && setting('ably_public_key') !== '' && is_string(setting('ably_public_key')) && strlen(setting('ably_public_key')) > 8)
                        if (!this.ablyInstance) {
                            this.ablyInstance = new Ably.Realtime.Promise(
                                "{{ setting('ably_public_key') }}");
                        }

                        let channelName = 'conversation-session-{{ $session }}';

                        this.ablyChannel = this.ablyInstance.channels.get(channelName);

                        await this.ablyChannel.subscribe('new-message', (message) => {
                            let data = message.data;

                            let incomingConversationId = data.conversationId;
                            let isWindowOpen = this.windowState === 'open';
                            let isActiveConversation = isWindowOpen && incomingConversationId === this.activeConversation;

                            if (isActiveConversation) {
                                this.messages.push(data.history);
                                this.scrollMessagesToBottom();

                                // Mark as read since the visitor is viewing this conversation
                                fetch(`{{ isset($routes) ? $routes['conversations'] : '' }}/${this.activeConversation}/messages?per_page=1`, {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                }).then(() => {
                                    this.notifyParentUnreadChange();
                                }).catch(() => {});
                            } else if (incomingConversationId === this.activeConversation) {
                                // Window is closed but this is the selected conversation — still push the message
                                this.messages.push(data.history);
                            }

                            this.conversations = this.conversations.map((conversation) => {
                                if (conversation.id === incomingConversationId) {
                                    return {
                                        ...conversation,
                                        last_message: data.history.message,
                                        unread_count: !isActiveConversation ?
                                            (conversation.unread_count || 0) + 1 : conversation.unread_count,
                                    };
                                }
                                return conversation;
                            });

                            if (!isActiveConversation) {
                                this.notifyParentUnreadChange();
                            }

                            this.playBubbleSound();
                        });
                    @endif
                },
                connectToAgent() {
                    @if (isset($chatbot) && isset($session) && \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-agent'))
                        let route = '{{ route('api.v2.chatbot.conversion.connect.support', [$chatbot->uuid, $session]) }}';

                        this.connectingToAgent = true;

                        this.messages.push({
                            id: 'connecting-to-agent',
                            role: 'connecting-to-agent',
                            created_at: Date.now()
                        });

                        this.scrollMessagesToBottom(true);

                        fetch(route, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
                            },
                            body: JSON.stringify({
                                conversation_id: this.activeConversation,
                            })
                        }).then((res) => {
                            return res.json();
                        }).then((data) => {
                            if (data.history) {
                                this.messages.push(data.history);
                            }

                            localStorage.setItem('connectToAgentStore:' + this.activeConversationData?.id, 'on');

                            const conversationData = this.normalizeConversation(data.data);

                            this.connect_agent_at = conversationData.connect_agent_at;

                            this.applyConversationData(conversationData);

                            this.showConnectButtonsStepOne = false;
                            this.showConnectButtonsStepTwo = false;

                            this.connectingToAgent = false;

                            this.messages.forEach(message => {
                                message.showConnectButtons = false;
                            });

                            this.scrollMessagesToBottom(true);
                        }).catch((err) => {
                            this.connectingToAgent = false;
                        });
                    @endif
                },
                doNotConnectToAgent() {
                    localStorage.setItem('connectToAgentStore:' + this.activeConversationData?.id, 'off');

                    this.messages.forEach(message => {
                        message.showConnectButtons = false;
                    });

                    this.showConnectButtonsStepOne = true;
                    this.showConnectButtonsStepTwo = false;

                    this.showConnectButtons = false;
                },
                isOriginTrusted(origin) {
                    if (!this.trustedDomains || !this.trustedDomains.length) {
                        return true;
                    }
                    try {
                        const hostname = new URL(origin).hostname;
                        return this.trustedDomains.includes(hostname);
                    } catch {
                        return false;
                    }
                },
                notifyParentUnreadChange() {
                    if (!this.parentOrigin) return;

                    window.parent.postMessage({
                        type: 'lqd-ext-chatbot-unread-change',
                    }, this.parentOrigin);
                },
                setBookingIframe(state) {
                    this.showBookingIframe = state;

                    if (state) {
                        this.$nextTick(() => {
                            this.setBookingIframeHeight();
                        });
                    }
                },
                doNotShowBookingIframe() {
                    this.messages.forEach(message => {
                        message.showBookingButtons = false;
                    });

                    this.showBookingButtons = false;
                },
                setBookingIframeHeight() {
                    const that = this;
                    const parent = document.querySelector('.lqd-ext-chatbot-window-booking-iframe-container');
                    if (!parent) return;

                    const child = parent.querySelector('.lqd-ext-chatbot-window-booking-iframe-container > div');
                    if (!child) return;

                    const observer = new ResizeObserver(entries => {
                        for (const entry of entries) {
                            const height = entry.contentRect.height;
                            child.style.height = `${height}px`;
                        }
                    });

                    observer.observe(parent);

                    if (!this._bookingMessageHandler) {
                        this._bookingMessageHandler = function(e) {
                            // Handle Calendly events
                            if (e.origin === "https://calendly.com" && e.data.event) {
                                if (e.data.event === 'calendly.event_scheduled') {
                                    that.handleBookingComplete('Calendly', e.data.payload);
                                }
                            }

                            // Handle Cal.com events
                            if (e.origin === "https://app.cal.com" && e.data) {
                                if (e.data.type === 'bookingSuccessful') {
                                    that.handleBookingComplete('Cal.com', e.data.data);
                                }
                            }
                        };
                        window.addEventListener('message', this._bookingMessageHandler);
                    }
                },
                async handleBookingComplete(platform, data) {
                    this.doNotShowBookingIframe();
                    this.setBookingIframe(false);
                    const message = '{{ __('Thank you! Your meeting has been scheduled.') }}';
                    this.setWidgetStatus({
                        type: 'success',
                        message: data.message ?? message,
                    });
                    const newAssistantMessage = {
                        id: new Date().getTime(),
                        message: message,
                        role: 'assistant',
                        created_at: new Date().toISOString()
                    };

                    this.messages.push(newAssistantMessage);

                    const res = await fetch(`{{ isset($routes) ? $routes['conversations'] : '' }}/${this.activeConversation}/messages/append`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
                        },
                        body: JSON.stringify({
                            prompt: message,
                            role: 'assistant',
                        })
                    });
                },
                handleWindowMessages(event) {
                    if (!this.isOriginTrusted(event.origin)) return;

                    // Set parentOrigin from incoming messages as a fallback
                    // (document.referrer may be empty due to strict Referrer-Policy)
                    if (!this.parentOrigin) {
                        this.parentOrigin = event.origin;

                        @if (!$is_editor && isset($session))
                            window.parent.postMessage({
                                type: 'lqd-ext-chatbot-session-id',
                                sessionId: '{{ $session }}'
                            }, this.parentOrigin);
                        @endif
                    }

                    switch (event.data.type) {
                        case 'lqd-ext-chatbot-request-styling':
                            this.handleStylingResponse(event);
                            break;
                        case 'lqd-ext-chatbot-send-prompt':
                            this.handleSendPromptFromExternal(event);
                            break;
                        case 'lqd-ext-chatbot-window-state':
                            this.windowState = event.data.state;

                            if (event.data.state === 'open' && this.activeConversation) {
                                fetch(`{{ isset($routes) ? $routes['conversations'] : '' }}/${this.activeConversation}/messages?per_page=1`, {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                }).then(() => {
                                    const conv = this.conversations.find(c => c.id === this.activeConversation);
                                    if (conv) conv.unread_count = 0;
                                    this.notifyParentUnreadChange();
                                }).catch(() => {});
                            }
                            break;
                    }
                },
                handleSendPromptFromExternal(event) {
                    const prompt = event.data.prompt;

                    if (!prompt) return;

                    // Start new conversation if needed
                    if (!this.activeConversationData?.id) {
                        this.startNewConversation().then(() => {
                            console.log('created new')
                            this.sendPromptMessage(prompt);
                        });
                    } else {
                        this.sendPromptMessage(prompt);
                    }
                },
                sendPromptMessage(prompt) {
                    const messageInput = this.$refs.message;
                    if (!messageInput) {
                        return;
                    }

                    messageInput.value = prompt;
                    messageInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));

                    this.$nextTick(() => {
                        this.onSendMessage();
                    });
                },
                handleStylingResponse(event) {
                    const chatbotElStyles = getComputedStyle(this.$el);
                    const styles = {};
                    const attrs = {};
                    [
                        '--lqd-ext-chat-font-family',
                        '--lqd-ext-chat-offset-y',
                        '--lqd-ext-chat-offset-x',
                        '--lqd-ext-chat-trigger-w',
                        '--lqd-ext-chat-trigger-h',
                        '--lqd-ext-chat-window-w',
                        '--lqd-ext-chat-window-h',
                        '--lqd-ext-chat-window-y-offset',
                        '--lqd-ext-chat-primary',
                        '--lqd-ext-chat-primary-foreground',
                    ].forEach(attr => styles[attr] = chatbotElStyles.getPropertyValue(attr) || '');

                    ['data-pos-x', 'data-pos-y'].forEach(attr => attrs[attr] = this.$el.getAttribute(attr));

                    event.source.postMessage({
                            type: 'lqd-ext-chatbot-response-styling',
                            data: {
                                styles,
                                attrs
                            },
                        },
                        event.origin,
                    );
                },
                toggleWindowState(state) {
                    if (state === this.windowState) return;

                    this.windowState = state ? state : (this.windowState === 'open' ? 'close' : 'open');
                    this.$el.setAttribute('data-window-state', this.windowState);
                },
                toggleView(view) {
                    if (view === this.currentView) return;

                    if (view === '<') {
                        const lastView = this.prevViews.pop();

                        this.currentView = lastView || 'welcome';
                    } else {
                        this.prevViews = this.prevViews.filter(v => v !== view);

                        if (this.prevViews.at(-1) !== this.currentView) {
                            this.prevViews.push(this.currentView);
                        }

                        this.currentView = view;
                    }

                    if (this.currentView !== 'conversation-messages') {
                        this.activeConversation = null;
                        this.activeConversationData = null;
                    }

                    if (this.currentView === 'conversations-list' && !this.conversations.length) {
                        this.startNewConversation();
                    }
                },
                onMessageFieldHitEnter(event) {
                    if (!event.shiftKey) {
                        this.onSendMessage();
                    } else {
                        event.target.value += '\n';
                        event.target.scrollTop = event.target.scrollHeight
                    };
                },
                async getSession() {
                    this.fetching = true;

                    const res = await fetch('{{ isset($routes) ? $routes['getSession'] : '' }}');
                    const data = await res.json();

                    if (!res.ok) {
                        console.error(data);
                        return this.fetching = false;
                    }

                    const conversations = Array.isArray(data.conversations?.data) ?
                        data.conversations.data :
                        (data.conversations || []);

                    this.conversations = conversations.map(conversation => this.normalizeConversation(conversation));

                    this.activeChatbot = data.data;
                    this.updateSuggestedPrompts(this.activeChatbot?.suggested_prompts);

                    if (!Array.isArray(this.activeChatbot.review_responses)) {
                        this.activeChatbot.review_responses = this.activeChatbot.review_responses ?
                            Object.values(this.activeChatbot.review_responses) : [];
                    }
                    this.activeChatbot.is_review_enabled = Boolean(this.activeChatbot.is_review_enabled);

                    if (typeof Alpine !== 'undefined' && Alpine.store('voiceCall')) {
                        Alpine.store('voiceCall').enabled = !!data.data.voice_call_enabled;
                    }

                    if (this.conversations.length) {
                        @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-agent') && isset($chatbot) && isset($session))
                            await this.initAbly();
                        @endif
                    }

                    this.fetching = false;
                },
                async startNewConversation() {
                    this.showConnectButtonsStepOne = true;
                    this.showConnectButtonsStepTwo = false;

                    this.showConnectButtons = true;
                    this.connectingToAgent = false;

                    @if ($is_editor)
                        return this.toggleView('conversation-messages');
                    @else

                        this.fetching = true;

                        this.assistantMessageBubbles = {};

                        const res = await fetch(`{{ isset($routes) ? $routes['conversations'] : '' }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                prompt: this.activeChatbot.bubble_message
                            })
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            if (this.ablyChannel && this.ablyInstance) {
                                this.ablyChannel.unsubscribe();
                                this.ablyChannel = null;
                            }

                            @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-agent') && isset($chatbot) && isset($session))
                                await this.initAbly();
                            @endif

                            return this.fetching = false;
                        }

                        this.conversations.push(this.normalizeConversation(data.data));

                        await this.$nextTick();

                        this.openConversation(data.data.id);

                        this.toggleView('conversation-messages');

                        this.fetching = false;

                        @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-agent') && isset($chatbot) && isset($session))
                            if (this.ablyChannel && this.ablyInstance) {
                                this.ablyChannel.unsubscribe();
                                // this.ablyInstance.close();
                                this.ablyChannel = null;
                            }
                            await this.initAbly();
                        @endif
                    @endif
                },
                async openConversation(conversationId) {

                    @if ($is_editor)
                        return this.toggleView('conversation-messages');
                    @else
                        this.hideReviewWidget();

                        this.fetching = true;

                        this.assistantMessageBubbles = {};

                        this.activeConversationData = this.conversations.find(conversation => conversation.id === conversationId);

                        // this.connect_agent_at = this.activeConversationData?.connect_agent_at;

                        this.connect_agent_at = null;

                        this.activeConversation = this.activeConversationData?.id;

                        let connectToAgentStore = localStorage.getItem('connectToAgentStore:' + this.activeConversationData?.id) ?? null;

                        if (connectToAgentStore === 'on') {
                            this.showConnectButtonsStepOne = false;
                            this.showConnectButtonsStepTwo = false;

                            this.showConnectButtons = false;
                        } else {
                            this.showConnectButtonsStepOne = true;
                            this.showConnectButtonsStepTwo = false;

                            this.showConnectButtons = true;
                        }

                        if (!this.activeConversation) {
                            console.error('{{ __('Conversation not found') }}');
                            return this.fetching = false;
                        }

                        const res = await fetch(`{{ isset($routes) ? $routes['conversations'] : '' }}/${conversationId}/messages`, {
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
                            },
                        });
                        const data = await res.json();

                        if (!res.ok) {
                            console.error(data);
                            return this.fetching = false;
                        }

                        this.messages = data.data.reverse();

                        // Clear per-conversation unread badge (messages were marked as read server-side)
                        const conv = this.conversations.find(c => c.id === conversationId);
                        if (conv) conv.unread_count = 0;

                        // Notify parent to update badge immediately
                        this.notifyParentUnreadChange();

                        await this.$nextTick();

                        this.toggleView('conversation-messages');

                        this.scrollMessagesToBottom();

                        this.fetching = false;

                        await this.refreshActiveConversationData(this.activeConversation);
                        this.evaluateReviewTriggers();
                    @endif
                },
                clearPendingMedia() {
                    if (this.pendingMediaPreview) {
                        URL.revokeObjectURL(this.pendingMediaPreview);
                    }
                    this.pendingMedia = null;
                    this.pendingMediaPreview = null;
                    if (this.$refs.mediaInput) {
                        this.$refs.mediaInput.value = null;
                    }
                    if (this.$refs.sendBtn) {
                        this.$refs.sendBtn.classList.toggle('active', this.$refs.message?.value?.trim());
                    }
                },
                async onFileSelect(event) {
                    const input = event.target;
                    const files = input.files;

                    if (!files || !files.length) return;

                    const conversation = this.conversations.find(c => c.id === this.activeConversation);
                    const isLiveAgent = conversation?.connect_agent_at != null || this.activeConversationData?.connect_agent_at != null;

                    if (isLiveAgent) {
                        @if (!$is_editor)
                            // Live Agent mode: upload immediately
                            const formData = new FormData();
                            formData.append("media", files[0]);

                            this.uploading = true;

                            const response = await fetch(`{{ isset($routes) ? $routes['conversations'] : '' }}/${this.activeConversation}/file`, {
                                method: "POST",
                                headers: {
                                    'Accept': 'application/json',
                                },
                                body: formData,
                            });

                            this.uploading = false;

                            const data = await response.json();
                            const mediaData = data.data;

                            if (this.$refs.mediaInput) {
                                this.$refs.mediaInput.value = null;
                            }

                            if (!mediaData?.media_name && !mediaData?.media_url) {
                                this.setWidgetStatus({
                                    type: 'error',
                                    message: data.message ?? '{{ __('Something went wrong. Please try again.') }}'
                                });

                                return console.error('{{ __('Something went wrong. Please try again.') }}', data);
                            }

                            const newUserMessage = {
                                id: new Date().getTime(),
                                message: mediaData.message,
                                media_name: mediaData.media_name,
                                media_url: mediaData.media_url,
                                role: 'user',
                                created_at: new Date().toISOString()
                            };

                            this.messages.push(newUserMessage);
                            this.scrollMessagesToBottom();
                        @endif
                    } else {
                        // AI mode: only images allowed
                        const file = files[0];
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                        if (!allowedTypes.includes(file.type)) {
                            this.setWidgetStatus({
                                type: 'error',
                                message: '{{ __('Only JPG, PNG, and WebP images are supported.') }}'
                            });
                            if (this.$refs.mediaInput) {
                                this.$refs.mediaInput.value = null;
                            }
                            return;
                        }

                        this.pendingMedia = file;
                        this.pendingMediaPreview = URL.createObjectURL(file);

                        if (this.$refs.mediaInput) {
                            this.$refs.mediaInput.value = null;
                        }

                        if (this.$refs.sendBtn) {
                            this.$refs.sendBtn.classList.add('active');
                        }
                    }
                },
                async onSendMessage() {
                    const messageString = this.$refs.message.value.trim();
                    const mediaFiles = this.$refs.mediaInput?.files;
                    const hasPendingMedia = !!this.pendingMedia;

                    if (!messageString && !mediaFiles?.length && !hasPendingMedia) return;

                    const pendingFile = this.pendingMedia;
                    const pendingPreview = this.pendingMediaPreview;

                    this.$refs.message.value = '';
                    this.$refs.mediaInput && (this.$refs.mediaInput.value = null);
                    this.pendingMedia = null;
                    this.pendingMediaPreview = null;

                    this.$refs.sendBtn.classList.remove('active');

                    // reset show connect agent buttons on previous messages
                    this.messages.forEach(message => message.showConnectButtons = false);

                    // reset show booking assistant buttons on previous messages
                    this.messages.forEach(message => message.showBookingButtons = false);

                    @if ($is_editor)
                        const newUserMessage = {
                            id: new Date().getTime(),
                            message: messageString,
                            role: 'user',
                            created_at: new Date().toISOString()
                        };

                        this.messages.push(newUserMessage);

                        const loaderMessage = {
                            role: 'loader',
                            id: `response-for-${newUserMessage.id}`,
                            created_at: new Date().toISOString()
                        };

                        this.messages.push(loaderMessage);

                        this.scrollMessagesToBottom();

                        // echo the message in editor
                        const timeout = setTimeout(() => {
                            this.onReceiveMessage({
                                data: {
                                    id: new Date().getTime(),
                                    message: messageString,
                                    role: 'assistant',
                                    created_at: new Date().toISOString()
                                }
                            }, loaderMessage);

                            clearTimeout(timeout);
                        }, 300);
                    @else
                        if (!this.activeConversation) {
                            console.error('No active conversation');
                            return;
                        }

                        const conversation = this.conversations.find(conversation => conversation.id === this.activeConversation);

                        const newUserMessage = {
                            id: new Date().getTime(),
                            message: messageString,
                            role: 'user',
                            created_at: new Date().toISOString(),
                            media_url: pendingPreview || null,
                            media_name: pendingFile?.name || null,
                        };

                        this.messages.push(newUserMessage);

                        let loaderMessage = null;

                        if (conversation.connect_agent_at == null) {
                            loaderMessage = {
                                role: 'loader',
                                id: `response-for-${newUserMessage.id}`,
                                created_at: new Date().toISOString()
                            };

                            this.messages.push(loaderMessage);
                        }

                        this.scrollMessagesToBottom();

                        let fetchOptions;

                        if (pendingFile) {
                            const formData = new FormData();
                            if (messageString) {
                                formData.append('prompt', messageString);
                            }
                            formData.append('media', pendingFile);
                            fetchOptions = {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
                                },
                                body: formData,
                            };
                        } else {
                            fetchOptions = {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
                                },
                                body: JSON.stringify({
                                    prompt: messageString,
                                }),
                            };
                        }

                        const res = await fetch(`{{ isset($routes) ? $routes['conversations'] : '' }}/${this.activeConversation}/messages`,
                            fetchOptions);

                        if (!res.ok) {
                            if (loaderMessage == null) {
                                const loaderMessage = {
                                    role: 'loader',
                                    id: `response-for-${newUserMessage.id}`,
                                    created_at: new Date().toISOString()
                                };

                                this.messages.push(loaderMessage);
                            }

                            const errorData = await res.json();

                            this.messages = this.messages.filter(msg => msg.id !== loaderMessage.id);
                            this.messages.push({
                                id: new Date().getTime(),
                                message: errorData.message ||
                                    '{{ __('Sorry, I could not process your request at the moment. Please try again later.') }}',
                                role: 'assistant',
                                created_at: new Date().toISOString()
                            });

                            this.scrollMessagesToBottom();

                            return;
                        }

                        const data = await res.json();

                        conversation.last_message = newUserMessage.message;
                        if (conversation.updated_at) {
                            conversation.updated_at = new Date().toISOString();
                        } else if (conversation.created_at) {
                            conversation.created_at = new Date().toISOString();
                        }

                        if (loaderMessage != null) {
                            this.onReceiveMessage(data, loaderMessage);
                        }

                        @if (isset($chatbot))
                            let condition = '{{ $chatbot->interaction_type->value }}';

                            if (
                                conversation.connect_agent_at == null &&
                                condition === '{{ \App\Extensions\Chatbot\System\Enums\InteractionType::SMART_SWITCH }}'
                            ) {
                                this.showConnectButtons = true;
                                document.addEventListener('typing-done', this.onTypingDone)
                            }

                            let bookingCondition = '{{ $chatbot->getAttribute('is_booking_assistant') }}';
                            if (bookingCondition == true) {
                                this.showBookingButtons = true;
                                document.addEventListener('typing-done', this.onTypingDone)
                            }
                        @endif
                    @endif
                },
                onTypingDone(event) {
                    const {
                        messageObj
                    } = event.detail;

                    let existingConnectAgentMessage = null;
                    let existingCollectEmailMessage = null;
                    let userMessages = [];

                    this.messages.forEach(message => {
                        if (message.showConnectButtons) {
                            return existingConnectAgentMessage = message;
                        }

                        if (message.role === 'collect-email') {
                            return existingCollectEmailMessage = message;
                        }

                        if (message.role === 'user') {
                            return userMessages.push(message)
                        }
                    });

                    // Show collect email message first
                    if (
                        !existingConnectAgentMessage &&
                        !existingCollectEmailMessage &&
                        userMessages.length &&
                        this.activeChatbot.showCollectEmail
                    ) {
                        this.messages.push({
                            id: 'collect-email',
                            role: 'collect-email',
                            created_at: Date.now()
                        });

                        this.activeChatbot.showCollectEmail = false;

                        this.$nextTick(() => {
                            this.$refs.routesView.scrollTo({
                                top: this.$refs.routesView.scrollTop + 44,
                                behavior: 'smooth'
                            });
                        });
                    }

                    // Show connect agent message
                    if (
                        userMessages.length &&
                        messageObj.showConnectButtonsWhenTypingDone &&
                        localStorage.getItem('connectToAgentStore:' + this.activeConversationData?.id) !== 'off'
                    ) {
                        messageObj.showConnectButtons = true;

                        this.$nextTick(() => {
                            this.$refs.routesView.scrollTo({
                                top: this.$refs.routesView.scrollTop + 44,
                                behavior: 'smooth'
                            });
                        });
                    }

                    // Connect agent immediately if needed
                    if (
                        userMessages.length &&
                        messageObj.connectToHumanAgentDirectlyWhenTypingDone
                    ) {
                        this.connectToAgent();
                    }

                    // Show booking buttons
                    if (
                        userMessages.length &&
                        messageObj.showBookingButtonsWhenTypingDone
                    ) {
                        messageObj.showBookingButtons = true;

                        this.$nextTick(() => {
                            this.$refs.routesView.scrollTo({
                                top: this.$refs.routesView.scrollTop + 44,
                                behavior: 'smooth'
                            });
                        });
                    }

                    document.removeEventListener('typing-done', this.onTypingDone);
                },
                async onReceiveMessage(data, loaderMessage) {
                    const message = data.data;
                    const messageToReplace = this.messages.find(msg => msg.id === loaderMessage.id);

                    messageToReplace.role = 'assistant';
                    messageToReplace.message = message.message;
                    messageToReplace.created_at = new Date().toISOString();
                    messageToReplace.isNew = true;
                    messageToReplace.showConnectButtonsWhenTypingDone = data.needs_human;
                    messageToReplace.connectToHumanAgentDirectlyWhenTypingDone = data.needs_human_direct;
                    messageToReplace.showBookingButtonsWhenTypingDone = data.needs_booking;
                    messageToReplace.isHTML = data.isHTML;

                    if (data.collect_email) {
                        this.activeChatbot.showCollectEmail = true;
                    }

                    this.playBubbleSound();

                    this.scrollMessagesToBottom();
                },
                scrollMessagesToBottom(smooth = false) {
                    this.$nextTick(() => {
                        // adding setTimeout to make sure all extra dom work is done. such as adding suggestions etc.
                        setTimeout(() => {
                            this.$refs.routesView.scrollTo({
                                top: this.$refs.routesView.scrollHeight,
                                behavior: smooth ? 'smooth' : 'auto'
                            });
                        }, 50)
                    })
                },
                normalizeSuggestedPrompts(prompts) {
                    if (!Array.isArray(prompts)) {
                        return [];
                    }

                    return prompts
                        .map((prompt) => {
                            const name = typeof prompt?.name === 'string' ? prompt.name.trim() : (prompt?.name ?? '').toString().trim();
                            const promptText = typeof prompt?.prompt === 'string' ? prompt.prompt.trim() : (prompt?.prompt ?? '').toString().trim();

                            return {
                                name,
                                prompt: promptText,
                            };
                        })
                        .filter(prompt => prompt.name.length || prompt.prompt.length);
                },
                updateSuggestedPrompts(prompts) {
                    const normalized = this.normalizeSuggestedPrompts(prompts);
                    const explicitEnabled = typeof this.activeChatbot?.suggested_prompts_enabled === 'boolean' ?
                        this.activeChatbot.suggested_prompts_enabled :
                        null;
                    const hasContent = normalized.length > 0;
                    const isEnabled = explicitEnabled === null ? hasContent : explicitEnabled;

                    if (!isEnabled) {
                        this.suggestedPrompts = [];
                        return;
                    }

                    if (!hasContent) {
                        this.suggestedPrompts = this.getDemoSuggestedPrompts();
                        return;
                    }

                    this.suggestedPrompts = normalized;
                },
                getDemoSuggestedPrompts() {
                    return [{
                            name: '{{ __('Pricing & Plans') }}',
                            prompt: '{{ __('What pricing options do you offer, and which plan fits small teams best?') }}',
                        },
                        {
                            name: '{{ __('Integration Help') }}',
                            prompt: '{{ __('How can I connect the chatbot to my website or CRM?') }}',
                        },
                        {
                            name: '{{ __('Support Hours') }}',
                            prompt: '{{ __('When is your support team available if I need further assistance?') }}',
                        },
                    ];
                },
                getSuggestedPromptLabel(prompt, index) {
                    const name = (prompt?.name ?? '').trim();

                    if (name.length) {
                        return name;
                    }

                    const promptText = (prompt?.prompt ?? '').trim();

                    if (promptText.length) {
                        const maxLength = 48;

                        if (promptText.length > maxLength) {
                            return `${promptText.slice(0, maxLength).trimEnd()}...`;
                        }

                        return promptText;
                    }

                    return `${this.suggestedPromptFallbackLabel} ${index + 1}`;
                },
                applySuggestedPrompt(prompt) {
                    const resolvedPrompt = typeof prompt === 'string' ?
                        prompt :
                        ((prompt?.prompt ?? '').trim() || (prompt?.name ?? '').trim());

                    if (!resolvedPrompt.length || !this.$refs.message) {
                        return;
                    }

                    this.showEmojiPicker = false;

                    this.$refs.message.value = resolvedPrompt;
                    this.$refs.message.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));

                    this.$nextTick(() => {
                        this.onSendMessage();
                    });
                },
                addMessage(message, el, options = {}) {
                    if (options.isHTML) {
                        return message;
                    }
                    const formattedMessage = this.getFormattedString(message);
                    const index = el.getAttribute('data-index');
                    const useTypeEffect = this.messages.find((msg, i) => i == index && msg.role === 'assistant' && msg.isNew);

                    this.assistantMessageBubbles[index] = el;

                    if (useTypeEffect) {
                        return this.typeMessage(this.messages[index], index);
                    }

                    return formattedMessage;
                },
                typeMessage(messageObj, bubbleMessageIndex) {
                    const messageEl = this.assistantMessageBubbles[bubbleMessageIndex];
                    const messageString = messageObj.message;

                    if (!messageEl || !messageString) return '';

                    const formattedMessage = this.getFormattedString(messageString);

                    let i = 0;
                    const speed = 25; // typing speed in milliseconds
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = formattedMessage;
                    const textContent = tempDiv.innerHTML || '';
                    messageObj.isNew = false;

                    const splitUnit = this.isJP(textContent) ? '' : ' ';
                    const units = textContent.split(splitUnit);

                    if (units.length === 1) {
                        messageEl.innerHTML = tempDiv.innerHTML;

                        messageObj.isTyping = false;

                        this.$dispatch('typing-done', {
                            messageObj,
                            bubbleMessageIndex
                        });

                        return tempDiv.innerHTML;
                    }

                    const typeWriter = () => {
                        if (i < units.length) {
                            messageEl.innerHTML = units.slice(0, i + 1).join(splitUnit) + (splitUnit);
                            i++;
                            setTimeout(typeWriter, speed);
                            this.scrollMessagesToBottom();
                        } else {
                            messageObj.isTyping = false;

                            this.$dispatch('typing-done', {
                                messageObj,
                                bubbleMessageIndex
                            });
                        }
                    };

                    messageObj.isTyping = true;

                    typeWriter();
                },
                getFormattedString(string) {
                    if (!('markdownit' in window) || !string) return '';

                    string
                        .replace(/>(\s*\r?\n\s*)</g, '><')
                        .replace(/\n(?!.*\n)/, '');

                    const renderer = window.markdownit({
                        html: true,
                        breaks: true,
                        highlight: (str, lang) => {
                            const language = lang && lang !== '' ? lang : 'md';
                            const codeString = str;

                            if (Prism.languages[language]) {
                                const highlighted = Prism.highlight(codeString, Prism.languages[language], language);
                                return `<pre class="language-${language}"><code data-lang="${language}" class="language-${language}">${highlighted}</code></pre>`;
                            }

                            return codeString;
                        }
                    });

                    renderer.use(function(md) {
                        md.core.ruler.after('inline', 'convert_elements', function(
                            state) {
                            state.tokens.forEach(function(blockToken) {
                                if (blockToken.type !== 'inline') return;

                                let fullContent = '';

                                blockToken.children.forEach(token => {
                                    let {
                                        content,
                                        type
                                    } = token;

                                    switch (type) {
                                        case 'link_open':
                                            token.attrs?.push(['target', '_blank']);
                                            content =
                                                `<a ${token.attrs.map(([key, value]) => `${key}="${value}"`).join(' ')}>`;
                                            break;
                                        case 'link_close':
                                            content = '</a>';
                                            break;
                                    }

                                    fullContent += content;
                                });

                                if (fullContent.includes('<ol>') ||
                                    fullContent.includes('<ul>')) {
                                    const listToken = new state.Token('html_inline', '', 0);
                                    listToken.content = fullContent.trim();
                                    listToken.markup = 'html';
                                    listToken.type = 'html_inline';

                                    blockToken.children = [listToken];
                                }
                            });
                        });

                        md.core.ruler.after('inline', 'convert_links', function(state) {
                            state.tokens.forEach(function(blockToken) {
                                if (blockToken.type !== 'inline') return;
                                blockToken.children.forEach(function(token, idx) {
                                    const {
                                        content
                                    } = token;
                                    if (content.includes('<a ')) {
                                        const linkRegex = /(.*)(<a\s+[^>]*\s+href="([^"]+)"[^>]*>([^<]*)<\/a>?)(.*)/;
                                        const linkMatch = content.match(linkRegex);

                                        if (linkMatch) {
                                            const [, before, , href, text, after] = linkMatch;

                                            const beforeToken = new state.Token('text', '', 0);
                                            beforeToken.content = before;

                                            const newToken = new state.Token('link_open', 'a', 1);
                                            newToken.attrs = [
                                                ['href', href],
                                                ['target', '_blank']
                                            ];
                                            const textToken = new state.Token('text', '', 0);
                                            textToken.content = text;
                                            const closingToken = new state.Token('link_close', 'a', -1);

                                            const afterToken = new state.Token('text', '', 0);
                                            afterToken.content = after;

                                            blockToken.children
                                                .splice(idx, 1, beforeToken, newToken, textToken, closingToken, afterToken);
                                        }
                                    }
                                });
                            });
                        });
                    });

                    return renderer.render(renderer.utils.unescapeAll(string));
                },
                isJP(string) {
                    return /[\u3040-\u30ff\u3400-\u4dbf\u4e00-\u9fff\uF900-\uFAFF]/.test(string);
                },
                getTimeLabel(time) {
                    const diff = Math.floor((new Date() - new Date(time ?? Date.now())) / 1000);

                    return (
                        diff < 60 ? '{{ __('just now') }}' :
                        diff < 3600 ? (Math.floor(diff / 60) === 1 ? '1 {{ __('minute ago') }}' : Math.floor(diff / 60) +
                            ' {{ __('minutes ago') }}') :
                        diff < 86400 ? (Math.floor(diff / 3600) === 1 ? '1 {{ __('hour ago') }}' : Math.floor(diff / 3600) +
                            ' {{ __('hours ago') }}') :
                        Math.floor(diff / 86400) === 1 ? '1 {{ __('day ago') }}' : Math.floor(diff / 86400) + ' {{ __('days ago') }}'
                    )
                },
                getViewLabel() {
                    let viewName;

                    switch (this.currentView) {
                        case 'conversations-list':
                            viewName = 'Recent Messages'
                            break;
                        case 'contact-form':
                            viewName = 'Send email'
                            break;
                        case 'thanks':
                            viewName = 'Thanks!'
                            break;
                        case 'articles-list':
                        case 'article-show':
                            viewName = 'Help Center'
                            break;
                    }

                    return viewName;
                },
                async onArticlesSearch(searchString) {
                    const string = searchString.trim();

                    if (!this.originalArticles) {
                        this.originalArticles = this.articles;
                    }

                    this.searchingArticles = true;

                    if (!string) {
                        this.searchingArticles = false;
                        this.articles = this.originalArticles;

                        return;
                    };

                    @if ($is_editor)
                        const filteredArticles = this.originalArticles.filter(article =>
                            article.title.toLowerCase().includes(string.toLowerCase()) ||
                            article.excerpt.toLowerCase().includes(string.toLowerCase())
                        );

                        this.articles = filteredArticles;

                        this.searchingArticles = false;
                    @else
                        const res = await fetch(`{{ isset($routes) ? $routes['articles'] : '' }}?search=${string}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();

                        this.articles = data;

                        this.searchingArticles = false;
                    @endif
                },
                async showArticle(articleId) {
                    if (articleId == null) return;

                    this.fetching = true;

                    const res = await fetch(`{{ isset($routes) ? $routes['articles'] : '' }}/${articleId}/show`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    this.fetching = false;

                    const article = data[0];

                    if (!article?.id) {
                        this.setWidgetStatus({
                            type: 'error',
                            message: '{{ __('Could not find the article') }}'
                        });

                        this.toggleView('welcome');

                        return;
                    }

                    this.showingArticle = article;

                    this.$nextTick(() => this.toggleView('article-show'))
                },
                onContactFormSubmit(event) {
                    const form = event.target;
                    const formData = new FormData(form);

                    fetch(`{{ isset($routes) ? $routes['send-email'] : '' }}`, {
                        method: 'POST',
                        body: formData
                    });

                    this.toggleView('thanks');
                },
                setWidgetStatus(opts = {}) {
                    this.widgetStatusTimeout && clearTimeout(this.widgetStatusTimeout);

                    const options = {
                        type: 'success',
                        message: '',
                        timeout: 4000,
                        ...opts,
                    };

                    this.widgetStatus = {
                        type: options.type,
                        message: options.message,
                    };

                    this.widgetStatusTimeout = setTimeout(() => {
                        this.widgetStatus = {
                            type: null,
                            message: null
                        };
                        this.widgetStatusTimeout && clearTimeout(this.widgetStatusTimeout);
                    }, options.timeout);
                },
                async collectEmail(event) {
                    const form = event.target;
                    const formData = new FormData(form);

                    if (this.activeChatbot.showCollectEmail) return;

                    const response = await fetch(`{{ isset($routes) ? $routes['collect-email'] : '' }}`, {
                        method: "POST",
                        headers: {
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    this.activeChatbot.showCollectEmail = false;

                    console.log(this.activeChatbot);

                    const data = await response.json();

                    if (!data.email) {
                        return this.setWidgetStatus({
                            type: 'error',
                            message: '{{ __('Something went wrong. Please try again.') }}'
                        });
                    }

                    this.setWidgetStatus({
                        type: 'success',
                        message: data.message ?? '{{ __('Email collected successfully.') }}'
                    });

                    this.messages = this.messages.filter(message => message.role !== 'collect-email');
                },

                normalizeConversation(conversation = {}) {
                    if (!conversation) return conversation;
                    return {
                        ...conversation,
                        review_requested_at: conversation.review_requested_at ?? null,
                        review_request_reason: conversation.review_request_reason ?? null,
                        review_submitted_at: conversation.review_submitted_at ?? null,
                        review_message: conversation.review_message ?? null,
                        review_selected_response: conversation.review_selected_response ?? null,
                        ticket_status: conversation.ticket_status ?? null,
                        last_activity_at: conversation.last_activity_at ?? null,
                    };
                },
                updateConversationInList(conversation) {
                    if (!conversation?.id) return;

                    const index = this.conversations.findIndex(item => item.id === conversation.id);

                    if (index !== -1) {
                        this.conversations[index] = {
                            ...this.conversations[index],
                            ...conversation
                        };
                    }
                },
                applyConversationData(conversation) {
                    if (!conversation) return;

                    const normalized = this.normalizeConversation(conversation);

                    if (this.activeConversationData?.id === normalized.id || this.activeConversation === normalized.id) {
                        this.activeConversationData = {
                            ...(this.activeConversationData || {}),
                            ...normalized
                        };
                    }

                    const dismissedKey = this.reviewDismissed[normalized.id];

                    if (
                        normalized.review_requested_at &&
                        dismissedKey &&
                        dismissedKey !== normalized.review_requested_at
                    ) {
                        delete this.reviewDismissed[normalized.id];
                    }

                    this.updateConversationInList(normalized);
                },
                getConversationRoute(conversationId) {
                    if (!conversationId || !this.conversationRouteTemplate) {
                        return null;
                    }

                    return this.conversationRouteTemplate.replace('__conversation__', conversationId);
                },
                getReviewRoute(conversationId) {
                    if (!conversationId || !this.reviewRouteTemplate) {
                        return null;
                    }

                    return this.reviewRouteTemplate.replace('__conversation__', conversationId);
                },
                async refreshActiveConversationData(conversationId) {
                    if (!conversationId) return;

                    const route = this.getConversationRoute(conversationId);

                    if (!route) return;

                    try {
                        const res = await fetch(route, {
                            headers: {
                                'Accept': 'application/json',
                                'X-timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
                            }
                        });
                        const data = await res.json();

                        if (res.ok && data?.data) {
                            this.applyConversationData(data.data);
                        }
                    } catch (error) {
                        console.error(error);
                    }
                },
                getReviewPrompt() {
                    if (this.activeChatbot?.review_prompt) {
                        return this.activeChatbot.review_prompt;
                    }

                    return '{{ __('We’d love your feedback—how did we do?') }}';
                },
                showReviewWidget(reason = null) {
                    if (
                        !this.activeChatbot?.is_review_enabled ||
                        !Array.isArray(this.activeChatbot.review_responses) ||
                        this.activeChatbot.review_responses.length === 0
                    ) {
                        return;
                    }

                    this.reviewWidget.visible = true;
                    this.reviewWidget.reason = reason;
                    this.reviewWidget.submitting = false;
                    this.reviewWidget.error = null;

                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.scrollMessagesToBottom(true);
                        }, 100);
                    });
                },
                hideReviewWidget() {
                    this.reviewWidget.visible = false;
                    this.reviewWidget.submitting = false;
                    this.reviewWidget.error = null;
                },
                dismissReviewWidget() {
                    if (this.activeConversationData?.id) {
                        this.reviewDismissed[this.activeConversationData.id] =
                            this.activeConversationData.review_requested_at || 'manual';
                    }

                    this.hideReviewWidget();
                },
                shouldTriggerInactivityReview() {
                    if (
                        !this.activeConversationData?.last_activity_at ||
                        this.activeConversationData.review_requested_at ||
                        this.activeConversationData.review_submitted_at
                    ) {
                        return false;
                    }

                    const lastActivity = new Date(this.activeConversationData.last_activity_at);

                    if (Number.isNaN(lastActivity.getTime())) {
                        return false;
                    }

                    return (Date.now() - lastActivity.getTime()) >= this.reviewInactivityDelay;
                },
                evaluateReviewTriggers() {
                    if (!this.activeChatbot?.is_review_enabled || !this.activeConversationData?.id) {
                        this.hideReviewWidget();
                        return;
                    }

                    const dismissedKey = this.reviewDismissed[this.activeConversationData.id];

                    if (
                        dismissedKey &&
                        dismissedKey === (this.activeConversationData.review_requested_at || 'manual')
                    ) {
                        return;
                    }

                    if (this.activeConversationData.review_submitted_at) {
                        this.hideReviewWidget();
                        return;
                    }

                    if (this.activeConversationData.review_requested_at) {
                        this.showReviewWidget(this.activeConversationData.review_request_reason);
                        return;
                    }

                    if (
                        this.activeConversationData.ticket_status === 'closed' &&
                        !this.activeConversationData.review_requested_at
                    ) {
                        this.requestReview('ticket_closed');
                        return;
                    }

                    if (this.shouldTriggerInactivityReview()) {
                        this.requestReview('inactive');
                    }
                },
                async requestReview(reason) {
                    if (!this.activeConversation || this.reviewRequesting) return;

                    const route = this.getReviewRoute(this.activeConversation);

                    if (!route) return;

                    this.reviewRequesting = true;

                    try {
                        const res = await fetch(route, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
                            },
                            body: JSON.stringify({
                                action: 'request',
                                reason: reason || 'manual'
                            })
                        });
                        const data = await res.json();

                        if (!res.ok) {
                            return;
                        }

                        if (data?.data) {
                            this.applyConversationData(data.data);
                        }

                        this.showReviewWidget(reason);
                    } catch (error) {
                        console.error(error);
                    } finally {
                        this.reviewRequesting = false;
                    }
                },
                async sendReviewResponse(response) {
                    if (!this.activeConversation || !response) return;

                    const trimmed = response.trim();

                    if (!trimmed.length) {
                        this.reviewWidget.error = '{{ __('Please choose a valid response.') }}';
                        return;
                    }

                    const route = this.getReviewRoute(this.activeConversation);

                    if (!route) return;

                    this.reviewWidget.submitting = true;
                    this.reviewWidget.error = null;

                    try {
                        const res = await fetch(route, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
                            },
                            body: JSON.stringify({
                                action: 'submit',
                                message: trimmed,
                                selected_response: trimmed,
                            })
                        });
                        const data = await res.json();

                        if (!res.ok) {
                            this.reviewWidget.error = data?.message || '{{ __('Unable to send your review. Please try again later.') }}';
                            return;
                        }

                        if (data?.data) {
                            this.applyConversationData(data.data);
                        }

                        this.setWidgetStatus({
                            type: 'success',
                            message: '{{ __('Thanks for your feedback!') }}'
                        });

                        this.hideReviewWidget();
                    } catch (error) {
                        console.error(error);
                        this.reviewWidget.error = '{{ __('Unable to send your review. Please try again later.') }}';
                    } finally {
                        this.reviewWidget.submitting = false;
                    }
                },
                async toggleSound(event) {
                    const checkboxEl = event.currentTarget;
                    const soundEnabled = checkboxEl.checked;

                    @if ($is_editor)
                        this.soundEnabled = soundEnabled;
                    @elseif (isset($chatbot))
                        const formData = new FormData();

                        formData.append('enabled_sound', soundEnabled);

                        const res = await fetch('{{ $routes['enable-sound'] }}', {
                            method: "POST",
                            headers: {
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const data = await res.json();

                        this.soundEnabled = soundEnabled;
                    @endif
                },

                setEmptyCart() {
                    this.cart = {
                        product_data: {},
                        products: [],
                    };
                },
                async productSelectVariant(variantID) {
                    const element = this.$event.currentTarget;
                    const parent = element.parentElement;

                    parent.querySelectorAll('.selected').forEach(child => child.classList.remove('selected'));
                    element.classList.add('selected');

                    return false;
                },
                async productAddToCart(productID) {
                    if (this.updatingCart) return;

                    const element = this.$event.currentTarget;
                    const parent = element.parentElement;
                    const productWrapper = element.closest('.lqd-ext-chatbot-product');
                    const selectedOptions = [];

                    if (productWrapper) {
                        const allVariantButtons = productWrapper.querySelectorAll('.lqd-ext-chatbot-product-variant-button');
                        allVariantButtons.forEach(button => {
                            if (button.classList.contains('selected')) {
                                const dataName = button.getAttribute('data-name');
                                if (dataName) {
                                    selectedOptions.push(dataName);
                                }
                            }
                        });
                    }

                    this.updatingCart = true;

                    const response = await fetch(`{{ isset($routes) ? $routes['product-addToCart'] : '' }}`, {
                        method: "POST",
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            selectedOptions: selectedOptions,
                            productID: productID,
                        }),
                    });

                    const data = await response.json();

                    this.updatingCart = false;

                    if (data.error) {
                        this.setWidgetStatus({
                            type: 'error',
                            message: data.message ?? '{{ __('Something went wrong. Please try again.') }}'
                        });

                        return console.error('{{ __('Something went wrong. Please try again.') }}', data);
                    }

                    if (data.cart) {
                        this.cart = this.buildCart(data.cart);
                    }

                    return false;
                },

                async productUpdateQuantity(type, productID) {
                    if (type === 'removeAll' && !confirm('{{ __('Remove all instances of the product?') }}')) {
                        return;
                    }

                    this.updatingCart = true;

                    const response = await fetch(`{{ isset($routes) ? $routes['product-updateQuantity'] : '' }}`, {
                        method: "POST",
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            productID: productID,
                            type: type, // 'add', 'remove', 'removeAll'
                        }),
                    });

                    const data = await response.json();

                    this.updatingCart = false;

                    if (data.error) {
                        this.setWidgetStatus({
                            type: 'error',
                            message: data.message ?? '{{ __('Something went wrong. Please try again.') }}'
                        });
                        return console.error('{{ __('Something went wrong. Please try again.') }}', data);
                    }

                    if (data.cart) {
                        this.cart = this.buildCart(data.cart);
                    }

                    return false;
                },
                async productCartCheckout() {
                    this.updatingCart = true;

                    const response = await fetch(`{{ isset($routes) ? $routes['product-cartCheckout'] : '' }}`, {
                        method: "POST",
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({}),
                    });

                    const data = await response.json();

                    this.updatingCart = false;

                    if (data.error) {
                        this.setWidgetStatus({
                            type: 'error',
                            message: data.message ?? '{{ __('Something went wrong. Please try again.') }}'
                        });
                    }

                    if (data.checkoutUrl) {
                        this.setWidgetStatus({
                            type: 'success',
                            message: data.message ?? '{{ __('Redirecting to checkout...') }}'
                        });

                        this.setEmptyCart();

                        window.open(data.checkoutUrl, '_blank');
                    }

                    return false;
                },
                async productGetCart(isInitialCheck = false) {
                    this.updatingCart = true;

                    const response = await fetch(`{{ isset($routes) ? $routes['product-getCart'] : '' }}`, {
                        method: "POST",
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        }
                    });
                    const data = await response.json();

                    this.updatingCart = false;

                    if (data.status === 'not_found' && isInitialCheck) {
                        return this.setEmptyCart();
                    }

                    if (!data.cart) {
                        return this.setWidgetStatus({
                            type: 'error',
                            message: data.message ?? '{{ __('Something went wrong. Please try again.') }}'
                        });
                    }

                    this.cart = this.buildCart(data.cart);

                    return false;
                },
                buildCart(cart) {
                    const updatedCart = cart;

                    Object.keys(updatedCart.product_data ?? {}).forEach(productId => {
                        updatedCart.product_data[productId].qty = updatedCart.products.filter(pId => pId === productId).length;
                    });

                    return updatedCart;
                },

                async exportConversation() {

                }
            }));
        });
    })();
</script>
