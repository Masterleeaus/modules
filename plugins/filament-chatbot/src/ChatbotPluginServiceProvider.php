<?php

namespace TitanZero\FilamentChatbot;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotResource;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotConversationResource;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotChannelResource;
use TitanZero\FilamentChatbot\Filament\Resources\ChatbotCustomerResource;

class ChatbotPluginServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-chatbot';

    public static string $viewNamespace = 'filament-chatbot';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile('chatbot')
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
            ])
            ->publishesAssets();
    }

    public function packageRegistered(): void
    {
        // Register services
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
    }

    public function packageBooted(): void
    {
        // Register assets
        FilamentAsset::register([
            Css::make('chatbot-styles', __DIR__ . '/../resources/css/chatbot.css'),
            Js::make('chatbot-scripts', __DIR__ . '/../resources/js/chatbot.js'),
        ], package: 'titanzero/filament-chatbot-plugin');

        // Register Filament plugin with dashboard
        if (class_exists(\Filament\Facades\Filament::class)) {
            \Filament\Facades\Filament::registerPlugin(
                \TitanZero\FilamentChatbot\Filament\ChatbotPlugin::make()
            );
        }

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/chatbot.php' => config_path('chatbot.php'),
        ], 'chatbot-config');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/chatbot-plugin'),
        ], 'chatbot-assets');
    }
}
