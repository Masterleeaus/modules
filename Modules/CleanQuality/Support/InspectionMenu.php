<?php

namespace Modules\CleanQuality\Support;

use Illuminate\Support\Facades\Route;
use Modules\CleanQuality\Support\Contracts\MenuProvider;

final class InspectionMenu implements MenuProvider
{
    public static function url(string $routeName): ?string
    {
        return Route::has($routeName) ? route($routeName) : null;
    }
}
