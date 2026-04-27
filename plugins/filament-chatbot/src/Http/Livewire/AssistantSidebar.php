<?php

namespace TitanZero\FilamentChatbot\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use TitanZero\FilamentChatbot\Jobs\ProcessAssistantRunJob;
use TitanZero\FilamentChatbot\Models\AssistantRun;
use TitanZero\FilamentChatbot\Models\AssistantThread;
use TitanZero\FilamentChatbot\Services\AssistantService;

class AssistantSidebar extends Component
{
    /** Whether the sidebar panel is open */
    public bool $open = false;

    /** The currently active assistant key */
    public string $assistantKey = '';

    /** The message currently being composed */
    public string $message = '';

    /** Ordered list of display messages [{role, content, status}] */
    public array $messages = [];

    /** The active thread ID */
    public ?int $threadId = null;

    /** Contextual data passed from the current page (set via Alpine/JS or a Filament page) */
    public array $pageContext = [];

    /** Whether the assistant is currently processing a run */
    public bool $processing = false;

    /** Authenticated user ID (used to scope the Echo channel) */
    public ?int $userId = null;

    public function mount(): void
    {
        $this->assistantKey = config('assistant.default_assistant', 'default');
        $this->open         = (bool) config('assistant.sidebar.open_by_default', false);
        $this->userId       = Auth::id();
        $this->loadThread();
    }

    public function loadThread(): void
    {
        if (! Auth::check()) {
            return;
        }

        $thread = AssistantThread::where('user_identifier', $this->userIdentifier())
            ->where('assistant_key', $this->assistantKey)
            ->first();

        if ($thread) {
            $this->threadId = $thread->id;
            $this->hydrateMessages($thread);
        }
    }

    public function switchAssistant(string $key): void
    {
        $this->assistantKey = $key;
        $this->messages     = [];
        $this->threadId     = null;
        $this->loadThread();
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function clearHistory(): void
    {
        if ($this->threadId) {
            AssistantThread::find($this->threadId)?->runs()->delete();
            AssistantThread::destroy($this->threadId);
            $this->threadId = null;
            $this->messages = [];
        }
    }

    /**
     * Receive context from the current Filament page.
     *
     * Call this from any Filament page component or Blade template:
     *   $wire('chatbot-assistant-sidebar').captureContext({ record: {...} })
     *
     * Or dispatch a browser event from a Filament page:
     *   dispatch('assistant-context', { resource: 'order', id: 42, data: {...} })
     *
     * @param  array<string, mixed>  $context
     */
    public function captureContext(array $context): void
    {
        $this->pageContext = $context;

        if ($this->threadId) {
            AssistantThread::find($this->threadId)?->update(['context' => $context]);
        }
    }

    public function send(): void
    {
        $input = trim($this->message);

        if ($input === '' || ! Auth::check()) {
            return;
        }

        // --- Upgrade 5: rate limiting ---
        $rateLimitKey = 'assistant.send.' . $this->userIdentifier();
        $maxPerMinute = (int) config('assistant.rate_limit.per_minute', 20);

        if (RateLimiter::tooManyAttempts($rateLimitKey, $maxPerMinute)) {
            $secondsUntilFree = RateLimiter::availableIn($rateLimitKey);
            $this->messages[] = [
                'role'    => 'assistant',
                'content' => "⚠ Too many requests. Please wait {$secondsUntilFree}s before sending another message.",
                'status'  => 'failed',
            ];

            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        $this->message    = '';
        $this->processing = true;

        // Optimistically add the user message to the UI
        $this->messages[] = ['role' => 'user', 'content' => $input, 'status' => 'sent'];

        /** @var AssistantService $service */
        $service = app(AssistantService::class);

        $thread = $service->resolveThread($this->userIdentifier(), $this->assistantKey);

        if ($this->pageContext) {
            $thread->update(['context' => $this->pageContext]);
        }

        $this->threadId = $thread->id;

        $run = $service->createRun($thread, $input);

        $queue = config('assistant.default_run_queue', 'default');

        ProcessAssistantRunJob::dispatch($run)->onQueue($queue);

        // Add a placeholder while the run processes
        $this->messages[] = [
            'role'    => 'assistant',
            'content' => '',
            'status'  => 'pending',
            'run_id'  => $run->id,
        ];
    }

    /**
     * Handle a real-time run-completion broadcast from Laravel Reverb.
     *
     * Registered dynamically in getListeners() on the private Echo channel
     * `assistant.user.{userId}`. The event name is AssistantRunCompleted.
     *
     * @param  array<string, mixed>  $event
     */
    public function handleRunCompleted(array $event): void
    {
        $runId = (int) ($event['run_id'] ?? 0);

        foreach ($this->messages as $idx => $msg) {
            if (($msg['run_id'] ?? 0) !== $runId) {
                continue;
            }

            if ($event['status'] === AssistantRun::STATUS_COMPLETED) {
                $this->messages[$idx]['content'] = $event['output'] ?? '';
                $this->messages[$idx]['status']  = AssistantRun::STATUS_COMPLETED;
            } else {
                $this->messages[$idx]['content'] = '⚠ ' . ($event['error'] ?? 'The assistant encountered an error.');
                $this->messages[$idx]['status']  = AssistantRun::STATUS_FAILED;
            }
        }

        $this->processing = collect($this->messages)->contains(fn ($m) => ($m['status'] ?? '') === 'pending');
    }

    /**
     * Poll-based fallback for when Reverb / WebSockets are unavailable.
     * The Blade template uses wire:poll only when no pending messages exist.
     */
    public function refreshRuns(): void
    {
        if (! $this->threadId) {
            return;
        }

        foreach ($this->messages as $idx => $msg) {
            if (($msg['status'] ?? '') !== 'pending' || empty($msg['run_id'])) {
                continue;
            }

            $run = AssistantRun::find($msg['run_id']);

            if (! $run) {
                continue;
            }

            if ($run->isCompleted()) {
                $this->messages[$idx]['content'] = $run->output ?? '';
                $this->messages[$idx]['status']  = 'completed';
            } elseif ($run->isFailed()) {
                $this->messages[$idx]['content'] = '⚠ ' . ($run->error ?? 'The assistant encountered an error.');
                $this->messages[$idx]['status']  = 'failed';
            }
        }

        $this->processing = collect($this->messages)->contains(fn ($m) => ($m['status'] ?? '') === 'pending');
    }

    public function render(): \Illuminate\View\View
    {
        $assistants = config('assistant.assistants', []);

        return view('filament-chatbot::livewire.assistant-sidebar', [
            'assistants'    => $assistants,
            'sidebarConfig' => config('assistant.sidebar', []),
            'buttonConfig'  => config('assistant.button', []),
        ]);
    }

    /**
     * Dynamic Livewire listeners — registers the private Echo channel using the
     * actual authenticated user ID, which is only known at runtime.
     *
     * When Reverb/Echo is active, `handleRunCompleted` fires instantly on run
     * completion. The poll in the Blade template is an independent fallback for
     * environments without WebSockets.
     *
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        $listeners = [];

        if ($this->userId) {
            $listeners["echo-private:assistant.user.{$this->userId},AssistantRunCompleted"] = 'handleRunCompleted';
        }

        return $listeners;
    }

    // -------------------------------------------------------------------------

    private function userIdentifier(): string
    {
        $user = Auth::user();

        if ($user) {
            return 'user:' . $user->getKey();
        }

        // Use session ID to isolate guest conversations — the @auth guard
        // in the Blade template prevents unauthenticated access, but this
        // ensures no cross-user data leakage if the guard is ever bypassed.
        return 'session:' . session()->getId();
    }

    private function hydrateMessages(AssistantThread $thread): void
    {
        $this->messages = [];

        foreach ($thread->completedAndFailedRuns()->get() as $run) {
            $this->messages[] = ['role' => 'user', 'content' => $run->input, 'status' => 'sent'];
            $this->messages[] = [
                'role'    => 'assistant',
                'content' => $run->isCompleted() ? ($run->output ?? '') : ('⚠ ' . $run->error),
                'status'  => $run->status,
                'run_id'  => $run->id,
            ];
        }
    }
}

