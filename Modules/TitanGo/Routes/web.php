<?php

use Illuminate\Support\Facades\Route;
use Modules\TitanGo\Http\Controllers\TechnicianJobController;

Route::middleware(['auth', 'role:technician'])
    ->prefix('technician')
    ->name('technician.')
    ->group(function () {
        Route::get('/dashboard', [TechnicianJobController::class, 'dashboard'])->name('dashboard');
        Route::get('/jobs', [TechnicianJobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{job}', [TechnicianJobController::class, 'show'])->name('jobs.show');
    });
