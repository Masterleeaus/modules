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
    | Assistants
    |--------------------------------------------------------------------------
    |
    | Each assistant is identified by a unique key. Configure the LLM model,
    | instruction/system prompt, and which tools the assistant may call.
    |
    */
    'assistants' => [
        'default' => [
            'name'        => 'Assistant',
            'description' => 'Your helpful admin assistant.',
            'instruction' => 'You are a helpful assistant for the admin panel. Answer questions concisely and accurately. When asked about data, say you can look it up if the appropriate tool is available.',
            'llm_connection' => 'openai',
            'model'       => 'gpt-4o',
            'tools'       => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | LLM Connections
    |--------------------------------------------------------------------------
    |
    | Define one or more LLM provider connections. Each entry needs a base URL
    | and an API key. The "openai" entry is used by default but you can point
    | it at any OpenAI-compatible endpoint (e.g. Azure OpenAI, local Ollama).
    |
    */
    'llm_connections' => [
        'openai' => [
            'url'     => 'https://api.openai.com/v1/',
            'api_key' => env('OPEN_AI_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tools (Open Functions)
    |--------------------------------------------------------------------------
    |
    | Each tool is identified by a key that matches entries in the "tools"
    | array inside an assistant config. The "tool" callable must return an
    | object with a generateFunctionDefinitions() method and handler methods
    | corresponding to each function name.
    |
    | Example:
    |
    |  'weather' => [
    |      'namespace'   => 'weather',
    |      'description' => 'Get weather information.',
    |      'tool'        => fn () => new \App\Assistant\Tools\WeatherTool(),
    |  ],
    |
    */
    'tools' => [
        // Add your custom tools here
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
        'enabled'          => true,
        'open_by_default'  => false,
        'width'            => 400,
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
        'show'    => true,
        'label'   => 'Ask Assistant',
        'icon'    => 'heroicon-o-chat-bubble-bottom-center-text',
    ],

];
