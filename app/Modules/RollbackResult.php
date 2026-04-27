<?php

namespace App\Modules;

class RollbackResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $moduleId,
        public readonly string $toVersion,
        public readonly string $message = '',
    ) {}

    public static function ok(string $moduleId, string $toVersion): self
    {
        return new self(true, $moduleId, $toVersion, "Module {$moduleId} rolled back to {$toVersion}.");
    }

    public static function fail(string $moduleId, string $toVersion, string $message): self
    {
        return new self(false, $moduleId, $toVersion, $message);
    }
}
