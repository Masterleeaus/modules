<?php

namespace App\Modules;

class UpgradeResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $moduleId,
        public readonly string $fromVersion,
        public readonly string $toVersion,
        public readonly string $message = '',
        public readonly array $hookResults = [],
    ) {}

    public static function ok(string $moduleId, string $from, string $to, array $hookResults = []): self
    {
        return new self(true, $moduleId, $from, $to, "Upgraded {$moduleId} from {$from} to {$to}.", $hookResults);
    }

    public static function fail(string $moduleId, string $from, string $to, string $message, array $hookResults = []): self
    {
        return new self(false, $moduleId, $from, $to, $message, $hookResults);
    }

    public static function noPathFound(string $moduleId, string $from, string $to): self
    {
        return new self(false, $moduleId, $from, $to, "No upgrade path found for {$moduleId} ({$from}→{$to}).");
    }
}
