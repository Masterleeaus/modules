<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;
use Illuminate\Support\Facades\Artisan;

abstract class BaseHook implements HookInterface
{
    /**
     * Resolve the absolute path to the module folder.
     */
    protected function modulePath(string $moduleId, string $suffix = ''): string
    {
        $base = base_path("Modules/{$moduleId}");

        return $suffix ? rtrim($base, '/') . '/' . ltrim($suffix, '/') : $base;
    }

    /**
     * Return all candidate migration directories for a module (supports both
     * PascalCase and lowercase conventions used across the codebase).
     *
     * @return string[]
     */
    protected function migrationPaths(string $moduleId): array
    {
        $paths = [
            $this->modulePath($moduleId, 'Database/Migrations'),
            $this->modulePath($moduleId, 'database/migrations'),
        ];

        return array_filter($paths, 'is_dir');
    }

    /**
     * Run an Artisan command quietly and return its exit code.
     */
    protected function artisan(string $command, array $params = []): int
    {
        return Artisan::call($command, $params);
    }
}
