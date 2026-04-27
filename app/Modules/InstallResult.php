<?php

namespace App\Modules;

class InstallResult
{
    private bool $alreadyInstalled = false;

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
        // Treat empty string the same as "no failed hook".
        return new self(false, $moduleId, $message, $hookResults, $failedHook !== '' ? $failedHook : null);
    }

    public static function alreadyInstalled(string $moduleId): self
    {
        $result = new self(true, $moduleId, 'Module is already installed. No changes made.');
        $result->alreadyInstalled = true;

        return $result;
    }

    public function isAlreadyInstalled(): bool
    {
        return $this->alreadyInstalled;
    }
}
