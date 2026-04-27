<?php

namespace App\Modules;

class InstallResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $moduleId,
        public readonly string $message = '',
        public readonly array $hookResults = [],
        public readonly ?string $failedHook = null,
    ) {}

    public static function ok(string $moduleId, array $hookResults = []): self
    {
        return new self(true, $moduleId, 'Module installed successfully.', $hookResults);
    }

    public static function fail(string $moduleId, string $message, string $failedHook = '', array $hookResults = []): self
    {
        return new self(false, $moduleId, $message, $hookResults, $failedHook ?: null);
    }

    public static function alreadyInstalled(string $moduleId): self
    {
        return new self(true, $moduleId, 'Module is already installed. No changes made.');
    }
}
