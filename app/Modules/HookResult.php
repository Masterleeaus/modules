<?php

namespace App\Modules;

class HookResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message = '',
        public readonly array $details = [],
    ) {}

    public static function ok(string $message = '', array $details = []): self
    {
        return new self(true, $message, $details);
    }

    public static function fail(string $message, array $details = []): self
    {
        return new self(false, $message, $details);
    }
}
