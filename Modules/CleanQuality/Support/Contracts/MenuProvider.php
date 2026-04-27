<?php

namespace Modules\CleanQuality\Support\Contracts;

interface MenuProvider
{
    /**
     * Resolve a route name into a URL, or null when the route is unavailable.
     */
    public static function url(string $routeName): ?string;
}
