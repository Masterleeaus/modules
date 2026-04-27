<?php

namespace TitanZero\FilamentChatbot\Tools;

/**
 * Base class for all assistant tools (Open Functions).
 *
 * Extend this class and implement:
 *   1. generateFunctionDefinitions() — return OpenAI-style function schemas.
 *   2. One public method per function, matching the name in the schema.
 *
 * Using a class instead of a closure allows config caching
 * (php artisan config:cache) because closures cannot be serialised.
 *
 * Register the tool in config/assistant.php as a callable:
 *
 *   'tools' => [
 *       'my_tool' => [
 *           'namespace'   => 'my_tool',
 *           'description' => 'Does something useful.',
 *           'tool'        => [\App\Assistant\Tools\MyTool::class, 'make'],
 *       ],
 *   ],
 *
 * Or using a static factory on the tool itself:
 *
 *   'tool' => [\App\Assistant\Tools\MyTool::class, 'make'],
 *
 * Example tool:
 *
 *   class SiteSummaryTool extends AbstractAssistantTool
 *   {
 *       public function generateFunctionDefinitions(): array
 *       {
 *           return [
 *               (new FunctionDefinition('getSiteSummary', 'Returns site statistics.'))
 *                   ->createFunctionDescription(),
 *           ];
 *       }
 *
 *       public function getSiteSummary(): TextResponseItem
 *       {
 *           return new TextResponseItem('Users: ' . User::count() . ', Orders: ' . Order::count());
 *       }
 *   }
 */
abstract class AbstractAssistantTool
{
    /**
     * Return OpenAI-style function definition schemas for this tool.
     *
     * Each item should be an array with at least:
     *   ['name' => '...', 'description' => '...', 'parameters' => [...]]
     *
     * @return array<int, array<string, mixed>>
     */
    abstract public function generateFunctionDefinitions(): array;

    /**
     * Convenience static factory so the tool can be registered as a callable:
     *   'tool' => [MyTool::class, 'make']
     */
    public static function make(): static
    {
        return app(static::class);
    }
}
