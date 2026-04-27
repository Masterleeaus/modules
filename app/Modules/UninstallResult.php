<?php

namespace App\Modules;

class UninstallResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $moduleId,
        public readonly string $message = '',
    ) {}

    public static function ok(string $moduleId): self
    {
        return new self(true, $moduleId, "Module {$moduleId} marked as uninstalled.");
    }

    public static function fail(string $moduleId, string $message): self
    {
        return new self(false, $moduleId, $message);
    }
}
