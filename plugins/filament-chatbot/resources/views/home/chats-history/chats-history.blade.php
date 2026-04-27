@php
    $status_filters = [
        'all' => [
            'label' => __('All'),
        ],
        'new' => [
            'label' => __('New'),
        ],
        'closed' => [
            'label' => __('Closed'),
        ],
    ];

    $sort_filters = [
        'newest' => [
            'label' => __('Newest'),
        ],
        'oldest' => [
            'label' => __('Oldest'),
        ],
    ];

    $agent_filters = [
        'all' => [
            'label' => __('All'),
        ],
        'ai' => [
            'label' => __('AI Agent'),
        ],
        'human' => [
            'label' => __('Human Agent'),
        ],
    ];

    $channel_filters = [
        'all' => [
            'label' => __('All Channel'),
        ],
        'frame' => [
            'label' => __('Live Chat'),
        ],
    ];

    if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-telegram')) {
        $channel_filters['telegram'] = [
            'label' => __('Telegram'),
        ];
    }

    if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-whatsapp')) {
        $channel_filters['whatsapp'] = [
            'label' => __('Whatsapp'),
        ];
    }

    $conversation_agent_filters = [
        'all' => [
            'label' => __('All Agents'),
        ],
    ];

    foreach (auth()->user()->externalChatbots as $chatbotAgent) {
        $conversation_agent_filters[$chatbotAgent->id] = [
            'label' => $chatbotAgent->title,
        ];
    }

    $hasCustomerTagExtension = \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-customer-tag');
    $customerTagRoutes = [
        'index' => $hasCustomerTagExtension && \Route::has('dashboard.chatbot-agent.customer-tags.index') ? route('dashboard.chatbot-agent.customer-tags.index') : null,
        'store' => $hasCustomerTagExtension && \Route::has('dashboard.chatbot-agent.customer-tags.store') ? route('dashboard.chatbot-agent.customer-tags.store') : null,
        'sync' => $hasCustomerTagExtension && \Route::has('dashboard.chatbot-agent.customer-tags.sync') ? route('dashboard.chatbot-agent.customer-tags.sync') : null,
    ];
    $chatbotAgentRoutes = [
        'pin' => \Route::has('dashboard.chatbot-agent.conversations.pinned') ? route('dashboard.chatbot-agent.conversations.pinned') : null,
        'close' => \Route::has('dashboard.chatbot-agent.conversations.closed') ? route('dashboard.chatbot-agent.conversations.closed') : null,
        'delete' => \Route::has('dashboard.chatbot-agent.destroy') ? route('dashboard.chatbot-agent.destroy') : null,
    ];
@endphp
<div
    class="lqd-ext-chatbot-history invisible fixed end-0 start-0 top-0 z-40 flex h-screen bg-background opacity-0 transition-all max-sm:block lg:start-[--navbar-width] lg:group-[&.focus-mode]/body:start-0 [&.lqd-open]:visible [&.lqd-open]:opacity-100"
    x-data="externalChatbotHistory"
    :class="{ 'lqd-open': open }"
    @keydown.window.escape="setOpen(false)"
>
    <div
        class="lqd-ext-chatbot-history-sidebar group/history-sidebar relative flex shrink-0 flex-col bg-foreground/[3%] lg:w-[clamp(250px,27%,400px)]"
        :class="{ 'mobile-dropdown-open': mobileDropdownOpen }"
    >
        @includeIf('chatbot::home.chats-history.particles.conversations-filter')

        <div
            class="transition-all max-lg:fixed max-lg:bottom-0 max-lg:top-[calc(var(--header-height)+3.5rem)] max-lg:z-10 max-lg:flex max-lg:h-0 max-lg:w-full max-lg:flex-col max-lg:overflow-hidden max-lg:bg-background/90 max-lg:backdrop-blur-lg lg:contents max-lg:[&.active]:h-full"
            :class="{ 'active': mobile.filtersVisible }"
        >
			@if (
							class_exists(\App\Extensions\Chatbot\System\Helpers\ChatbotHelper::class) &&
							method_exists(\App\Extensions\Chatbot\System\Helpers\ChatbotHelper::class, 'planAllowsHumanAgent') &&
							\App\Extensions\Chatbot\System\Helpers\ChatbotHelper::planAllowsHumanAgent()
						)
                @includeIf('chatbot-agent::particles.conversations-sort')
            @endif

            @includeIf('chatbot::home.chats-history.particles.conversations-channel-filter')

            @include('chatbot::home.chats-history.particles.conversations-list')
        </div>
    </div>

    <div
        class="lqd-ext-chatbot-history-content-wrap flex h-full grow flex-col overflow-y-auto lg:w-1/2"
        x-ref="historyContentWrap"
    >
        @include('chatbot::home.chats-history.particles.messages-header')

        <div
            class="lqd-ext-chatbot-history-messages relative mt-auto flex flex-col gap-2 py-10"
            x-ref="historyMessages"
        >
            <div class="space-y-2 px-4 max-lg:pb-[calc(var(--bottom-menu-height)+2rem)] xl:px-10">
                @include('chatbot::home.chats-history.particles.messages-list')
            </div>
        </div>
    </div>

    <div
        class="lqd-ext-chatbot-contact-info flex flex-col border-s transition-all max-lg:fixed max-lg:bottom-0 max-lg:top-[calc(var(--header-height)+3.5rem)] max-lg:z-10 max-lg:h-0 max-lg:max-h-[calc(100%-var(--header-height)-3.5rem)] max-lg:w-full max-lg:overflow-hidden max-lg:bg-background/90 max-lg:backdrop-blur-lg lg:w-[clamp(250px,27%,400px)] max-lg:[&.active]:h-full"
        :class="{ 'active': mobile.contactInfoVisible }"
    >
        @include('chatbot::home.chats-history.particles.contact-info-head')

        <div class="grid grow grid-cols-1 place-items-start overflow-y-auto">
            @include('chatbot::home.chats-history.particles.contact-info-tab-details')
            {{-- @include('chatbot::home.chats-history.particles.contact-info-tab-history') --}}
        </div>
    </div>
</div>

@push('css')
    <link
        rel="stylesheet"
        href="{{ custom_theme_url('/assets/libs/datepicker/air-datepicker.css') }}"
    />
    <link
        rel="stylesheet"
        href="{{ custom_theme_url('assets/libs/jscolorpicker/dist/colorpicker.css') }}"
    >
@endpush

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/fslightbox/fslightbox.js') }}"></script>
    <script src="{{ custom_theme_url('assets/libs/jscolorpicker/dist/colorpicker.iife.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/datepicker/air-datepicker.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/datepicker/locale/en.js') }}"></script>
    <script>
        const customerTagRoutes = @json($customerTagRoutes);
        const chatbotAgentRoutes = @json($chatbotAgentRoutes);
    </script>
    <script>
        (() => {
            document.addEventListener('alpine:init', () => {
                Alpine.data('externalChatbotHistory', () => ({
                    filters: {
                        status: '{{ array_key_first($status_filters) }}',
                        agent: '{{ array_key_first($agent_filters) }}',
                        channel: '{{ array_key_first($channel_filters) }}',
                        sort: '{{ array_key_first($sort_filters) }}',
                        chatbot: '{{ array_key_first($conversation_agent_filters) }}',
                        unreadsOnly: false,
                        dateRange: {
                            start: null,
                            end: null,
                        },
                    },
                    open: false,
                    chatsList: [],
                    activeChat: null,
                    fetching: true,
                    activeSessionId: null,
                    currentPage: 1,
                    allLoaded: false,
                    /**
                     * @type {IntersectionObserver}
                     */
                    loadMoreIO: null,
                    originalLoadMoreHref: null,
                    mobileDropdownOpen: false,
                    messagesSearchFormVisible: false,
                    conversationsSearchFormVisible: false,
                    contactInfo: {
                        activeTab: 'details',
                        editMode: false,
                    },
                    mobile: {
                        filtersVisible: false,
                        contactInfoVisible: false,
                    },
                    userConversationHistory: [],
                    showExportOptions: false,
                    datepicker: null,
                    hasCustomerTags: @json($hasCustomerTagExtension),
                    customerTagModal: {
                        show: false,
                        loading: false,
                        saving: false,
                        creating: false,
                        items: [],
                        selected: [],
                        form: {
                            tag: '',
                            tag_color: '#6366f1',
                        },
                    },

                    async init() {
                        this.setActiveChat = this.setActiveChat.bind(this);

                        this.originalLoadMoreHref = this.$refs.loadMore.href;

                        await this.fetchChats({
                            loadMore: true
                        });

                        this.setupLoadMoreIO();

                        Alpine.store('externalChatbotHistory', this);

                        this.initDateRangePicker();
                    },
                    async loadMore() {
                        if (this.fetching || this.allLoaded) return;

                        await this.fetchChats({
                            loadMore: true
                        });
                    },
                    setupLoadMoreIO() {
                        this.loadMoreIO = new IntersectionObserver(async ([entry], observer) => {
                            if (entry.isIntersecting && !this.fetching && !this.allLoaded) {
                                await this.loadMore();
                            }
                        });

                        this.loadMoreIO.observe(this.$refs.loadMoreWrap);
                    },
                    async setOpen(open) {
                        if (this.open === open) return;

                        const topNoticeBar = document.querySelector('.top-notice-bar');
                        const navbar = document.querySelector('.lqd-navbar');
                        const pageContentWrap = document.querySelector('.lqd-page-content-wrap');
                        const navbarExpander = document.querySelector('.lqd-navbar-expander');

                        this.open = open;

                        document.documentElement.style.overflow = this.open ? 'hidden' : '';

                        if (navbar) {
                            navbar.style.position = this.open ? 'fixed' : '';
                        }

                        if (pageContentWrap && navbar?.offsetWidth > 0) {
                            pageContentWrap.style.paddingInlineStart = this.open ? 'var(--navbar-width)' : '';
                        }

                        if (topNoticeBar) {
                            topNoticeBar.style.visibility = this.open ? 'hidden' : '';
                        }

                        if (navbarExpander) {
                            navbarExpander.style.visibility = this.open ? 'hidden' : '';
                            navbarExpander.style.opacity = this.open ? 0 : 1;
                        }
                    },
                    async fetchChats(opts = {}) {
                        const options = {
                            loadMore: false,
                            ...opts
                        };

                        if (!options.loadMore) {
                            this.$refs.loadMore.href = this.originalLoadMoreHref;
                        }

                        this.fetching = true;

                        let url =
                            `${this.$refs.loadMore.href}&agentFilter=${this.filters.agent}&chatbot_channel=${this.filters.channel}&chatbot_id=${this.filters.chatbot}&status=${this.filters.status}&unread=${this.filters.unreadsOnly}&sort=${this.filters.sort}`;

                        if (this.filters.dateRange.start && this.filters.dateRange.end) {
                            const formatLocalDate = (d) => {
                                if (d instanceof Date) {
                                    const year = d.getFullYear();
                                    const month = String(d.getMonth() + 1).padStart(2, '0');
                                    const day = String(d.getDate()).padStart(2, '0');
                                    return `${year}-${month}-${day}`;
                                }
                                return d;
                            };
                            const startDate = formatLocalDate(this.filters.dateRange.start);
                            const endDate = formatLocalDate(this.filters.dateRange.end);
                            url += `&start_date=${startDate}&end_date=${endDate}`;
                        }

                        const res = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();
                        const {
                            data: conversations
                        } = data;

                        if (!res.ok || !conversations) {
                            if (data.message) {
                                toastr.error(data.message);
                            }
                            return;
                        }

                        const normalizedConversations = Array.isArray(conversations) ?
                            conversations.map(c => this.normalizeConversationPayload(c)) : [];

                        if (!options.loadMore) {
                            this.chatsList = normalizedConversations;
                        } else {
                            this.chatsList.push(...normalizedConversations);
                        }

                        this.allLoaded = data.meta.current_page >= data.meta.last_page;

                        this.$refs.loadMore.href = data.links.next ?? data.links.first;

                        if ((!options.loadMore || !this.activeChat) && this.chatsList.length) {
                            this.activeChat = this.chatsList[0];
                            this.syncCustomerTagSelection();
                        }

                        this.fetching = false;

                        this.scrollMessagesToBottom();
                    },

                    async filterAgent(agent) {
                        if (this.filters.agent === agent) return;

                        this.filters.agent = agent;

                        await this.fetchChats();
                    },

                    async filterChatbot(chatbotId) {
                        this.filters.chatbot = chatbotId;

                        await this.fetchChats();
                    },

                    async filterStatus(status) {
                        if (this.filters.status === status) return;

                        this.filters.status = status;

                        this.fetchChats();

                        toastr.success('{{ trans('Filter ticket status') }}')
                    },

                    async filterSort(sort) {
                        if (this.filters.sort === sort) return;

                        this.filters.sort = sort;

                        await this.fetchChats();
                    },

                    async filterUnread(event) {
                        const checkbox = event.target;
                        const unreadsOnly = checkbox.checked;

                        if (this.filters.unreadsOnly === unreadsOnly) return;

                        this.filters.unreadsOnly = unreadsOnly;

                        await this.fetchChats();
                    },


                    async handleConversationsSearch() {
                        const query = this.$refs.convSearchForm?.querySelector('input[name="conv_search"]')?.value?.trim();
                        this.fetching = true;

                        const res = await fetch('{{ route('dashboard.chatbot.conversations.search') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                            body: JSON.stringify({
                                search: query,
                            }),
                        });
                        const data = await res.json();
                        const {
                            data: conversations
                        } = data;

                        if (!res.ok || !conversations) {
                            if (data.message) {
                                toastr.error(data.message);
                            }
                            return;
                        }

                        const normalizedSearchConversations = Array.isArray(conversations) ?
                            conversations.map(c => this.normalizeConversationPayload(c)) : [];

                        this.chatsList = normalizedSearchConversations;
                        this.allLoaded = true;

                        this.activeChat = normalizedSearchConversations.at(0) ?? null;
                        this.syncCustomerTagSelection();

                        this.fetching = false;
                        this.scrollMessagesToBottom();
                    },

                    async setActiveChat(chatId) {
                        if (!chatId) return;

                        this.activeChat = this.chatsList.find(c => c.id === chatId);

                        this.mobile.filtersVisible = false;

                        this.syncCustomerTagSelection();
                        this.scrollMessagesToBottom();
                    },

                    getUnreadMessages(chatId) {
                        if (chatId == null) return;

                        let chat = this.chatsList.find(chat => chat.id === chatId);

                        if (!chat) return;

                        return chat?.histories?.filter(history => history.role == 'user' && history.read_at == null)?.length ?? 0;
                    },

                    getAllUnreadMessages() {
                        const unreadMessages = this.chatsList?.reduce((previousValue, chat) => {
                            return previousValue + (this.getUnreadMessages(chat.id) ?? 0);
                        }, 0);

                        return unreadMessages;
                    },

                    scrollMessagesToBottom() {
                        this.$nextTick(() => {
                            this.$refs.historyContentWrap.scrollTo({
                                top: this.$refs.historyContentWrap.scrollHeight,
                                behavior: 'smooth',
                            });
                        })
                    },

                    async handleMessagesSearch(inputElement) {
                        const searchString = inputElement.value;

                        if (!searchString.trim() && this.activeChat.originalHistories) {
                            if (this.activeChat) {
                                this.activeChat.histories = this.activeChat.originalHistories;
                            }
                            return;
                        }

                        if (this.activeChat.histories) {

                            if (!this.activeChat.originalHistories) {
                                this.activeChat.originalHistories = this.activeChat.histories;
                            }

                            const filteredHistories = this.activeChat.histories.filter(message =>
                                message.message && message.message.toLowerCase().includes(searchString.toLowerCase())
                            );

                            this.activeChat.histories = filteredHistories;
                        }
                    },

                    getFormattedString(string, options = {}) {
                        if (!('markdownit' in window) || !string) return '';

                        if (options.isHTML) {
                            return string;
                        }

                        string
                            .replace(/>(\s*\r?\n\s*)</g, '><')
                            .replace(/\n(?!.*\n)/, '');

                        const renderer = window.markdownit({
                            breaks: true,
                            highlight: (str, lang) => {
                                const language = lang && lang !== '' ? lang : 'md';
                                // const codeString = str.replace(/&/g, '&amp;').replace(/</g, '&lt;');
                                const codeString = str;

                                if (Prism.languages[language]) {
                                    const highlighted = Prism.highlight(codeString, Prism.languages[language], language);
                                    return `<pre class="language-${language}"><code data-lang="${language}" class="language-${language}">${highlighted}</code></pre>`;
                                }

                                return codeString;
                            }
                        });

                        renderer.use(function(md) {
                            md.core.ruler.after('inline', 'convert_links', function(state) {
                                state.tokens.forEach(function(blockToken) {
                                    if (blockToken.type !== 'inline') return;
                                    blockToken.children.forEach(function(token, idx) {
                                        if (token.content.includes('<a ')) {
                                            const linkRegex = /(.*)(<a\s+href="([^"]+)"[^>]*>([^<]+)<\/a>)(.*)/;
                                            const linkMatch = token.content.match(linkRegex);

                                            if (linkMatch) {
                                                const [, before, , href, text, after] = linkMatch;

                                                const beforeToken = new state.Token('text', '', 0);
                                                beforeToken.content = before;

                                                const newToken = new state.Token('link_open', 'a', 1);
                                                newToken.attrs = [
                                                    ['href', href]
                                                ];
                                                const textToken = new state.Token('text', '', 0);
                                                textToken.content = text;
                                                const closingToken = new state.Token('link_close', 'a', -1);

                                                const afterToken = new state.Token('text', '', 0);
                                                afterToken.content = after;

                                                blockToken.children.splice(idx, 1, beforeToken, newToken, textToken,
                                                    closingToken, afterToken);
                                            }
                                        }
                                    });
                                });
                            });
                        });

                        return renderer.render(renderer.utils.unescapeAll(string));
                    },

                    getDiffHumanTime(time) {
                        const diff = Math.floor((new Date() - new Date(time)) / 1000);

                        return diff < 60 ? " {{ __('Just now') }}" :
                            diff < 3600 ? (Math.floor(diff / 60) === 1 ?
                                "1 {{ __('minute ago') }}" : Math.floor(diff / 60) +
                                " {{ __('minutes ago') }}") :
                            diff < 86400 ? (Math.floor(diff / 3600) === 1 ?
                                "1 {{ __('hour ago') }}" : Math.floor(diff / 3600) +
                                " {{ __('hours ago') }}") :
                            Math.floor(diff / 86400) === 1 ? "1 {{ __('day ago') }}" : Math.floor(
                                diff / 86400) + " {{ __('days ago') }}"
                    },

                    getShortDiffHumanTime(time) {
                        const diff = Math.floor((new Date() - new Date(time)) / 1000);

                        return diff < 60 ? '{{ __('Just now') }}' :
                            diff < 3600 ? Math.floor(diff / 60) + '{{ __('m') }}' :
                            diff < 86400 ? Math.floor(diff / 3600) + '{{ __('h') }}' :
                            Math.floor(diff / 86400) + '{{ __('d') }}'
                    },

                    // Date Range
                    initDateRangePicker() {
                        this.$nextTick(() => {
                            if (!document.getElementById('conversationsDatepicker')) return;
                            this.datepicker = new AirDatepicker('#conversationsDatepicker', {
                                locale: defaultLocale,
                                inline: true,
                                range: true,
                                multipleDatesSeparator: ' - ',
                                dateFormat: 'yyyy-MM-dd',
                                onSelect: ({
                                    date
                                }) => {
                                    if (Array.isArray(date) && date.length === 2) {
                                        this.filters.dateRange.start = date[0];
                                        this.filters.dateRange.end = date[1];
                                    }
                                }
                            });
                        });
                    },

                    async applyDateRange() {
                        if (!this.filters.dateRange.start || !this.filters.dateRange.end) {
                            toastr.warning('{{ __('Please select a date range') }}');
                            return;
                        }
                        await this.fetchChats();
                        toastr.success('{{ __('Date filter applied') }}');
                    },

                    async clearDateRange() {
                        this.filters.dateRange.start = null;
                        this.filters.dateRange.end = null;
                        if (this.datepicker) this.datepicker.clear();
                        await this.fetchChats();
                        toastr.success('{{ __('Date filter cleared') }}');
                    },

                    // Conversation payload normalization
                    normalizeConversationPayload(conversation) {
                        if (!conversation) return conversation;
                        if (!Array.isArray(conversation.customer_tags)) {
                            conversation.customer_tags = [];
                        }
                        return conversation;
                    },

                    // Pin / Close / Delete
                    async pinConversation(conversationId) {
                        if (conversationId == null || !chatbotAgentRoutes.pin) return;

                        const res = await fetch(chatbotAgentRoutes.pin + '?conversation_id=' + conversationId, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();

                        if (data.status === 'success') {
                            const chat = this.chatsList.find(chat => chat.id == data.data.id);
                            if (!chat) return;
                            chat.pinned = data.data.pinned;

                            this.chatsList.sort((a, b) => {
                                if (a.pinned !== b.pinned) return b.pinned - a.pinned;
                                const aDate = new Date(a.updated_at || a.created_at);
                                const bDate = new Date(b.updated_at || b.created_at);
                                return this.filters.sort === 'newest' ? bDate - aDate : aDate - bDate;
                            });

                            toastr.success(data.message ?? '{{ __('Conversation pin status updated.') }}');
                        } else {
                            toastr.error(data.message || '{{ __('Failed to update pin status.') }}');
                        }
                    },

                    async closeConversation(conversationId) {
                        if (conversationId == null || !chatbotAgentRoutes.close) return;
                        if (!confirm('{{ __('Do you want to close this conversation?') }}')) return;

                        const res = await fetch(chatbotAgentRoutes.close + '?conversation_id=' + conversationId, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();

                        if (data.status === 'success') {
                            const chatIndex = this.chatsList.findIndex(chat => chat.id == this.activeChat.id);
                            if (chatIndex === -1) return;
                            this.chatsList.splice(chatIndex, 1);
                            this.activeChat = this.chatsList.at(Math.max(0, Math.min(this.chatsList.length - 1, chatIndex)));
                            this.syncCustomerTagSelection();
                            toastr.success('{{ __('Conversation closed successfully.') }}');
                        } else {
                            toastr.error(data.message || '{{ __('Failed to close conversation.') }}');
                        }
                    },

                    async deleteConversation(conversationId) {
                        if (conversationId == null || !chatbotAgentRoutes.delete) return;
                        if (!confirm('{{ __('Do you want to delete this conversation?') }}')) return;

                        const res = await fetch(chatbotAgentRoutes.delete, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                conversation_id: conversationId,
                            }),
                        });
                        const data = await res.json();

                        if (data.status == 'success') {
                            const chatIndex = this.chatsList.findIndex(chat => chat.id == this.activeChat.id);
                            if (chatIndex === -1) return;
                            this.chatsList.splice(chatIndex, 1);
                            this.activeChat = this.chatsList.at(Math.max(0, Math.min(this.chatsList.length - 1, chatIndex)));
                            this.syncCustomerTagSelection();
                        }
                    },

                    // Customer Tags
                    async toggleCustomTagModal() {
                        this.customerTagModal.show = !this.customerTagModal.show;
                        if (this.customerTagModal.show && !this.customerTagModal.items.length) {
                            this.$nextTick(() => this.loadCustomerTags());
                        }
                    },

                    syncCustomerTagSelection() {
                        if (!this.hasCustomerTags) return;
                        if (!this.activeChat || !Array.isArray(this.activeChat.customer_tags)) {
                            this.customerTagModal.selected = [];
                            return;
                        }
                        this.customerTagModal.selected = this.activeChat.customer_tags.map(tag => tag.id);
                    },

                    resetCustomerTagForm() {
                        this.customerTagModal.form = {
                            tag: '',
                            tag_color: '#6366f1',
                        };
                    },

                    async loadCustomerTags() {
                        if (!this.hasCustomerTags || !customerTagRoutes.index || !this.activeChat?.id) return;

                        this.customerTagModal.loading = true;
                        try {
                            const res = await fetch(`${customerTagRoutes.index}?conversation_id=${this.activeChat.id}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                if (data.message) toastr.error(data.message);
                                return;
                            }

                            this.customerTagModal.items = data.tags ?? [];
                            this.customerTagModal.selected = data.assigned ?? [];

                            if (!this.customerTagModal.items.length) {
                                this.resetCustomerTagForm();
                            }
                        } finally {
                            this.customerTagModal.loading = false;
                        }
                    },

                    async toggleCustomerTagSelection(tagId) {
                        if (!this.hasCustomerTags || !customerTagRoutes.sync || !this.activeChat?.id || this.customerTagModal.saving) return;

                        if (!this.customerTagModal.items.length) {
                            toastr.error('{{ __('Please create a tag first.') }}');
                            return;
                        }

                        const wasSelected = this.customerTagModal.selected.includes(tagId);
                        const previousSelection = [...this.customerTagModal.selected];

                        if (wasSelected) {
                            this.customerTagModal.selected = this.customerTagModal.selected.filter(id => id !== tagId);
                        } else {
                            this.customerTagModal.selected = [...this.customerTagModal.selected, tagId];
                        }

                        const success = await this.syncCustomerTags();
                        if (!success) this.customerTagModal.selected = previousSelection;
                    },

                    async syncCustomerTags() {
                        if (!this.hasCustomerTags || !customerTagRoutes.sync || !this.activeChat?.id) return false;

                        this.customerTagModal.saving = true;
                        try {
                            const res = await fetch(customerTagRoutes.sync, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    conversation_id: this.activeChat.id,
                                    tag_ids: this.customerTagModal.selected,
                                }),
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                if (data.message) toastr.error(data.message);
                                return false;
                            }

                            this.activeChat.customer_tags = data.data?.customer_tags ?? [];
                            const chatIndex = this.chatsList.findIndex(chat => chat.id == this.activeChat.id);
                            if (chatIndex !== -1) {
                                this.chatsList[chatIndex].customer_tags = this.activeChat.customer_tags;
                            }
                            this.syncCustomerTagSelection();
                            toastr.success(data.message ?? '{{ __('Customer tags updated.') }}');
                            return true;
                        } catch (error) {
                            toastr.error('{{ __('Unable to update tags right now.') }}');
                            return false;
                        } finally {
                            this.customerTagModal.saving = false;
                        }
                    },

                    async createCustomerTag() {
                        if (!this.hasCustomerTags || !customerTagRoutes.store) return;

                        const tagName = (this.customerTagModal.form.tag || '').trim();
                        if (!tagName) {
                            toastr.error('{{ __('Please enter a tag name.') }}');
                            return;
                        }

                        this.customerTagModal.creating = true;
                        try {
                            const res = await fetch(customerTagRoutes.store, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    tag: tagName,
                                    tag_color: this.customerTagModal.form.tag_color,
                                }),
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                if (data.message) toastr.error(data.message);
                                return;
                            }

                            toastr.success(data.message ?? '{{ __('Customer tag created.') }}');
                            this.customerTagModal.form.tag = '';
                            await this.loadCustomerTags();
                        } finally {
                            this.customerTagModal.creating = false;
                        }
                    },

                    // Export conversations
                    async exportConversations(format) {
                        if (!this.chatsList || this.chatsList.length === 0) {
                            toastr.warning('{{ __('No conversations to export') }}');
                            return;
                        }

                        try {
                            toastr.info('{{ __('Preparing export... Please wait') }}');
                            const exportData = this.prepareExportData();

                            switch (format) {
                                case 'csv':
                                    this.exportAsCSV(exportData);
                                    break;
                                case 'json':
                                    this.exportAsJSON(exportData);
                                    break;
                                case 'pdf':
                                    this.exportAsPDF(exportData);
                                    break;
                            }

                            toastr.success(`{{ __('Conversations exported as') }} ${format.toUpperCase()}`);
                        } catch (error) {
                            console.error('Export error:', error);
                            toastr.error('{{ __('Failed to export conversations') }}');
                        }
                    },

                    prepareExportData() {
                        return this.chatsList.map(chat => ({
                            id: chat.id,
                            customer_name: chat.conversation_name || 'N/A',
                            customer_email: chat.ip_address || 'N/A',
                            chatbot_name: chat.chatbot?.title || 'N/A',
                            channel: chat.chatbot_channel || 'N/A',
                            status: chat.ticket_status || 'N/A',
                            created_at: chat.created_at || 'N/A',
                            updated_at: chat.updated_at || 'N/A',
                            messages: (chat.histories || []).map(msg => ({
                                id: msg.id,
                                role: msg.role || 'N/A',
                                message: msg.message || '',
                                created_at: msg.created_at || 'N/A',
                                user_name: msg.role === 'user' ? (chat.conversation_name || 'User') : 'AI',
                            }))
                        }));
                    },

                    exportAsCSV(data) {
                        const headers = ['Conversation ID', 'Customer Name', 'IP Address', 'Chatbot', 'Channel', 'Status', 'Message ID', 'Role',
                            'User Name', 'Message', 'Message Created At', 'Conversation Created At'
                        ];
                        const csvRows = [headers.join(',')];

                        data.forEach(conv => {
                            if (conv.messages && conv.messages.length > 0) {
                                conv.messages.forEach(msg => {
                                    const values = [
                                        conv.id,
                                        `"${(conv.customer_name || '').replace(/"/g, '""')}"`,
                                        `"${(conv.customer_email || '').replace(/"/g, '""')}"`,
                                        `"${(conv.chatbot_name || '').replace(/"/g, '""')}"`,
                                        `"${(conv.channel || '').replace(/"/g, '""')}"`,
                                        `"${(conv.status || '').replace(/"/g, '""')}"`,
                                        msg.id,
                                        `"${(msg.role || '').replace(/"/g, '""')}"`,
                                        `"${(msg.user_name || '').replace(/"/g, '""')}"`,
                                        `"${(msg.message || '').replace(/"/g, '""').replace(/\n/g, ' ')}"`,
                                        `"${msg.created_at}"`,
                                        `"${conv.created_at}"`
                                    ];
                                    csvRows.push(values.join(','));
                                });
                            } else {
                                const values = [
                                    conv.id,
                                    `"${(conv.customer_name || '').replace(/"/g, '""')}"`,
                                    `"${(conv.customer_email || '').replace(/"/g, '""')}"`,
                                    `"${(conv.chatbot_name || '').replace(/"/g, '""')}"`,
                                    `"${(conv.channel || '').replace(/"/g, '""')}"`,
                                    `"${(conv.status || '').replace(/"/g, '""')}"`,
                                    'N/A', 'N/A', 'N/A', 'No messages', 'N/A',
                                    `"${conv.created_at}"`
                                ];
                                csvRows.push(values.join(','));
                            }
                        });

                        const csvString = csvRows.join('\n');
                        const blob = new Blob([csvString], {
                            type: 'text/csv;charset=utf-8;'
                        });
                        this.downloadFile(blob, `conversations_${this.getTimestamp()}.csv`);
                    },

                    exportAsJSON(data) {
                        const jsonString = JSON.stringify(data, null, 2);
                        const blob = new Blob([jsonString], {
                            type: 'application/json'
                        });
                        this.downloadFile(blob, `conversations_${this.getTimestamp()}.json`);
                    },

                    exportAsPDF(data) {
                        const {
                            jsPDF
                        } = window.jspdf;
                        const doc = new jsPDF();

                        const pageWidth = doc.internal.pageSize.getWidth();
                        const margin = 10;
                        const maxWidth = pageWidth - (margin * 2);
                        let y = 20;

                        doc.setFontSize(16);
                        doc.text('Conversations Export', margin, y);
                        y += 10;

                        doc.setFontSize(10);
                        doc.text(`Export Date: ${new Date().toLocaleString()}`, margin, y);
                        y += 7;
                        doc.text(`Total Conversations: ${data.length}`, margin, y);
                        y += 10;

                        doc.setFontSize(8);
                        data.forEach((conv, convIndex) => {
                            if (y > 270) {
                                doc.addPage();
                                y = 20;
                            }

                            doc.setFontSize(10);
                            doc.setFont(undefined, 'bold');
                            doc.text(`Conversation #${conv.id} - ${conv.customer_name}`, margin, y);
                            y += 5;

                            doc.setFontSize(8);
                            doc.setFont(undefined, 'normal');
                            doc.text(`IP: ${conv.customer_email} | Chatbot: ${conv.chatbot_name}`, margin + 3, y);
                            y += 4;
                            doc.text(`Channel: ${conv.channel} | Status: ${conv.status} | Created: ${conv.created_at}`, margin + 3, y);
                            y += 6;

                            if (conv.messages && conv.messages.length > 0) {
                                doc.setFontSize(7);
                                conv.messages.forEach(msg => {
                                    if (y > 275) {
                                        doc.addPage();
                                        y = 20;
                                    }

                                    doc.setFont(undefined, 'bold');
                                    doc.text(`[${msg.role.toUpperCase()}] ${msg.user_name} - ${msg.created_at}`, margin + 5, y);
                                    y += 4;

                                    doc.setFont(undefined, 'normal');
                                    const lines = doc.splitTextToSize(msg.message || 'No message content', maxWidth - 10);
                                    lines.forEach(line => {
                                        if (y > 280) {
                                            doc.addPage();
                                            y = 20;
                                        }
                                        doc.text(line, margin + 7, y);
                                        y += 3.5;
                                    });
                                    y += 2;
                                });
                            } else {
                                doc.setFont(undefined, 'italic');
                                doc.text('No messages in this conversation', margin + 5, y);
                                y += 4;
                            }

                            y += 5;

                            if (convIndex < data.length - 1) {
                                if (y > 275) {
                                    doc.addPage();
                                    y = 20;
                                }
                                doc.setDrawColor(200, 200, 200);
                                doc.line(margin, y, pageWidth - margin, y);
                                y += 5;
                            }
                        });

                        doc.save(`conversations_${this.getTimestamp()}.pdf`);
                    },

                    downloadFile(blob, filename) {
                        const link = document.createElement('a');
                        const url = URL.createObjectURL(blob);
                        link.setAttribute('href', url);
                        link.setAttribute('download', filename);
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                    },

                    getTimestamp() {
                        const now = new Date();
                        return now.getFullYear() +
                            String(now.getMonth() + 1).padStart(2, '0') +
                            String(now.getDate()).padStart(2, '0') + '_' +
                            String(now.getHours()).padStart(2, '0') +
                            String(now.getMinutes()).padStart(2, '0') +
                            String(now.getSeconds()).padStart(2, '0');
                    },

                    // Stub functions to avoid errors
                    productSelectVariant() {},
                    productAddToCart() {},
                    updatingCart: false,
                }));
            });
        })();
    </script>
@endpush
