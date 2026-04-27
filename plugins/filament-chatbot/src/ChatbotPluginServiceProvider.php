<?php

namespace TitanZero\FilamentChatbot;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TitanZero\FilamentChatbot\Http\Livewire\AssistantSidebar;
use TitanZero\FilamentChatbot\Services\AssistantService;
use TitanZero\FilamentChatbot\Services\RunProcessorService;

class ChatbotPluginServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-chatbot';

    public static string $viewNamespace = 'filament-chatbot';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile(['chatbot', 'assistant'])
            ->hasViews()
            ->hasMigrations([
                'create_chatbots_table',
                'create_chatbot_conversations_table',
                'create_chatbot_histories_table',
                'create_chatbot_channels_table',
                'create_chatbot_customers_table',
                'create_chatbot_embeddings_table',
                'create_chatbot_canned_responses_table',
                'create_chatbot_knowledge_base_articles_table',
                // Assistant Engine migrations
                '2026_04_27_000001_create_assistant_threads_table',
                '2026_04_27_000002_create_assistant_runs_table',
            ])
            ->hasAssets();
    }

    public function packageRegistered(): void
    {
        // Chatbot services
        $this->app->singleton('chatbot.service', function ($app) {
            return new \TitanZero\FilamentChatbot\Services\ChatbotService();
        });

        $this->app->singleton('chatbot.generator', function ($app) {
            return new \TitanZero\FilamentChatbot\Services\GeneratorService();
        });

        $this->app->singleton('chatbot.training', function ($app) {
            return new \TitanZero\FilamentChatbot\Services\TrainingService();
        });

        $this->app->singleton('chatbot.analytics', function ($app) {
            return new \TitanZero\FilamentChatbot\Services\ChatbotAnalyticsService();
        });

        // Assistant Engine services
        $this->app->singleton(AssistantService::class);
        $this->app->singleton(RunProcessorService::class);
    }

    public function packageBooted(): void
    {
        // Register assets
        FilamentAsset::register([
            Css::make('chatbot-styles', __DIR__ . '/../resources/css/chatbot.css'),
            Js::make('chatbot-scripts', __DIR__ . '/../resources/js/chatbot.js'),
        ], package: 'titanzero/filament-chatbot-plugin');

        // Register Livewire component for the AI assistant sidebar
        if (class_exists(Livewire::class)) {
            Livewire::component('chatbot-assistant-sidebar', AssistantSidebar::class);
        }

        // Publish chatbot config
        $this->publishes([
            __DIR__ . '/../config/chatbot.php' => config_path('chatbot.php'),
        ], 'chatbot-config');

        // Publish assistant config
        $this->publishes([
            __DIR__ . '/Config/assistant.php' => config_path('assistant.php'),
        ], 'filament-assistant-config');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/chatbot-plugin'),
        ], 'chatbot-assets');
    }
}
