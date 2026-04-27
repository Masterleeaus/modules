<?php

namespace Modules\SupplyChain\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('account/supply-chain')
            ->name('supplychain.')
            ->group(module_path('SupplyChain', '/Routes/web.php'));

        Route::middleware(['api', 'auth:sanctum'])
            ->prefix('api/supply-chain')
            ->name('supplychain.api.')
            ->group(module_path('SupplyChain', '/Routes/api.php'));
    }
}
