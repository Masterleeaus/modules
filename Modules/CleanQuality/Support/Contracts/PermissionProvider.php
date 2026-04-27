<?php

namespace Modules\CleanQuality\Support\Contracts;

interface PermissionProvider
{
    /**
     * Map a current permission key to a backward-compatible legacy permission key.
     */
    public static function legacyFor(string $permission): ?string;
}
