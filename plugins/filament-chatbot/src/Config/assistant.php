<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Assistant
    |--------------------------------------------------------------------------
    |
    | The key of the default assistant to use when no specific assistant is
    | selected. Must correspond to a key in the "assistants" array below.
    |
    */
    'default_assistant' => env('DEFAULT_ASSISTANT_KEY', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Default Run Queue
    |--------------------------------------------------------------------------
    |
    | The queue name used when dispatching ProcessAssistantRunJob jobs. Set to
    | "sync" to process runs inline (useful for local development).
    |
    */
    'default_run_queue' => env('DEFAULT_RUN_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Token Budget (Context Window Guard)
    |--------------------------------------------------------------------------
    |
    | Maximum number of prior messages (user + assistant, counting each
    | separately) to include in each LLM request. Oldest messages are
    | removed first to stay within the configured limit.
    |
    | 0 means no limit (include full history).
    | A sensible default for GPT-4o (128k) is 40–80; for Claude 3 use 60–100.
    |
    | You can also override this per assistant using the "max_context_messages"
    | key on each assistant config entry.
    |
    */
    'max_context_messages' => (int) env('ASSISTANT_MAX_CONTEXT_MESSAGES', 0),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Controls how many messages a single user may send per minute.
    | Exceeding the limit returns an in-chat warning and does not dispatch
    | a queue job, protecting your LLM API quota.
    |
    */
    'rate_limit' => [
        'per_minute' => (int) env('ASSISTANT_RATE_LIMIT_PER_MINUTE', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Assistants
    |--------------------------------------------------------------------------
    |
    | Each assistant is identified by a unique key. Configure the LLM model,
    | instruction/system prompt, which tools the assistant may call, and
    | optionally override the context window budget for this assistant.
    |
    */
    'assistants' => [
        'default' => [
            'name'                 => 'Assistant',
            'description'         => 'Your helpful admin assistant.',
            'instruction'         => 'You are a helpful assistant for the admin panel. Answer questions concisely and accurately. When asked about data, say you can look it up if the appropriate tool is available.',
            'llm_connection'      => 'openai',
            'model'               => 'gpt-4o',
            'tools'               => [],
            // Optionally override the global max_context_messages for this assistant:
            // 'max_context_messages' => 40,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | LLM Connections
    |--------------------------------------------------------------------------
    |
    | Define one or more LLM provider connections. Each entry requires:
    |   - driver   : "openai" (default), "anthropic", "gemini", or a
    |                fully-qualified class name implementing LlmDriverContract.
    |   - url      : Base URL (override the driver's default if needed).
    |   - api_key  : API key / bearer token for the provider.
    |
    | Additional driver-specific keys (e.g. anthropic_version, max_tokens)
    | are passed through to the driver unchanged.
    |
    */
    'llm_connections' => [
        // ── OpenAI (or any OpenAI-compatible endpoint) ───────────────────────
        'openai' => [
            'driver'  => 'openai',
            'url'     => 'https://api.openai.com/v1/',
            'api_key' => env('OPEN_AI_KEY'),
        ],

        // ── Anthropic Claude ─────────────────────────────────────────────────
        // 'anthropic' => [
        //     'driver'             => 'anthropic',
        //     'url'                => 'https://api.anthropic.com',
        //     'api_key'            => env('ANTHROPIC_API_KEY'),
        //     'anthropic_version'  => '2023-06-01',
        //     'max_tokens'         => 4096,
        // ],

        // ── Google Gemini ─────────────────────────────────────────────────────
        // 'gemini' => [
        //     'driver'  => 'gemini',
        //     'api_key' => env('GEMINI_API_KEY'),
        // ],

        // ── Azure OpenAI ──────────────────────────────────────────────────────
        // 'azure' => [
        //     'driver'  => 'openai',
        //     'url'     => 'https://YOUR_RESOURCE.openai.azure.com/openai/deployments/YOUR_DEPLOYMENT/',
        //     'api_key' => env('AZURE_OPENAI_KEY'),
        // ],

        // ── Local Ollama ──────────────────────────────────────────────────────
        // 'ollama' => [
        //     'driver'  => 'openai',
        //     'url'     => 'http://localhost:11434/v1/',
        //     'api_key' => 'ollama',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tools (Open Functions)
    |--------------------------------------------------------------------------
    |
    | Each tool is identified by a key that matches entries in the "tools"
    | array inside an assistant config.
    |
    | The "tool" value must be a callable that returns an object extending
    | AbstractAssistantTool (which has a generateFunctionDefinitions() method
    | and one public method per function it exposes).
    |
    | Use the static callable form [ClassName::class, 'make'] instead of
    | closures so that config can be cached (php artisan config:cache).
    |
    | Example:
    |
    |  'site_stats' => [
    |      'namespace'   => 'site_stats',
    |      'description' => 'Returns key site statistics.',
    |      'tool'        => [\App\Assistant\Tools\SiteStatsTool::class, 'make'],
    |  ],
    |
    */
    'tools' => [
        // Add your tools here
    ],

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Controls whether the AI assistant sidebar is shown and its initial state.
    |
    */
    'sidebar' => [
        'enabled'         => true,
        'open_by_default' => false,
        'width'           => 400,
    ],

    /*
    |--------------------------------------------------------------------------
    | Button
    |--------------------------------------------------------------------------
    |
    | The floating action button that opens the assistant sidebar.
    |
    */
    'button' => [
        'show'  => true,
        'label' => 'Ask Assistant',
        'icon'  => 'heroicon-o-chat-bubble-bottom-center-text',
    ],

];
