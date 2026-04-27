<?php

use Illuminate\Support\Facades\Route;
use Modules\GroundZero\Http\Controllers\DispatchBoardController;

/*
|--------------------------------------------------------------------------
| GroundZero Web Routes
|--------------------------------------------------------------------------
|
| Dispatch board API endpoints consumed by the Vue SPA.  All routes require
| an authenticated session with the "owner" role.
|
*/

Route::middleware(['web', 'auth'])
    ->prefix('owner/ground-zero')
    ->name('ground-zero.')
    ->group(function () {
        Route::get('/board', [DispatchBoardController::class, 'board'])
            ->name('board');

        Route::post('/jobs/{job}/assign', [DispatchBoardController::class, 'assign'])
            ->name('jobs.assign');

        Route::get('/technicians/{user}/route/optimise', [DispatchBoardController::class, 'optimiseRoute'])
            ->name('technicians.route.optimise');
    });
