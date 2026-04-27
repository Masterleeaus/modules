<?php

namespace Modules\CleanQuality\Support\Contracts;

interface RouteNamer
{
    /**
     * Return all canonical route names as a flat list.
     *
     * @return array<int, string>
     */
    public static function all(): array;
}
