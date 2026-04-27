<?php

namespace App\Modules;

class HealthResult
{
    public function __construct(
        public readonly bool $healthy,
        public readonly string $moduleId,
        public readonly string $message = '',
        public readonly array $checks = [],
    ) {}

    public static function healthy(string $moduleId, array $checks = []): self
    {
        return new self(true, $moduleId, "Module {$moduleId} is healthy.", $checks);
    }

    public static function unhealthy(string $moduleId, string $message, array $checks = []): self
    {
        return new self(false, $moduleId, $message, $checks);
    }
}
