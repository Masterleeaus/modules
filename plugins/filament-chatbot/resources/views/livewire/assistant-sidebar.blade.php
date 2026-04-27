<div
    x-data="{
        open: @entangle('open'),
        width: {{ $sidebarConfig['width'] ?? 400 }},
    }"
    class="chatbot-assistant-wrapper"
    x-on:assistant-context.window="$wire.call('captureContext', $event.detail)"
>
    {{-- marked.js for markdown rendering (loaded once, lazy) --}}
    <script>
        if (typeof window.__assistantMarkedLoaded === 'undefined') {
            window.__assistantMarkedLoaded = true;
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/marked@13/marked.min.js';
            s.onload = () => {
                marked.setOptions({ breaks: true, gfm: true });
                document.dispatchEvent(new Event('assistant:marked-ready'));
            };
            document.head.appendChild(s);
        }

        function assistantParseMd(text) {
            if (typeof marked === 'undefined' || !text) return (text || '').replace(/\n/g, '<br>');
            return marked.parse(text);
        }
    </script>

    {{-- Floating toggle button --}}
    @if(($buttonConfig['show'] ?? true))
    <button
        x-on:click="open = !open"
        title="{{ $buttonConfig['label'] ?? 'Ask Assistant' }}"
        class="chatbot-assistant-fab fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 dark:bg-primary-500 dark:hover:bg-primary-400"
    >
        @if($buttonConfig['icon'] ?? false)
            <x-dynamic-component :component="$buttonConfig['icon']" class="h-6 w-6" />
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 16a2 2 0 01-2 2H7l-4 4V6a2 2 0 012-2h14a2 2 0 012 2v10z" />
            </svg>
        @endif
    </button>
    @endif

    {{-- Sidebar panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        :style="'width: ' + width + 'px'"
        class="chatbot-assistant-panel fixed right-0 top-0 z-40 flex h-full flex-col border-l border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900"
        @keydown.escape.window="open = false"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $assistants[$assistantKey]['name'] ?? 'Assistant' }}
                </span>
            </div>

            <div class="flex items-center gap-2">
                {{-- Assistant switcher --}}
                @if(count($assistants) > 1)
                <select
                    wire:change="switchAssistant($event.target.value)"
                    class="rounded border border-gray-300 bg-white px-2 py-1 text-xs text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300"
                >
                    @foreach($assistants as $key => $config)
                        <option value="{{ $key }}" @selected($assistantKey === $key)>
                            {{ $config['name'] ?? $key }}
                        </option>
                    @endforeach
                </select>
                @endif

                {{-- Clear history --}}
                <button
                    wire:click="clearHistory"
                    wire:confirm="Clear all conversation history?"
                    title="Clear history"
                    class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>

                {{-- Close --}}
                <button
                    x-on:click="open = false"
                    class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Message list — poll is a fallback for when Reverb is unavailable --}}
        <div
            class="flex-1 overflow-y-auto px-4 py-3 space-y-3"
            @if($processing) wire:poll.3s="refreshRuns" @endif
            x-ref="messageList"
            x-init="$watch('$wire.messages', () => { $nextTick(() => { $el.scrollTop = $el.scrollHeight }) })"
        >
            @forelse($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-3 py-2 text-sm
                        {{ $msg['role'] === 'user'
                            ? 'bg-primary-600 text-white dark:bg-primary-500'
                            : 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100' }}
                        {{ ($msg['status'] ?? '') === 'failed' ? 'border border-red-400 bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300' : '' }}
                    ">
                        @if(($msg['status'] ?? '') === 'pending')
                            {{-- Loading dots while processing --}}
                            <div class="flex items-center gap-1 py-1">
                                <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.3s]"></span>
                                <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.15s]"></span>
                                <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400"></span>
                            </div>
                        @elseif($msg['role'] === 'assistant' && ($msg['status'] ?? '') !== 'failed')
                            {{-- Render assistant output as markdown --}}
                            <div
                                class="prose prose-sm dark:prose-invert max-w-none break-words"
                                x-data="{ raw: @js($msg['content'] ?? '') }"
                                x-html="assistantParseMd(raw)"
                            ></div>
                        @else
                            <p class="whitespace-pre-wrap break-words">{{ $msg['content'] ?? '' }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex h-full flex-col items-center justify-center text-center text-gray-400 dark:text-gray-600 py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 h-12 w-12 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M21 16a2 2 0 01-2 2H7l-4 4V6a2 2 0 012-2h14a2 2 0 012 2v10z" />
                    </svg>
                    <p class="text-sm">
                        Start a conversation with<br>
                        <strong class="text-gray-600 dark:text-gray-400">{{ $assistants[$assistantKey]['name'] ?? 'your Assistant' }}</strong>
                    </p>
                    @if($assistants[$assistantKey]['description'] ?? false)
                        <p class="mt-1 text-xs opacity-70">{{ $assistants[$assistantKey]['description'] }}</p>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Input area --}}
        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            <form wire:submit.prevent="send" class="flex items-end gap-2">
                <textarea
                    wire:model.defer="message"
                    rows="1"
                    placeholder="Ask a question…"
                    @keydown.enter.exact.prevent="if(!$event.shiftKey){ $wire.call('send') }"
                    :disabled="{{ json_encode($processing) }}"
                    class="block flex-1 resize-none rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-500"
                    style="max-height: 120px; overflow-y: auto;"
                ></textarea>
                <button
                    type="submit"
                    :disabled="{{ json_encode($processing) }}"
                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-primary-600 text-white hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-primary-500 dark:hover:bg-primary-400"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                    </svg>
                </button>
            </form>
            <p class="mt-1 text-center text-xs text-gray-400 dark:text-gray-600">
                Press Enter to send · Shift+Enter for newline
            </p>
        </div>
    </div>

    {{-- Backdrop overlay (mobile) --}}
    <div
        x-show="open"
        x-on:click="open = false"
        x-transition.opacity
        class="fixed inset-0 z-30 bg-black/30 sm:hidden"
    ></div>
</div>

